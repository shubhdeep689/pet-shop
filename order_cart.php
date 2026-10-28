<?php

session_start();

require_once "config/db.php";

/* =========================
   LOGIN CHECK
========================= */

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = (int) $_SESSION["user_id"];

/* =========================
   GET USER
========================= */

$stmtUser = $conn->prepare("
    SELECT name, email
    FROM users
    WHERE id = ?
");

$stmtUser->bind_param("i", $userId);
$stmtUser->execute();

$userResult = $stmtUser->get_result();

if ($userResult->num_rows === 0) {
    die("User not found.");
}

$user = $userResult->fetch_assoc();

$customerName = $user["name"];
$customerEmail = $user["email"];

/* =========================
   GET CART
========================= */

$stmtCart = $conn->prepare("
    SELECT
        ci.id,
        ci.pet_id,
        ci.product_id,
        ci.quantity,

        p.name AS pet_name,
        p.price AS pet_price,

        pr.name AS product_name,
        pr.price AS product_price

    FROM cart_items ci

    LEFT JOIN pets p
        ON ci.pet_id = p.id

    LEFT JOIN products pr
        ON ci.product_id = pr.id

    WHERE ci.user_id = ?

    ORDER BY ci.id ASC
");

$stmtCart->bind_param("i", $userId);
$stmtCart->execute();

$cartResult = $stmtCart->get_result();

if ($cartResult->num_rows === 0) {
    header("Location: cart.php");
    exit();
}

/* =========================
   PREPARE CART ITEMS
========================= */

$cartItems = [];
$totalAmount = 0;

while ($row = $cartResult->fetch_assoc()) {

    $quantity = (int)$row["quantity"];

    if (!empty($row["product_id"])) {

        $itemName = $row["product_name"];
        $price = (float)$row["product_price"];
        $itemType = "Product";

        $productId = (int)$row["product_id"];
        $petId = null;

    } else {

        $itemName = $row["pet_name"];
        $price = (float)$row["pet_price"];
        $itemType = "Pet";

        $petId = (int)$row["pet_id"];
        $productId = null;
    }

    $subtotal = $price * $quantity;

    $totalAmount += $subtotal;

    $cartItems[] = [
        "pet_id" => $petId,
        "product_id" => $productId,
        "item_name" => $itemName,
        "item_type" => $itemType,
        "quantity" => $quantity,
        "price" => $price,
        "subtotal" => $subtotal
    ];
}

/* =========================
   CREATE ORDER
========================= */

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        /* Start transaction */
        $conn->begin_transaction();

        /* =========================
           CHECK PRODUCT STOCK
        ========================= */

        foreach ($cartItems as $item) {

            if ($item["item_type"] !== "Product") {
                continue;
            }

            $productId = (int)$item["product_id"];
            $quantity = (int)$item["quantity"];

            $stmtStock = $conn->prepare("
                SELECT id, name, stock
                FROM products
                WHERE id = ?
                FOR UPDATE
            ");

            $stmtStock->bind_param("i", $productId);
            $stmtStock->execute();

            $stockResult = $stmtStock->get_result();

            if ($stockResult->num_rows === 0) {
                throw new Exception("Product is no longer available.");
            }

            $stockData = $stockResult->fetch_assoc();

            $currentStock = (int)$stockData["stock"];

            if ($currentStock < $quantity) {

                throw new Exception(
                    "Not enough stock for " .
                    $stockData["name"] .
                    ". Only " .
                    $currentStock .
                    " item(s) are available."
                );
            }
        }

        /* =========================
           INSERT ORDER
        ========================= */

        $status = "Pending";
        $whatsappSent = 0;

        $stmtOrder = $conn->prepare("
            INSERT INTO orders
            (
                user_id,
                customer_name,
                customer_email,
                total_amount,
                status,
                whatsapp_sent
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmtOrder->bind_param(
            "issdsi",
            $userId,
            $customerName,
            $customerEmail,
            $totalAmount,
            $status,
            $whatsappSent
        );

        if (!$stmtOrder->execute()) {
            throw new Exception("Unable to create order.");
        }

        $orderId = $conn->insert_id;

        /* =========================
           INSERT ORDER ITEMS
        ========================= */

        foreach ($cartItems as $item) {

            $petId = $item["pet_id"];
            $productId = $item["product_id"];
            $itemName = $item["item_name"];
            $itemType = $item["item_type"];
            $quantity = $item["quantity"];
            $price = $item["price"];
            $subtotal = $item["subtotal"];

            $stmtItem = $conn->prepare("
                INSERT INTO order_items
                (
                    order_id,
                    pet_id,
                    product_id,
                    item_name,
                    item_type,
                    quantity,
                    price,
                    subtotal
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmtItem->bind_param(
                "iiissidd",
                $orderId,
                $petId,
                $productId,
                $itemName,
                $itemType,
                $quantity,
                $price,
                $subtotal
            );

            if (!$stmtItem->execute()) {
                throw new Exception("Unable to save order items.");
            }
        }

        /* =========================
           REDUCE PRODUCT STOCK
        ========================= */

/* =========================
   REDUCE PRODUCT AND PET STOCK
========================= */

foreach ($cartItems as $item) {

    $quantity = (int) $item["quantity"];

    // =========================
    // REDUCE PRODUCT STOCK
    // =========================

    if ($item["item_type"] === "Product") {

        $productId = (int) $item["product_id"];

        $stmtUpdate = $conn->prepare("
            UPDATE products
            SET stock = stock - ?
            WHERE id = ?
            AND stock >= ?
        ");

        $stmtUpdate->bind_param(
            "iii",
            $quantity,
            $productId,
            $quantity
        );

        if (!$stmtUpdate->execute()) {
            throw new Exception(
                "Unable to update product stock."
            );
        }

        if ($stmtUpdate->affected_rows !== 1) {
            throw new Exception(
                "Product stock changed while placing the order. Please try again."
            );
        }
    }

    // =========================
    // REDUCE PET STOCK
    // =========================

    elseif ($item["item_type"] === "Pet") {

        $petId = (int) $item["pet_id"];

        $stmtUpdate = $conn->prepare("
            UPDATE pets
            SET
                quantity = quantity - ?,
                availability = CASE
                    WHEN quantity - ? = 0 THEN 'Sold'
                    ELSE availability
                END
            WHERE id = ?
              AND quantity >= ?
              AND availability = 'Available'
        ");

        $stmtUpdate->bind_param(
            "iiii",
            $quantity,
            $quantity,
            $petId,
            $quantity
        );

        if (!$stmtUpdate->execute()) {
            throw new Exception(
                "Unable to update pet stock."
            );
        }

        if ($stmtUpdate->affected_rows !== 1) {
            throw new Exception(
                "Pet stock changed while placing the order. Please try again."
            );
        }
    }
}


        /* =========================
           CLEAR CART
        ========================= */

        $stmtClear = $conn->prepare("
            DELETE FROM cart_items
            WHERE user_id = ?
        ");

        $stmtClear->bind_param("i", $userId);

        if (!$stmtClear->execute()) {
            throw new Exception("Unable to clear cart.");
        }

        /* =========================
           COMMIT
        ========================= */

        $conn->commit();

        /* =========================
           WHATSAPP
        ========================= */

        $shopWhatsAppNumber = "+919779654617";

        $message = "Hello Brar Pet Shop,\n\n";
        $message .= "New Order #" . $orderId . "\n";
        $message .= "Customer: " . $customerName . "\n";
        $message .= "Email: " . $customerEmail . "\n\n";

        $message .= "Order Items:\n";

        foreach ($cartItems as $item) {

            $message .=
                $item["item_name"] .
                " x " .
                $item["quantity"] .
                " - Rs. " .
                number_format($item["subtotal"], 2) .
                "\n";
        }

        $message .=
            "\nTotal: Rs. " .
            number_format($totalAmount, 2);

        $whatsappUrl =
            "https://wa.me/" .
            $shopWhatsAppNumber .
            "?text=" .
            urlencode($message);

        ?>


    <!DOCTYPE html>

    <html lang="en">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0">

        <title>
            Order Created - Brar Pet Shop
        </title>

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet">

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
            rel="stylesheet">

        <style>

            body {
                background: #f8f9fa;
            }

            .success-card {
                max-width: 650px;
                margin: 80px auto;
                border: none;
                border-radius: 20px;
                box-shadow: 0 10px 35px rgba(0,0,0,0.08);
            }

            .success-icon {
                width: 80px;
                height: 80px;
                margin: auto;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #d1e7dd;
                color: #198754;
                font-size: 40px;
            }

        </style>

    </head>

    <body>

    <div class="container">

        <div class="card success-card">

            <div class="card-body p-5 text-center">

                <div class="success-icon mb-4">
                    <i class="bi bi-check-lg"></i>
                </div>

                <h2 class="fw-bold">
                    Order Created Successfully!
                </h2>

                <p class="text-muted">
                    Your order has been saved successfully.
                </p>

                <div class="alert alert-light border text-start mt-4">

                    <div class="row">

                        <div class="col-6 mb-3">

                            <small class="text-muted">
                                Order Number
                            </small>

                            <div class="fw-bold">
                                #<?php echo $orderId; ?>
                            </div>

                        </div>

                        <div class="col-6 mb-3">

                            <small class="text-muted">
                                Status
                            </small>

                            <div>
                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>
                            </div>

                        </div>

                        <div class="col-6">

                            <small class="text-muted">
                                Customer
                            </small>

                            <div class="fw-semibold">
                                <?php
                                echo htmlspecialchars($customerName);
                                ?>
                            </div>

                        </div>

                        <div class="col-6">

                            <small class="text-muted">
                                Total
                            </small>

                            <div class="fw-bold text-success">
                                ₹<?php
                                echo number_format(
                                    $totalAmount,
                                    2
                                );
                                ?>
                            </div>

                        </div>

                    </div>

                </div>

                <p class="text-muted mt-4">
                    Send your order details to Brar Pet Shop on WhatsApp.
                </p>

                <a
                    href="<?php echo htmlspecialchars($whatsappUrl); ?>"
                    target="_blank"
                    class="btn btn-success btn-lg w-100">

                    <i class="bi bi-whatsapp me-2"></i>

                    Send Order on WhatsApp

                </a>

                <a
                    href="my_orders.php"
                    class="btn btn-outline-primary mt-3">

                    <i class="bi bi-receipt me-2"></i>

                    View My Orders

                </a>

                <a
                    href="index.php"
                    class="btn btn-link mt-2">

                    Continue Shopping

                </a>

            </div>

        </div>

    </div>

    </body>

    </html>

    <?php

    exit();

} catch (Exception $e) {

    $conn->rollback();

    $errorMessage = $e->getMessage();
}


}

?>

<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>
    Confirm Order - Brar Pet Shop
</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    rel="stylesheet">


</head>

<body class="bg-light">

<div class="container py-5">


<div
    class="card shadow-sm mx-auto"
    style="max-width: 800px;">

    <div class="card-body p-4 p-md-5">

        <h2 class="fw-bold mb-2">
            Confirm Your Order
        </h2>

        <p class="text-muted mb-4">
            Please review your order before placing it.
        </p>

        <?php if ($errorMessage !== ""): ?>

            <div class="alert alert-danger">

                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                <?php
                echo htmlspecialchars($errorMessage);
                ?>

            </div>

        <?php endif; ?>

        <!-- =========================
             ORDER ITEMS
        ========================== -->

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                <tr>

                    <th>Item</th>

                    <th>Type</th>

                    <th>Price</th>

                    <th>Qty</th>

                    <th class="text-end">
                        Subtotal
                    </th>

                </tr>

                </thead>

                <tbody>

                <?php foreach ($cartItems as $item): ?>

                    <tr>

                        <td class="fw-semibold">

                            <?php
                            echo htmlspecialchars(
                                $item["item_name"]
                            );
                            ?>

                        </td>

                        <td>

                            <?php if ($item["item_type"] === "Pet"): ?>

                                <span class="badge bg-primary">
                                    Pet
                                </span>

                            <?php else: ?>

                                <span class="badge bg-success">
                                    Product
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                            ₹<?php
                            echo number_format(
                                $item["price"],
                                2
                            );
                            ?>

                        </td>

                        <td>

                            <?php echo $item["quantity"]; ?>

                        </td>

                        <td class="text-end fw-semibold">

                            ₹<?php
                            echo number_format(
                                $item["subtotal"],
                                2
                            );
                            ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

                <tfoot>

                <tr>

                    <th colspan="4" class="text-end">
                        Total:
                    </th>

                    <th class="text-end text-success fs-5">

                        ₹<?php
                        echo number_format(
                            $totalAmount,
                            2
                        );
                        ?>

                    </th>

                </tr>

                </tfoot>

            </table>

        </div>

        <!-- =========================
             CUSTOMER
        ========================== -->

        <div class="card bg-light border-0 mt-4">

            <div class="card-body">

                <h6 class="fw-bold mb-3">

                    <i class="bi bi-person-fill me-2"></i>

                    Customer Information

                </h6>

                <div>
                    <strong>Name:</strong>
                    <?php
                    echo htmlspecialchars($customerName);
                    ?>
                </div>

                <div>
                    <strong>Email:</strong>
                    <?php
                    echo htmlspecialchars($customerEmail);
                    ?>
                </div>

            </div>

        </div>

        <div class="alert alert-info mt-4">

            <i class="bi bi-info-circle-fill me-2"></i>

            Your order will be created first. After that,
            you can send the order details on WhatsApp.

        </div>

        <!-- =========================
             CREATE ORDER
        ========================== -->

        <form method="POST">

            <button
                type="submit"
                class="btn btn-success btn-lg w-100">

                <i class="bi bi-cart-check me-2"></i>

                Create Order

            </button>

        </form>

        <a
            href="cart.php"
            class="btn btn-outline-secondary w-100 mt-3">

            <i class="bi bi-arrow-left me-2"></i>

            Back to Cart

        </a>

    </div>

</div>


</div>

</body>

</html>
