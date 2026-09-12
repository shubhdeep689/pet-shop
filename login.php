```php
<?php

session_start();
require_once "config/db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $message = "Please enter your email and password.";
        $messageType = "danger";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password FROM users WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];

                header("Location: index.php");
                exit();

            } else {

                $message = "Incorrect email or password.";
                $messageType = "danger";
            }

        } else {

            $message = "Incorrect email or password.";
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

    <meta name="description"
          content="Login to Brar Pet Shop">

    <title>Login | Brar Pet Shop</title>


    <!-- Bootstrap -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet">


    <!-- Bootstrap Icons -->

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">


    <!-- Main CSS -->

    <link rel="stylesheet"
          href="style.css">


    <style>

        /* ================================
           LOGIN PAGE
        ================================= */

        body.auth-page {
            background:
                radial-gradient(circle at top left,
                    rgba(13, 110, 253, 0.12),
                    transparent 35%),
                radial-gradient(circle at bottom right,
                    rgba(25, 135, 84, 0.12),
                    transparent 35%),
                #f6f8fb;

            min-height: 100vh;
        }


        .login-wrapper {
            min-height: calc(100vh - 150px);

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 60px 20px;
        }


        .login-card {
            width: 100%;
            max-width: 1050px;

            background: #ffffff;

            border-radius: 28px;

            overflow: hidden;

            box-shadow:
                0 25px 70px rgba(0, 0, 0, 0.12);

            display: grid;

            grid-template-columns: 1fr 1fr;
        }


        /* ================================
           LEFT SIDE
        ================================= */

        .login-welcome {
            position: relative;

            padding: 55px 50px;

            background:
                linear-gradient(
                    135deg,
                    #0d6efd,
                    #0b5ed7
                );

            color: white;

            display: flex;
            flex-direction: column;
            justify-content: center;

            overflow: hidden;
        }


        .login-welcome::before {
            content: "";

            position: absolute;

            width: 260px;
            height: 260px;

            border-radius: 50%;

            background: rgba(255,255,255,0.08);

            top: -100px;
            right: -80px;
        }


        .login-welcome::after {
            content: "";

            position: absolute;

            width: 180px;
            height: 180px;

            border-radius: 50%;

            background: rgba(255,255,255,0.07);

            bottom: -80px;
            left: -60px;
        }


        .brand-icon {
            width: 72px;
            height: 72px;

            border-radius: 22px;

            background: rgba(255,255,255,0.16);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 32px;

            margin-bottom: 25px;

            backdrop-filter: blur(10px);

            position: relative;
            z-index: 2;
        }


        .welcome-content {
            position: relative;
            z-index: 2;
        }


        .welcome-label {
            font-size: 13px;

            text-transform: uppercase;

            letter-spacing: 2px;

            font-weight: 700;

            opacity: 0.85;

            margin-bottom: 10px;
        }


        .welcome-title {
            font-size: 42px;

            line-height: 1.15;

            font-weight: 800;

            margin-bottom: 18px;
        }


        .welcome-text {
            font-size: 16px;

            line-height: 1.7;

            opacity: 0.9;

            max-width: 420px;

            margin-bottom: 30px;
        }


        .welcome-features {
            display: flex;

            flex-direction: column;

            gap: 14px;
        }


        .welcome-feature {
            display: flex;

            align-items: center;

            gap: 12px;

            font-size: 14px;
        }


        .welcome-feature-icon {
            width: 34px;
            height: 34px;

            border-radius: 10px;

            background: rgba(255,255,255,0.15);

            display: flex;

            align-items: center;
            justify-content: center;
        }


        /* ================================
           RIGHT SIDE
        ================================= */

        .login-form-section {
            padding: 55px 55px;

            display: flex;
            flex-direction: column;
            justify-content: center;
        }


        .login-heading {
            margin-bottom: 30px;
        }


        .login-small-title {
            color: #0d6efd;

            font-size: 13px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 1.5px;

            margin-bottom: 8px;
        }


        .login-heading h2 {
            font-size: 34px;

            font-weight: 800;

            margin-bottom: 8px;

            color: #212529;
        }


        .login-heading p {
            margin: 0;

            color: #6c757d;
        }


        /* ================================
           ALERT
        ================================= */

        .login-alert {
            border: 0;

            border-radius: 12px;

            font-size: 14px;

            padding: 12px 15px;

            margin-bottom: 22px;
        }


        /* ================================
           INPUTS
        ================================= */

        .login-label {
            font-weight: 600;

            font-size: 14px;

            margin-bottom: 8px;
        }


        .login-input-group {
            border: 1px solid #dee2e6;

            border-radius: 12px;

            overflow: hidden;

            transition: 0.2s;
        }


        .login-input-group:focus-within {
            border-color: #0d6efd;

            box-shadow:
                0 0 0 4px rgba(13,110,253,0.10);
        }


        .login-input-group .input-group-text {
            border: 0;

            background: #f8f9fa;

            color: #6c757d;

            padding-left: 15px;

            padding-right: 12px;
        }


        .login-input-group .form-control {
            border: 0;

            box-shadow: none;

            padding: 13px 12px;

            font-size: 14px;
        }


        .login-input-group .form-control:focus {
            box-shadow: none;
        }


        .password-toggle {
            border: 0;

            background: #ffffff;

            color: #6c757d;

            padding: 0 15px;

            cursor: pointer;
        }


        .password-toggle:hover {
            color: #0d6efd;
        }


        /* ================================
           LOGIN BUTTON
        ================================= */

        .login-btn {
            border: 0;

            border-radius: 12px;

            padding: 13px;

            font-weight: 700;

            font-size: 15px;

            background: linear-gradient(
                135deg,
                #0d6efd,
                #0b5ed7
            );

            box-shadow:
                0 8px 20px rgba(13,110,253,0.22);

            transition: 0.25s;
        }


        .login-btn:hover {
            transform: translateY(-2px);

            box-shadow:
                0 12px 25px rgba(13,110,253,0.30);
        }


        /* ================================
           SIGNUP
        ================================= */

        .signup-link {
            text-align: center;

            margin-top: 25px;

            font-size: 14px;

            color: #6c757d;
        }


        .signup-link a {
            color: #0d6efd;

            font-weight: 700;

            text-decoration: none;
        }


        .signup-link a:hover {
            text-decoration: underline;
        }


        /* ================================
           RESPONSIVE
        ================================= */

        @media (max-width: 850px) {

            .login-card {
                grid-template-columns: 1fr;
            }

            .login-welcome {
                padding: 40px 35px;
            }

            .welcome-title {
                font-size: 34px;
            }

            .login-form-section {
                padding: 40px 35px;
            }

        }


        @media (max-width: 500px) {

            .login-wrapper {
                padding: 30px 15px;
            }

            .login-card {
                border-radius: 20px;
            }

            .login-welcome {
                padding: 35px 25px;
            }

            .login-form-section {
                padding: 35px 25px;
            }

            .welcome-title {
                font-size: 30px;
            }

            .login-heading h2 {
                font-size: 28px;
            }

        }

    </style>

</head>


<body class="auth-page">


<?php include 'navbar.php'; ?>


<!-- ================================
     LOGIN
================================= -->

<main class="login-wrapper">


    <div class="login-card">


        <!-- ============================
             LEFT
        ============================= -->

        <section class="login-welcome">


            <div class="welcome-content">


                <div class="brand-icon">

                    <i class="bi bi-heart-fill"></i>

                </div>


                <div class="welcome-label">

                    Brar Pet Shop

                </div>


                <h1 class="welcome-title">

                    Welcome back! 🐾

                </h1>


                <p class="welcome-text">

                    Your pets deserve the best.
                    Log in to manage your cart and continue shopping.

                </p>


                <div class="welcome-features">


                    <div class="welcome-feature">

                        <span class="welcome-feature-icon">

                            <i class="bi bi-shield-check"></i>

                        </span>

                        <span>
                            Safe & secure account
                        </span>

                    </div>


                    <div class="welcome-feature">

                        <span class="welcome-feature-icon">

                            <i class="bi bi-cart-check"></i>

                        </span>

                        <span>
                            Save your shopping cart
                        </span>

                    </div>


                    <div class="welcome-feature">

                        <span class="welcome-feature-icon">

                            <i class="bi bi-whatsapp"></i>

                        </span>

                        <span>
                            Easy WhatsApp ordering
                        </span>

                    </div>


                </div>


            </div>


        </section>



        <!-- ============================
             RIGHT
        ============================= -->

        <section class="login-form-section">


            <div class="login-heading">

                <div class="login-small-title">

                    Your Account

                </div>


                <h2>

                    Log in

                </h2>


                <p>

                    Enter your account details.

                </p>

            </div>



            <!-- ============================
                 MESSAGE
            ============================= -->

            <?php if ($message !== ""): ?>

                <div class="alert alert-<?php echo $messageType; ?> login-alert"
                     role="alert">

                    <i class="bi bi-exclamation-circle me-2"></i>

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>



            <!-- ============================
                 FORM
            ============================= -->

            <form method="POST" action="">


                <!-- Email -->

                <div class="mb-4">


                    <label for="loginEmail"
                           class="form-label login-label">

                        Email Address

                    </label>


                    <div class="input-group login-input-group">


                        <span class="input-group-text">

                            <i class="bi bi-envelope"></i>

                        </span>


                        <input
                            type="email"
                            class="form-control"
                            id="loginEmail"
                            name="email"
                            required
                            autocomplete="email"
                            placeholder="Enter your email"
                            value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
                        >


                    </div>


                </div>



                <!-- Password -->

                <div class="mb-4">


                    <label for="loginPassword"
                           class="form-label login-label">

                        Password

                    </label>


                    <div class="input-group login-input-group">


                        <span class="input-group-text">

                            <i class="bi bi-lock"></i>

                        </span>


                        <input
                            type="password"
                            class="form-control"
                            id="loginPassword"
                            name="password"
                            required
                            minlength="6"
                            autocomplete="current-password"
                            placeholder="Enter your password"
                        >


                        <button
                            type="button"
                            class="password-toggle"
                            id="togglePassword"
                            aria-label="Show password"
                        >

                            <i class="bi bi-eye"></i>

                        </button>


                    </div>


                </div>



                <!-- Login -->

                <button type="submit"
                        class="btn btn-primary w-100 login-btn">


                    <i class="bi bi-box-arrow-in-right me-2"></i>


                    Log in


                </button>


            </form>



            <!-- Signup -->

            <div class="signup-link">


                New to Brar Pet Shop?


                <a href="signup.php">

                    Create an account

                </a>


            </div>


        </section>


    </div>


</main>



<?php include 'footer.php'; ?>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>


<script>

    // Show / Hide Password

    const togglePassword =
        document.getElementById("togglePassword");

    const password =
        document.getElementById("loginPassword");


    togglePassword.addEventListener("click", function () {

        const isPassword =
            password.getAttribute("type") === "password";


        password.se
```
