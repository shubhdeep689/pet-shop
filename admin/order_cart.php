<?php

session_start();

require_once "config/db.php";

/* User must be logged in */
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION["user_id"];

/* Get logged-in user's details */
$stmt = $conn->prepare(
    "SELECT name, email
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$userResult = $stmt->get_result();

if ($userResult->num_rows !== 1) {
    $stmt->close();
    session_unset();
    session_destroy();

    header("Location: login.php");
    exit();
}

$user = $userResult->fetch_assoc();
$stmt->close();


/* Get cart items */
$stmt = $conn->prepare(
    "SELECT
        c.id AS cart_id,
        c.quantity,
        c.pet_id,
        c.product_id,
        COALESCE(p.name, pr.name) AS name,
        COALESCE(p.price, pr.price) AS price,
        CASE
            WHEN c.pet_id IS NOT NULL THEN 'Pet'
            WHEN c.product_id IS NOT NULL THEN 'Product'
            ELSE 'Unknown'
        END AS item_type
     FROM cart_items c
     LEFT JOIN pets p
        ON c.pet_id = p.id
     LEFT JOIN products pr
        ON c.product_id = pr.id
     WHERE c.user_id = ?
     ORDER BY c.id ASC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    header("Location: cart.php");
    exit();
}


/* Store cart items */
$cartItems = [];
$totalAmount = 0;

while ($row = $result->fetch_assoc()) {

    $quantity = (int) $row["quantity"];
    $price = (float) $row["price"];

    $subtotal = $quantity * $price;

    $row["subtotal"] = $subtotal;

    $cartItems[] = $row;

    $totalAmount += $subtotal;
}

$stmt->close();


/*
|--------------------------------------------------------------------------
| Create Order
|--------------------------------------------------------------------------
*/

$conn->begin_transaction();

try {

    /* Insert order */
    $status = "Pending";
    $whatsappSent = 0;

    $stmt = $conn->prepare(
        "INSERT INTO orders
        (
            user_id,
            customer_name,
            customer_email,
            total_amount,
            status,
            whatsapp_sent
        )
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        throw new Exception("Order query failed.");
    }

    $stmt->bind_param(
        "issdsi",
        $user_id,
        $user["name"],
        $user["email"],
        $totalAmount,
        $status,
        $whatsappSent
    );

    if (!$stmt->execute()) {
        throw new Exception("Order could not be created.");
    }

    $order_id = $conn->insert_id;

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Insert Order Items
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare(
        "INSERT INTO order_items
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
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        throw new Exception("Order item query failed.");
    }


    foreach ($cartItems as $item) {

        $pet_id = !empty($item["pet_id"])
            ? (int) $item["pet_id"]
            : null;

        $product_id = !empty($item["product_id"])
            ? (int) $item["product_id"]
            : null;

        $itemName = $item["name"];
        $itemType = $item["item_type"];
        $quantity = (int) $item["quantity"];
        $price = (float) $item["price"];
        $subtotal = (float) $item["subtotal"];

        $stmt->bind_param(
            "iiissidd",
            $order_id,
            $pet_id,
            $product_id,
            $itemName,
            $itemType,
            $quantity,
            $price,
            $subtotal
        );

        if (!$stmt->execute()) {
            throw new Exception("Order item could not be saved.");
        }
    }

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Clear Cart
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare(
        "DELETE FROM cart_items
         WHERE user_id = ?"
    );

    if (!$stmt) {
        throw new Exception("Cart clearing query failed.");
    }

    $stmt->bind_param("i", $user_id);

    if (!$stmt->execute()) {
        throw new Exception("Cart could not be cleared.");
    }

    $stmt->close();


    /* Everything successful */
    $conn->commit();


} catch (Exception $e) {

    /* Something failed — undo everything */
    $conn->rollback();

    die(
        "Order could not be completed. Please try again.<br><br>" .
        htmlspecialchars($e->getMessage())
    );
}


/*
|--------------------------------------------------------------------------
| WhatsApp Message
|--------------------------------------------------------------------------
*/

$whatsappMessage = "";

$whatsappMessage .= "🐾 *NEW PET SHOP ORDER*%0A";
$whatsappMessage .= "--------------------------------%0A";

$whatsappMessage .= "*Order ID:* #" . $order_id . "%0A";
$whatsappMessage .= "*Customer:* " . $user["name"] . "%0A";
$whatsappMessage .= "*Email:* " . $user["email"] . "%0A";

$whatsappMessage .= "--------------------------------%0A";

foreach ($cartItems as $item) {

    $whatsappMessage .=
        "*" . $item["name"] . "*%0A";

    $whatsappMessage .=
        "Type: " . $item["item_type"] . "%0A";

    $whatsappMessage .=
        "Quantity: " . $item["quantity"] . "%0A";

    $whatsappMessage .=
        "Price: ₹" . number_format($item["price"], 2) . "%0A";

    $whatsappMessage .=
        "Subtotal: ₹" . number_format($item["subtotal"], 2) . "%0A";

    $whatsappMessage .= "--------------------------------%0A";
}

$whatsappMessage .=
    "*TOTAL: ₹" . number_format($totalAmount, 2) . "*%0A%0A";

$whatsappMessage .=
    "Please confirm this order. Thank you! 🐶🐱";


/*
|--------------------------------------------------------------------------
| IMPORTANT:
| Replace this number with your shop WhatsApp number.
| Use country code without + or spaces.
| Example: 919876543210
|--------------------------------------------------------------------------
*/

$shopWhatsAppNumber = "+919779654617";

$whatsappURL =
    "https://wa.me/" .
    $shopWhatsAppNumber .
    "?text=" .
    rawurlencode(
        urldecode($whatsappMessage)
    );

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Order Created - Brar Pet Shop</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <style>

        body {
            min-height: 100vh;
            background: #f5f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .success-card {
            width: 100%;
            max-width: 650px;
            background: white;
            border-radius: 20px;
            padding: 45px;
            text-align: center;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.10);
        }

        .success-icon {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            background: #e8f8ee;
            color: #198754;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            margin: 0 auto 25px;
        }

        .order-number {
            font-size: 28px;
            font-weight: 700;
            color: #0d6efd;
            margin: 15px 0;
        }

        .total {
            font-size: 24px;
            font-weight: 700;
            color: #198754;
        }

        .whatsapp-btn {
            font-size: 17px;
            padding: 13px 25px;
            border-radius: 10px;
        }

        @media (max-width: 576px) {

            .success-card {
                margin: 20px;
                padding: 30px 20px;
            }

        }

    </style>

</head>

<body>

<div class="success-card">

    <div class="success-icon">
        <i class="bi bi-check-lg"></i>
    </div>

    <h1 class="fw-bold">
        Order Created Successfully!
    </h1>

    <p class="text-muted">
        Your order has been saved successfully.
    </p>

    <div class="order-number">
        Order #<?php echo $order_id; ?>
    </div>

    <div class="border rounded-3 p-3 my-4">

        <div class="text-muted mb-1">
            Customer
        </div>

        <div class="fw-bold">
            <?php echo htmlspecialchars($user["name"]); ?>
        </div>

        <hr>

        <div class="text-muted mb-1">
            Total Amount
        </div>

        <div class="total">
            ₹<?php echo number_format($totalAmount, 2); ?>
        </div>

    </div>

    <a
        href="<?php echo htmlspecialchars($whatsappURL); ?>"
        target="_blank"
        class="btn btn-success whatsapp-btn w-100 mb-3">

        <i class="bi bi-whatsapp me-2"></i>
        Send Order on WhatsApp

    </a>

    <a
        href="index.php"
        class="btn btn-outline-secondary w-100">

        <i class="bi bi-house me-2"></i>
        Back to Website

    </a>

    <p class="small text-muted mt-4 mb-0">
        Order status: <strong>Pending</strong>
    </p>

</div>

</body>

</html>