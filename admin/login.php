<?php

session_start();

require_once "../config/db.php";

$message = "";
$messageType = "";

if (isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $message = "Please enter username and password.";
        $messageType = "danger";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, username, password
             FROM admins
             WHERE username = ?"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin["password"])) {

                $_SESSION["admin_id"] = $admin["id"];
                $_SESSION["admin_username"] = $admin["username"];

                header("Location: index.php");
                exit();

            } else {

                $message = "Invalid username or password.";
                $messageType = "danger";
            }

        } else {

            $message = "Invalid username or password.";
            $messageType = "danger";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Login - Brar Pet Shop</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>

        body {
            min-height: 100vh;
            background: linear-gradient(
                135deg,
                #0d6efd,
                #6610f2
            );

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .login-header {
            background: #fff;
            padding: 35px 30px 20px;
            text-align: center;
        }

        .admin-icon {
            width: 75px;
            height: 75px;
            margin: auto;
            border-radius: 50%;
            background: #0d6efd;
            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 32px;
        }

        .login-body {
            padding: 30px;
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 10px;
        }

        .btn-login {
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
        }

    </style>

</head>

<body>

<div class="card shadow-lg login-card">

    <div class="login-header">

        <div class="admin-icon mb-3">
            <i class="bi bi-shield-lock"></i>
        </div>

        <h3 class="fw-bold mb-1">
            Brar Pet Shop
        </h3>

        <p class="text-muted mb-0">
            Admin Panel
        </p>

    </div>

    <div class="login-body">

        <?php if ($message !== ""): ?>

            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Username
                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>

                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        placeholder="Enter admin username"
                        required
                    >

                </div>

            </div>

            <div class="mb-4">

                <label class="form-label fw-semibold">
                    Password
                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter admin password"
                        required
                    >

                </div>

            </div>

            <button
                type="submit"
                class="btn btn-primary btn-login w-100"
            >

                <i class="bi bi-box-arrow-in-right me-2"></i>

                Login to Dashboard

            </button>

        </form>

        <div class="text-center mt-4">

            <a
                href="../index.php"
                class="text-decoration-none"
            >

                <i class="bi bi-arrow-left me-1"></i>
                Back to Website

            </a>

        </div>

    </div>

</div>

</body>

</html>