```php
<?php

session_start();

require_once "config/db.php";


// Check login
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}


$user_id = $_SESSION["user_id"];


// ==================================================
// ADD PET TO CART
// ==================================================

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {

    $pet_id = (int) $_GET["id"];


    // Check pet exists
    $stmt = $conn->prepare(
        "SELECT id FROM pets WHERE id = ?"
    );

    $stmt->bind_param("i", $pet_id);

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows === 0) {

        header("Location: pets.php");
        exit();

    }

    $stmt->close();


    // Check if already in cart
    $stmt = $conn->prepare(
        "SELECT id, quantity
         FROM cart_items
         WHERE user_id = ?
         AND pet_id = ?"
    );

    $stmt->bind_param(
        "ii",
        $user_id,
        $pet_id
    );

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows > 0) {

        $cart = $result->fetch_assoc();

        $new_quantity = $cart["quantity"] + 1;


        $update = $conn->prepare(
            "UPDATE cart_items
             SET quantity = ?
             WHERE id = ?"
        );

        $update->bind_param(
            "ii",
            $new_quantity,
            $cart["id"]
        );

        $update->execute();

        $update->close();


    } else {


        $insert = $conn->prepare(
            "INSERT INTO cart_items
             (user_id, pet_id, product_id, quantity)
             VALUES (?, ?, NULL, 1)"
        );

        $insert->bind_param(
            "ii",
            $user_id,
            $pet_id
        );

        $insert->execute();

        $insert->close();

    }


    $stmt->close();


    header("Location: cart.php");
    exit();

}


// ==================================================
// ADD PRODUCT TO CART
// ==================================================

if (isset($_GET["product_id"]) && is_numeric($_GET["product_id"])) {

    $product_id = (int) $_GET["product_id"];


    // Check product exists
    $stmt = $conn->prepare(
        "SELECT id FROM products WHERE id = ?"
    );

    $stmt->bind_param(
        "i",
        $product_id
    );

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows === 0) {

        header("Location: product.php");
        exit();

    }

    $stmt->close();


    // Check if product already in cart
    $stmt = $conn->prepare(
        "SELECT id, quantity
         FROM cart_items
         WHERE user_id = ?
         AND product_id = ?"
    );

    $stmt->bind_param(
        "ii",
        $user_id,
        $product_id
    );

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows > 0) {

        $cart = $result->fetch_assoc();

        $new_quantity = $cart["quantity"] + 1;


        $update = $conn->prepare(
            "UPDATE cart_items
             SET quantity = ?
             WHERE id = ?"
        );

        $update->bind_param(
            "ii",
            $new_quantity,
            $cart["id"]
        );

        $update->execute();

        $update->close();


    } else {


        $insert = $conn->prepare(
            "INSERT INTO cart_items
             (user_id, pet_id, product_id, quantity)
             VALUES (?, NULL, ?, 1)"
        );

        $insert->bind_param(
            "ii",
            $user_id,
            $product_id
        );

        $insert->execute();

        $insert->close();

    }


    $stmt->close();


    header("Location: cart.php");
    exit();

}


// If nothing was selected

header("Location: pets.php");
exit();

?>
```
