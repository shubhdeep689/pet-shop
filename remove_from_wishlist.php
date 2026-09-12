<?php

session_start();

require_once "config/db.php";


// Login check
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}


$userId = (int)$_SESSION["user_id"];

$wishlistId = isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;


// Invalid ID
if ($wishlistId <= 0) {
    header("Location: wishlist.php");
    exit();
}


// Delete only the logged-in user's wishlist item
$stmt = $conn->prepare("
    DELETE FROM wishlist
    WHERE id = ?
    AND user_id = ?
");

$stmt->bind_param("ii", $wishlistId, $userId);

if ($stmt->execute()) {

    header("Location: wishlist.php?success=removed");
    exit();

}


// If something goes wrong
header("Location: wishlist.php?error=remove_failed");
exit();

?>