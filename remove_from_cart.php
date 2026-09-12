<?php

session_start();

require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: cart.php");
    exit();
}

$cart_id = (int) $_GET["id"];
$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "DELETE FROM cart_items
     WHERE id = ? AND user_id = ?"
);

$stmt->bind_param("ii", $cart_id, $user_id);

$stmt->execute();

$stmt->close();

header("Location: cart.php");
exit();

?>