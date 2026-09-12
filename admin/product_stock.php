<?php

require_once "auth.php";
require_once "../config/db.php";

header("Content-Type: application/json");


// Get product ID and stock
$productId = isset($_POST["product_id"])
    ? (int)$_POST["product_id"]
    : 0;

$stock = isset($_POST["stock"])
    ? (int)$_POST["stock"]
    : -1;


// Validate
if ($productId <= 0 || $stock < 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid product ID or stock quantity."
    ]);

    exit;
}


// Update stock
$stmt = $conn->prepare("
    UPDATE products
    SET stock = ?
    WHERE id = ?
");

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Database query failed."
    ]);

    exit;
}


$stmt->bind_param(
    "ii",
    $stock,
    $productId
);


if ($stmt->execute()) {

    if ($stmt->affected_rows >= 0) {

        echo json_encode([
            "success" => true,
            "message" => "Stock updated successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Product not found."
        ]);

    }

} else {

    echo json_encode([
        "success" => false,
        "message" => "Stock could not be updated."
    ]);

}


$stmt->close();
$conn->close();

?>