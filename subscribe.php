
<?php
session_start();

require_once __DIR__ . '/config/db.php';

// Accept POST requests only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Validate email address
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

if (!$email) {
    $_SESSION['newsletter_message'] = 'Please enter a valid email address.';
    $_SESSION['newsletter_status'] = 'danger';

    header('Location: index.php');
    exit;
}

// Insert email into database
$stmt = $conn->prepare(
    'INSERT INTO newsletter (email) VALUES (?)'
);

if (!$stmt) {
    $_SESSION['newsletter_message'] = 'Something went wrong. Please try again.';
    $_SESSION['newsletter_status'] = 'danger';
} else {
    $stmt->bind_param('s', $email);

    if ($stmt->execute()) {
        $_SESSION['newsletter_message'] = 'Thank you for subscribing!';
        $_SESSION['newsletter_status'] = 'success';
    } elseif ($stmt->errno === 1062) {
        $_SESSION['newsletter_message'] = 'This email is already subscribed.';
        $_SESSION['newsletter_status'] = 'info';
    } else {
        $_SESSION['newsletter_message'] = 'Subscription failed. Please try again.';
        $_SESSION['newsletter_status'] = 'danger';
    }

    $stmt->close();
}

header('Location: index.php');
exit;

