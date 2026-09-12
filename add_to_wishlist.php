<?php

session_start();

require_once "config/db.php";


// User must be logged in

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit();

}

$userId = (int)$_SESSION["user_id"];


// Get IDs

$petId = isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;

$productId = isset($_GET["product_id"])
    ? (int)$_GET["product_id"]
    : 0;


// Make sure only one type is provided

if ($petId > 0 && $productId > 0) {

    header("Location: index.php");

    exit();

}


// No item

if ($petId <= 0 && $productId <= 0) {

    header("Location: index.php");

    exit();

}


// =========================
// ADD PET TO WISHLIST
// =========================

if ($petId > 0) {

    // Check pet exists

    $stmt = $conn->prepare("
        SELECT id
        FROM pets
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $petId);

    $stmt->execute();

    $petResult = $stmt->get_result();

    if ($petResult->num_rows === 0) {

        header("Location: pets.php?error=not_found");

        exit();

    }


    // Check if already in wishlist

    $stmt = $conn->prepare("
        SELECT id
        FROM wishlist
        WHERE user_id = ?
        AND pet_id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        "ii",
        $userId,
        $petId
    );

    $stmt->execute();

    $existing = $stmt->get_result();


    if ($existing->num_rows > 0) {

        header("Location: pets.php?error=already_wishlisted");

        exit();

    }


    // Add to wishlist

    $stmt = $conn->prepare("
        INSERT INTO wishlist
        (user_id, pet_id, product_id)
        VALUES (?, ?, NULL)
    ");

    $stmt->bind_param(
        "ii",
        $userId,
        $petId
    );

    if ($stmt->execute()) {

        header("Location: wishlist.php?success=added");

        exit();

    }

}


// =========================
// ADD PRODUCT TO WISHLIST
// =========================

if ($productId > 0) {

    // Check product exists

    $stmt = $conn->prepare("
        SELECT id
        FROM products
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $productId);

    $stmt->execute();

    $productResult = $stmt->get_result();

    if ($productResult->num_rows === 0) {

        header("Location: product.php?error=not_found");

        exit();

    }


    // Check if already in wishlist

    $stmt = $conn->prepare("
        SELECT id
        FROM wishlist
        WHERE user_id = ?
        AND product_id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        "ii",
        $userId,
        $productId
    );

    $stmt->execute();

    $existing = $stmt->get_result();


    if ($existing->num_rows > 0) {

        header("Location: product.php?error=already_wishlisted");

        exit();

    }


    // Add to wishlist

    $stmt = $conn->prepare("
        INSERT INTO wishlist
        (user_id, pet_id, product_id)
        VALUES (?, NULL, ?)
    ");

    $stmt->bind_param(
        "ii",
        $userId,
        $productId
    );

    if ($stmt->execute()) {

        header("Location: wishlist.php?success=added");

        exit();

    }

}


// Something went wrong

header("Location: index.php");

exit();

?>