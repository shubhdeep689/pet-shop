<?php

session_start();

require_once "config/db.php";


// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$userName = $_SESSION["user_name"] ?? "Customer";


// Get user's cart
$sql = "SELECT
            c.quantity,
            p.name,
            p.price
        FROM cart_items c
        INNER JOIN pets p ON c.pet_id = p.id
        WHERE c.user_id = ?
        ORDER BY c.id ASC";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {

    header("Location: cart.php");
    exit();

}


// WhatsApp message
$message = "Hello Brar Pet Shop!%0A%0A";

$message .= "I would like to order the following pets:%0A%0A";

$total = 0;
$itemNumber = 1;

while ($item = $result->fetch_assoc()) {

    $name = $item["name"];
    $quantity = $item["quantity"];
    $price = $item["price"];

    $itemTotal = $price * $quantity;

    $total += $itemTotal;

    $message .= $itemNumber . ". "
             . $name
             . " x "
             . $quantity
             . " - ₹"
             . number_format($itemTotal)
             . "%0A";

    $itemNumber++;
}


$message .= "%0A--------------------%0A";

$message .= "Total: ₹" . number_format($total);

$message .= "%0A%0ACustomer Name: "
          . $userName;

$message .= "%0A%0APlease confirm my order. Thank you!";


// YOUR WHATSAPP NUMBER
$whatsappNumber = "+919779654617";


// WhatsApp URL
$whatsappURL = "https://wa.me/"
             . $whatsappNumber
             . "?text="
             . $message;


// Redirect to WhatsApp
header("Location: " . $whatsappURL);
exit();

?>