
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

// ==========================================
// ACCEPT POST REQUESTS ONLY
// ==========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cart.php");
    exit();
}

// ==========================================
// GET AND VALIDATE INPUT
// ==========================================

$cart_id = filter_input(INPUT_POST, "cart_id", FILTER_VALIDATE_INT);
$action = $_POST["action"] ?? "";

if (!$cart_id || $cart_id < 1) {
    $_SESSION["cart_message"] = "Invalid cart item.";
    header("Location: cart.php");
    exit();
}

if (!in_array($action, ["increase", "decrease"], true)) {
    $_SESSION["cart_message"] = "Invalid quantity operation.";
    header("Location: cart.php");
    exit();
}

// ==========================================
// GET THE CUSTOMER'S CART ITEM
// ==========================================

$sql = "SELECT
            c.id,
            c.quantity,
            c.pet_id,
            c.product_id,

            p.quantity AS pet_stock,
            p.availability AS pet_availability,

            pr.stock AS product_stock

        FROM cart_items c

        LEFT JOIN pets p
            ON c.pet_id = p.id

        LEFT JOIN products pr
            ON c.product_id = pr.id

        WHERE c.id = ?
          AND c.user_id = ?

        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();
$item = $result->fetch_assoc();

$stmt->close();

// ==========================================
// VERIFY CART ITEM OWNERSHIP
// ==========================================

if (!$item) {
    $_SESSION["cart_message"] =
        "Cart item not found or you do not have permission to update it.";

    header("Location: cart.php");
    exit();
}

// ==========================================
// DETERMINE INVENTORY
// ==========================================

$current_quantity = (int) $item["quantity"];

$isPet = $item["pet_id"] !== null;

if ($isPet) {

    $available_stock = (int) ($item["pet_stock"] ?? 0);

    $petAvailable = strcasecmp(
        trim((string) ($item["pet_availability"] ?? "")),
        "Available"
    ) === 0;

} else {

    $available_stock = (int) ($item["product_stock"] ?? 0);

    $petAvailable = true;
}

// ==========================================
// DECREASE QUANTITY
// ==========================================

if ($action === "decrease") {

    // Quantity 1 becomes 0, so remove the item.
    if ($current_quantity <= 1) {

        $stmt = $conn->prepare(
            "DELETE FROM cart_items
             WHERE id = ? AND user_id = ?"
        );

        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();

        $deleted = $stmt->affected_rows;
        $stmt->close();

        if ($deleted > 0) {
            $_SESSION["cart_message"] =
                "Item removed from your cart.";
        } else {
            $_SESSION["cart_message"] =
                "Unable to remove this item.";
        }

    } else {

        $new_quantity = $current_quantity - 1;

        $stmt = $conn->prepare(
            "UPDATE cart_items
             SET quantity = ?
             WHERE id = ? AND user_id = ?"
        );

        $stmt->bind_param(
            "iii",
            $new_quantity,
            $cart_id,
            $user_id
        );

        $stmt->execute();
        $stmt->close();

        $_SESSION["cart_message"] =
            "Cart quantity decreased successfully.";
    }

    header("Location: cart.php");
    exit();
}

// ==========================================
// INCREASE QUANTITY
// ==========================================

// Pets must remain available.
if ($isPet && !$petAvailable) {

    $_SESSION["cart_message"] =
        "This pet is no longer available. You cannot increase its quantity.";

    header("Location: cart.php");
    exit();
}

// Ensure inventory is available.
if ($available_stock < 1) {

    $_SESSION["cart_message"] =
        "This item is out of stock. You cannot increase its quantity.";

    header("Location: cart.php");
    exit();
}

// Prevent quantity from exceeding stock.
if ($current_quantity >= $available_stock) {

    $_SESSION["cart_message"] =
        "You have reached the maximum available stock for this item.";

    header("Location: cart.php");
    exit();
}

// ==========================================
// UPDATE QUANTITY
// ==========================================

$new_quantity = $current_quantity + 1;

$stmt = $conn->prepare(
    "UPDATE cart_items
     SET quantity = ?
     WHERE id = ?
       AND user_id = ?"
);

$stmt->bind_param(
    "iii",
    $new_quantity,
    $cart_id,
    $user_id
);

$stmt->execute();

$updated = $stmt->affected_rows;

$stmt->close();

// ==========================================
// SET RESULT MESSAGE
// ==========================================

if ($updated > 0) {

    $_SESSION["cart_message"] =
        "Cart quantity increased successfully.";

} else {

    $_SESSION["cart_message"] =
        "The cart quantity was not changed.";
}

// ==========================================
// RETURN TO CART
// ==========================================

header("Location: cart.php");
exit();

?>

