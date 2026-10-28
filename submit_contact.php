<?php

// Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact.php");
    exit;
}

// Connect to the database
require_once __DIR__ . "/config/db.php";

// Collect form data
$fullName = trim($_POST["fullName"] ?? "");
$email    = trim($_POST["email"] ?? "");
$phone    = trim($_POST["phone"] ?? "");
$petType  = trim($_POST["petType"] ?? "");
$subject  = trim($_POST["subject"] ?? "");
$message  = trim($_POST["message"] ?? "");

// Validate required fields
if (
    $fullName === "" ||
    $email === "" ||
    $phone === "" ||
    $petType === "" ||
    $subject === "" ||
    $message === ""
) {
    exit("Error: Please fill in all fields.");
}

// Validate name
if (strlen($fullName) > 100) {
    exit("Error: Name must not exceed 100 characters.");
}

// Validate email
if (
    strlen($email) > 150 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    exit("Error: Please enter a valid email address.");
}

// Validate phone: accept 10 digits or Indian number with country code
$phoneDigits = preg_replace('/\D/', '', $phone);

if (
    !preg_match('/^\+?[0-9\s().-]+$/', $phone) ||
    !(strlen($phoneDigits) === 10 ||
      (strlen($phoneDigits) === 12 && str_starts_with($phoneDigits, "91")))
) {
    exit("Error: Please enter a valid Indian phone number.");
}

// Validate pet type using the actual values from contact.php
$allowedPetTypes = [
    "dog",
    "cat",
    "bird",
    "fish",
    "rabbit",
    "hamster",
    "other"
];

if (!in_array($petType, $allowedPetTypes, true)) {
    exit("Error: Please select a valid pet type.");
}

// Validate subject and message
if (strlen($subject) > 200 || strlen($message) > 10000) {
    exit("Error: Subject or message is too long.");
}

// Prepare the database query
$sql = "INSERT INTO contact_messages
        (full_name, email, phone, pet_type, subject, message)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Contact form prepare error: " . $conn->error);
    exit("Database query failed. Please check your contact_messages table.");
}

// Bind data securely
$stmt->bind_param(
    "ssssss",
    $fullName,
    $email,
    $phone,
    $petType,
    $subject,
    $message
);

// Save data
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    header("Location: contact.php?sent=1");
    exit;
}

// Log the actual error for debugging
error_log("Contact form insert error: " . $stmt->error);

$stmt->close();
$conn->close();

exit("Failed to save your message. Check the PHP error log for details.");

?>
