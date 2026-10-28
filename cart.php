
<?php

session_start();

require_once "config/db.php";

// ==========================================
// CHECK LOGIN
// ==========================================

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION["user_id"];
$userName = $_SESSION["user_name"] ?? "Customer";

// ==========================================
// GET CART MESSAGE
// ==========================================

$cartMessage = $_SESSION["cart_message"] ?? "";
unset($_SESSION["cart_message"]);

// ==========================================
// GET CART ITEMS
// SUPPORTS PETS + PRODUCTS
// ==========================================

$sql = "SELECT
            c.id AS cart_id,
            c.quantity,
            c.pet_id,
            c.product_id,

            COALESCE(p.name, pr.name) AS name,
            COALESCE(p.price, pr.price) AS price,
            COALESCE(p.image, pr.image) AS image,
            COALESCE(p.description, pr.description) AS description,

            p.age,
            p.quantity AS pet_stock,
            p.availability AS pet_availability,
            pr.stock AS product_stock,

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

        ORDER BY c.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

// ==========================================
// CALCULATE TOTALS
// ==========================================

$totalItems = 0;
$subtotal = 0;
$cartItems = [];

while ($item = $result->fetch_assoc()) {

    $item["quantity"] = (int) $item["quantity"];
    $item["price"] = (float) $item["price"];

    $cartItems[] = $item;

    $totalItems += $item["quantity"];

    $subtotal += $item["price"] * $item["quantity"];
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="Shopping Cart | Brar Pet Shop">

    <title>Shopping Cart | Brar Pet Shop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link rel="stylesheet"
          href="style.css">

</head>

<body>

<?php include 'navbar.php'; ?>

<!-- CART HEADER -->

<section class="py-5 bg-light">

    <div class="container text-center">

        <i class="bi bi-cart3 display-4 text-primary"></i>

        <h1 class="mt-3">Your Shopping Cart</h1>

        <p class="text-muted">
            Review your selected pets and products before placing your order.
        </p>

    </div>

</section>

<!-- CART CONTENT -->

<section class="py-5">

    <div class="container">

        <!-- SUCCESS / ERROR MESSAGE -->

        <?php if ($cartMessage !== ""): ?>

            <div class="alert alert-info alert-dismissible fade show"
                 role="alert">

                <?php echo htmlspecialchars($cartMessage); ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                </button>

            </div>

        <?php endif; ?>

        <div class="row g-4">

            <!-- CART ITEMS -->

            <div class="col-lg-8">

                <div class="card shadow-sm">

                    <div class="card-header bg-white">

                        <h5 class="mb-0">

                            <i class="bi bi-cart-fill text-primary me-2"></i>

                            Cart Items

                        </h5>

                    </div>

                    <?php if (count($cartItems) > 0): ?>

                        <div class="card-body">

                            <?php foreach ($cartItems as $item): ?>

                                <?php

                                $cartId = (int) $item["cart_id"];
                                $quantity = (int) $item["quantity"];

                                $stock = $item["item_type"] === "Pet"
                                    ? (int) $item["pet_stock"]
                                    : (int) $item["product_stock"];

                                $isPetUnavailable =
                                    $item["item_type"] === "Pet" &&
                                    strcasecmp(
                                        trim((string) $item["pet_availability"]),
                                        "Available"
                                    ) !== 0;

                                $isOutOfStock = $stock < 1;

                                ?>

                                <div class="row align-items-center border-bottom py-4">

                                    <!-- IMAGE -->

                                    <div class="col-md-3 text-center mb-3 mb-md-0">

                                        <img
                                            src="<?php echo htmlspecialchars($item["image"] ?? ""); ?>"
                                            alt="<?php echo htmlspecialchars($item["name"] ?? "Item"); ?>"
                                            class="img-fluid rounded"
                                            style="height:120px;
                                                   width:150px;
                                                   object-fit:cover;"
                                        >

                                    </div>

                                    <!-- ITEM INFORMATION -->

                                    <div class="col-md-5">

                                        <h5 class="mb-1">

                                            <?php echo htmlspecialchars($item["name"] ?? "Unavailable item"); ?>

                                        </h5>

                                        <?php if ($item["item_type"] === "Pet"): ?>

                                            <span class="badge bg-success mb-2">

                                                <i class="bi bi-paw-fill me-1"></i>
                                                Pet

                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-primary mb-2">

                                                <i class="bi bi-box-seam me-1"></i>
                                                Product

                                            </span>

                                        <?php endif; ?>

                                        <p class="text-muted mb-1">

                                            <?php echo htmlspecialchars($item["description"] ?? ""); ?>

                                        </p>

                                        <?php if ($item["item_type"] === "Pet"): ?>

                                            <small class="text-muted">

                                                <strong>Age:</strong>

                                                <?php echo htmlspecialchars((string) ($item["age"] ?? "Not specified")); ?>

                                            </small>

                                            <br>

                                        <?php endif; ?>

                                        <small>

                                            <strong>Price:</strong>

                                            <span class="text-danger">

                                                ₹<?php echo number_format($item["price"], 2); ?>

                                            </span>

                                        </small>

                                        <br>

                                        <small class="text-muted">

                                            Available stock: <?php echo $stock; ?>

                                        </small>

                                        <?php if ($isPetUnavailable || $isOutOfStock): ?>

                                            <div class="alert alert-warning py-2 mt-2 mb-0">

                                                <small>
                                                    This item is currently unavailable.
                                                    Please remove it or contact the shop.
                                                </small>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                    <!-- QUANTITY CONTROLS -->

                                    <div class="col-md-2 text-center my-3 my-md-0">

                                        <small class="text-muted d-block mb-2">
                                            Quantity
                                        </small>

                                        <div class="d-flex justify-content-center align-items-center gap-2">

                                            <!-- DECREASE QUANTITY -->

                                            <form action="update_cart.php"
                                                  method="POST">

                                                <input type="hidden"
                                                       name="cart_id"
                                                       value="<?php echo $cartId; ?>">

                                                <input type="hidden"
                                                       name="action"
                                                       value="decrease">

                                                <button type="submit"
                                                        class="btn btn-outline-secondary btn-sm"
                                                        aria-label="Decrease quantity">

                                                    <i class="bi bi-dash-lg"></i>

                                                </button>

                                            </form>

                                            <!-- CURRENT QUANTITY -->

                                            <strong class="fs-5">

                                                <?php echo $quantity; ?>

                                            </strong>

                                            <!-- INCREASE QUANTITY -->

                                            <form action="update_cart.php"
                                                  method="POST">

                                                <input type="hidden"
                                                       name="cart_id"
                                                       value="<?php echo $cartId; ?>">

                                                <input type="hidden"
                                                       name="action"
                                                       value="increase">

                                                <button type="submit"
                                                        class="btn btn-outline-primary btn-sm"
                                                        aria-label="Increase quantity"
                                                        <?php echo ($quantity >= $stock || $isPetUnavailable) ? "disabled" : ""; ?>>

                                                    <i class="bi bi-plus-lg"></i>

                                                </button>

                                            </form>

                                        </div>

                                    </div>

                                    <!-- SUBTOTAL AND REMOVE -->

                                    <div class="col-md-2 text-center">

                                        <p class="fw-bold mb-2">

                                            ₹<?php echo number_format(
                                                $item["price"] * $quantity,
                                                2
                                            ); ?>

                                        </p>

                                        <a
                                            href="remove_from_cart.php?id=<?php echo $cartId; ?>"
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Remove this item from your cart?');"
                                        >

                                            <i class="bi bi-trash"></i>

                                            Remove

                                        </a>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                        <!-- CONTINUE SHOPPING -->

                        <div class="card-footer bg-white">

                            <div class="d-flex gap-2 flex-wrap">

                                <a href="pets.php"
                                   class="btn btn-outline-primary">

                                    <i class="bi bi-paw me-2"></i>
                                    Browse Pets

                                </a>

                                <a href="product.php"
                                   class="btn btn-outline-success">

                                    <i class="bi bi-box-seam me-2"></i>
                                    Browse Products

                                </a>

                            </div>

                        </div>

                    <?php else: ?>

                        <!-- EMPTY CART -->

                        <div class="card-body text-center py-5">

                            <i class="bi bi-cart-x display-1 text-muted"></i>

                            <h4 class="mt-3">
                                Your cart is empty
                            </h4>

                            <p class="text-muted">

                                You haven't added any pets or products to your cart yet.

                            </p>

                            <div class="d-flex justify-content-center gap-2 flex-wrap">

                                <a href="pets.php"
                                   class="btn btn-primary">

                                    <i class="bi bi-paw me-2"></i>
                                    Browse Pets

                                </a>

                                <a href="product.php"
                                   class="btn btn-success">

                                    <i class="bi bi-box-seam me-2"></i>
                                    Browse Products

                                </a>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

            <!-- ORDER SUMMARY -->

            <div class="col-lg-4">

                <div class="card shadow-sm">

                    <div class="card-header bg-primary text-white">

                        <h5 class="mb-0">Order Summary</h5>

                    </div>

                    <div class="card-body">

                        <div class="d-flex justify-content-between mb-3">

                            <span>Items</span>

                            <strong><?php echo $totalItems; ?></strong>

                        </div>

                        <div class="d-flex justify-content-between mb-3">

                            <span>Subtotal</span>

                            <strong>

                                ₹<?php echo number_format($subtotal, 2); ?>

                            </strong>

                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">

                            <strong>Total</strong>

                            <strong class="text-primary fs-5">

                                ₹<?php echo number_format($subtotal, 2); ?>

                            </strong>

                        </div>

                        <?php if ($totalItems > 0): ?>

                            <a href="order_cart.php"
                               class="btn btn-success w-100">

                                <i class="bi bi-whatsapp me-2"></i>
                                Order Cart

                            </a>

                            <small class="text-muted d-block text-center mt-2">

                                Review your order before proceeding to WhatsApp.

                            </small>

                        <?php else: ?>

                            <button type="button"
                                    class="btn btn-success w-100"
                                    disabled>

                                <i class="bi bi-whatsapp me-2"></i>
                                Order Cart

                            </button>

                            <small class="text-muted d-block text-center mt-2">

                                Add a pet or product to enable ordering.

                            </small>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="script.js"></script>

</body>

</html>

