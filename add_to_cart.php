
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
// HELPER: SHOW MESSAGE AND REDIRECT
// ==========================================

function cartRedirect($message, $page = "cart.php")
{
    $_SESSION["cart_message"] = $message;

    header("Location: " . $page);
    exit();
}

// ==========================================
// ADD PET TO CART
// ==========================================

if (isset($_GET["id"])) {

    if (!ctype_digit((string) $_GET["id"]) || (int) $_GET["id"] < 1) {
        cartRedirect("Invalid pet ID.", "pets.php");
    }

    $pet_id = (int) $_GET["id"];

    // Check pet availability and quantity.
    $stmt = $conn->prepare(
        "SELECT id, quantity, availability
         FROM pets
         WHERE id = ?
         LIMIT 1"
    );

    $stmt->bind_param("i", $pet_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $pet = $result->fetch_assoc();

    $stmt->close();

    if (!$pet) {
        cartRedirect("Pet not found.", "pets.php");
    }

    // Quantity determines whether the pet is available.
$available_quantity = (int) $pet["quantity"];

if ($available_quantity <= 0) {
    cartRedirect("This pet is sold out.");
}

    // Check whether the pet is already in this customer's cart.
    $stmt = $conn->prepare(
        "SELECT id, quantity
         FROM cart_items
         WHERE user_id = ?
           AND pet_id = ?
         LIMIT 1"
    );

    $stmt->bind_param("ii", $user_id, $pet_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $cart = $result->fetch_assoc();

    $stmt->close();

    if ($cart) {

        $current_quantity = (int) $cart["quantity"];

        // Prevent quantity from exceeding pet inventory.
        if ($current_quantity + 1 > $available_quantity) {
            cartRedirect(
                "You cannot add more of this pet than the available quantity."
            );
        }

        $new_quantity = $current_quantity + 1;
        $cart_id = (int) $cart["id"];

        $stmt = $conn->prepare(
            "UPDATE cart_items
             SET quantity = ?
             WHERE id = ? AND user_id = ? AND pet_id = ?"
        );

        $stmt->bind_param(
            "iiii",
            $new_quantity,
            $cart_id,
            $user_id,
            $pet_id
        );

        $stmt->execute();
        $stmt->close();

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO cart_items
             (user_id, pet_id, product_id, quantity)
             VALUES (?, ?, NULL, 1)"
        );

        $stmt->bind_param("ii", $user_id, $pet_id);
        $stmt->execute();
        $stmt->close();

    }

    cartRedirect("Pet added to your cart successfully.");

}

// ==========================================
// ADD PRODUCT TO CART
// ==========================================

if (isset($_GET["product_id"])) {

    if (
        !ctype_digit((string) $_GET["product_id"]) ||
        (int) $_GET["product_id"] < 1
    ) {
        cartRedirect("Invalid product ID.", "products.php");
    }

    $product_id = (int) $_GET["product_id"];

    // Check product stock.
    $stmt = $conn->prepare(
        "SELECT id, stock
         FROM products
         WHERE id = ?
         LIMIT 1"
    );

    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    $stmt->close();

    if (!$product) {
        cartRedirect("Product not found.", "products.php");
    }

    $available_stock = (int) $product["stock"];

    if ($available_stock < 1) {
        cartRedirect("This product is currently out of stock.");
    }

    // Check whether the product is already in the cart.
    $stmt = $conn->prepare(
        "SELECT id, quantity
         FROM cart_items
         WHERE user_id = ?
           AND product_id = ?
         LIMIT 1"
    );

    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $cart = $result->fetch_assoc();

    $stmt->close();

    if ($cart) {

        $current_quantity = (int) $cart["quantity"];

        // Prevent quantity from exceeding available stock.
        if ($current_quantity + 1 > $available_stock) {
            cartRedirect(
                "You cannot add more units than the available stock."
            );
        }

        $new_quantity = $current_quantity + 1;
        $cart_id = (int) $cart["id"];

        $stmt = $conn->prepare(
            "UPDATE cart_items
             SET quantity = ?
             WHERE id = ? AND user_id = ? AND product_id = ?"
        );

        $stmt->bind_param(
            "iiii",
            $new_quantity,
            $cart_id,
            $user_id,
            $product_id
        );

        $stmt->execute();
        $stmt->close();

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO cart_items
             (user_id, pet_id, product_id, quantity)
             VALUES (?, NULL, ?, 1)"
        );

        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $stmt->close();

    }

    cartRedirect("Product added to your cart successfully.");

}

// ==========================================
// INVALID REQUEST
// ==========================================

cartRedirect("Please select a valid pet or product.", "pets.php");

?>

