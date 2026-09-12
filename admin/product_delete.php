<?php

require_once "auth.php";
require_once "../config/db.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: products.php");
    exit();
}

$product_id = (int) $_GET["id"];


/* Check whether product exists */

$stmt = $conn->prepare(
    "SELECT id FROM products WHERE id = ?"
);

if (!$stmt) {
    header("Location: products.php?error=delete_failed");
    exit();
}

$stmt->bind_param("i", $product_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: products.php?error=not_found");
    exit();
}

$stmt->close();


/* Remove product from carts first */

$stmt = $conn->prepare(
    "DELETE FROM cart_items WHERE product_id = ?"
);

if ($stmt) {

    $stmt->bind_param("i", $product_id);

    $stmt->execute();

    $stmt->close();
}


/* Delete product */

$stmt = $conn->prepare(
    "DELETE FROM products WHERE id = ?"
);

if (!$stmt) {

    header("Location: products.php?error=delete_failed");
    exit();
}

$stmt->bind_param("i", $product_id);


if ($stmt->execute()) {

    $stmt->close();

    header("Location: products.php?success=deleted");
    exit();

}


$stmt->close();

header("Location: products.php?error=delete_failed");
exit();

?>