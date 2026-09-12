<?php

session_start();

require_once "config/db.php";

/* =========================
   LOGIN CHECK
========================= */

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = (int)$_SESSION["user_id"];

$success = "";
$error = "";

/* =========================
   GET CURRENT USER
========================= */

$stmt = $conn->prepare("
    SELECT id, name, email
    FROM users
    WHERE id = ?
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$user = $result->fetch_assoc();

/* =========================
   UPDATE PROFILE
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");

    $currentPassword = $_POST["current_password"] ?? "";
    $newPassword = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    /* =========================
       BASIC VALIDATION
    ========================= */

    if ($name === "") {

        $error = "Name cannot be empty.";

    } elseif ($email === "") {

        $error = "Email cannot be empty.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }

    /* =========================
       CHECK EMAIL
    ========================= */

    if ($error === "") {

        $stmtEmail = $conn->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            AND id != ?
        ");

        $stmtEmail->bind_param(
            "si",
            $email,
            $userId
        );

        $stmtEmail->execute();

        $emailResult = $stmtEmail->get_result();

        if ($emailResult->num_rows > 0) {

            $error = "This email address is already registered.";

        }
    }

    /* =========================
       PASSWORD VALIDATION
    ========================= */

    if (
        $error === "" &&
        (
            $currentPassword !== "" ||
            $newPassword !== "" ||
            $confirmPassword !== ""
        )
    ) {

        if ($currentPassword === "") {

            $error = "Enter your current password.";

        } elseif ($newPassword === "") {

            $error = "Enter your new password.";

        } elseif (strlen($newPassword) < 6) {

            $error = "New password must contain at least 6 characters.";

        } elseif ($newPassword !== $confirmPassword) {

            $error = "New password and confirm password do not match.";

        } else {

            /* Get current hashed password */

            $stmtPassword = $conn->prepare("
                SELECT password
                FROM users
                WHERE id = ?
            ");

            $stmtPassword->bind_param(
                "i",
                $userId
            );

            $stmtPassword->execute();

            $passwordResult = $stmtPassword->get_result();

            $passwordData = $passwordResult->fetch_assoc();

            if (
                !$passwordData ||
                !password_verify(
                    $currentPassword,
                    $passwordData["password"]
                )
            ) {

                $error = "Current password is incorrect.";

            }
        }
    }

    /* =========================
       SAVE CHANGES
    ========================= */

    if ($error === "") {

        if (
            $currentPassword !== "" &&
            $newPassword !== ""
        ) {

            $hashedPassword = password_hash(
                $newPassword,
                PASSWORD_DEFAULT
            );

            $stmtUpdate = $conn->prepare("
                UPDATE users
                SET
                    name = ?,
                    email = ?,
                    password = ?
                WHERE id = ?
            ");

            $stmtUpdate->bind_param(
                "sssi",
                $name,
                $email,
                $hashedPassword,
                $userId
            );

        } else {

            $stmtUpdate = $conn->prepare("
                UPDATE users
                SET
                    name = ?,
                    email = ?
                WHERE id = ?
            ");

            $stmtUpdate->bind_param(
                "ssi",
                $name,
                $email,
                $userId
            );
        }

        if ($stmtUpdate->execute()) {

            /* Update session name */

            $_SESSION["user_name"] = $name;

            /* Update displayed user data */

            $user["name"] = $name;
            $user["email"] = $email;

            $success = "Profile updated successfully.";

        } else {

            $error = "Unable to update profile. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>
    My Profile - Brar Pet Shop
</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    rel="stylesheet">

<style>

    body {
        background: #f8f9fa;
    }

    .profile-wrapper {
        max-width: 850px;
        margin: 60px auto;
    }

    .profile-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
    }

    .profile-header {
        background: linear-gradient(
            135deg,
            #0d6efd,
            #6610f2
        );
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 35px;
    }

    .profile-avatar {
        width: 85px;
        height: 85px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 42px;
        margin-bottom: 15px;
    }

    .form-control {
        padding: 12px 14px;
    }

    .section-title {
        font-weight: 700;
        margin-bottom: 20px;
    }

</style>


</head>

<body>

<div class="container">


<div class="profile-wrapper">

    <div class="card profile-card">

        <!-- =========================
             PROFILE HEADER
        ========================== -->

        <div class="profile-header">

            <div class="profile-avatar">

                <i class="bi bi-person-fill"></i>

            </div>

            <h2 class="fw-bold mb-1">

                My Profile

            </h2>

            <p class="mb-0 opacity-75">

                Manage your Brar Pet Shop account

            </p>

        </div>

        <div class="card-body p-4 p-md-5">

            <!-- =========================
                 ALERTS
            ========================== -->

            <?php if ($success !== ""): ?>

                <div class="alert alert-success">

                    <i class="bi bi-check-circle-fill me-2"></i>

                    <?php
                    echo htmlspecialchars($success);
                    ?>

                </div>

            <?php endif; ?>

            <?php if ($error !== ""): ?>

                <div class="alert alert-danger">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <?php
                    echo htmlspecialchars($error);
                    ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <!-- =========================
                     PERSONAL INFORMATION
                ========================== -->

                <h5 class="section-title">

                    <i class="bi bi-person me-2"></i>

                    Personal Information

                </h5>

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Full Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?php
                        echo htmlspecialchars($user["name"]);
                        ?>"
                        maxlength="100"
                        required>

                </div>

                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?php
                        echo htmlspecialchars($user["email"]);
                        ?>"
                        maxlength="150"
                        required>

                </div>

                <hr class="my-4">

                <!-- =========================
                     CHANGE PASSWORD
                ========================== -->

                <h5 class="section-title">

                    <i class="bi bi-shield-lock me-2"></i>

                    Change Password

                </h5>

                <p class="text-muted small">

                    Leave these fields empty if you do not want
                    to change your password.

                </p>

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Current Password
                    </label>

                    <input
                        type="password"
                        name="current_password"
                        class="form-control"
                        autocomplete="current-password">

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            New Password
                        </label>

                        <input
                            type="password"
                            name="new_password"
                            class="form-control"
                            minlength="6"
                            autocomplete="new-password">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Confirm New Password
                        </label>

                        <input
                            type="password"
                            name="confirm_password"
                            class="form-control"
                            minlength="6"
                            autocomplete="new-password">

                    </div>

                </div>

                <div class="alert alert-light border small">

                    <i class="bi bi-info-circle me-2"></i>

                    Password must contain at least 6 characters.

                </div>

                <!-- =========================
                     BUTTONS
                ========================== -->

                <div class="d-flex flex-column flex-md-row gap-2 mt-4">

                    <button
                        type="submit"
                        class="btn btn-primary px-4">

                        <i class="bi bi-check-lg me-2"></i>

                        Save Changes

                    </button>

                    <a
                        href="my_orders.php"
                        class="btn btn-outline-secondary">

                        <i class="bi bi-receipt me-2"></i>

                        My Orders

                    </a>

                    <a
                        href="index.php"
                        class="btn btn-outline-secondary">

                        <i class="bi bi-house me-2"></i>

                        Home

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>


</div>

</body>

</html>
