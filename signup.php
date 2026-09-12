
<?php

session_start();
require_once "config/db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || $email === "" || $password === "") {

        $message = "Please fill in all fields.";
        $messageType = "danger";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $messageType = "danger";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $messageType = "danger";

    } else {

        // Check whether email already exists
        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "This email is already registered.";
            $messageType = "warning";

        } else {

            // Securely hash password
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $hashedPassword
            );

            if ($stmt->execute()) {

                $message = "Account created successfully! You can now login.";
                $messageType = "success";

                // Clear form values
                $_POST = [];

            } else {

                $message = "Unable to create account.";
                $messageType = "danger";
            }

            $stmt->close();
        }

        $check->close();
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
          content="Create your Brar Pet Shop account.">

    <title>Sign Up | Brar Pet Shop</title>


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

        /* =================================
           SIGNUP PAGE
        ================================= */

        body.auth-page {

            background:
                radial-gradient(
                    circle at top left,
                    rgba(13, 110, 253, 0.12),
                    transparent 35%
                ),
                radial-gradient(
                    circle at bottom right,
                    rgba(25, 135, 84, 0.12),
                    transparent 35%
                ),
                #f6f8fb;

            min-height: 100vh;
        }


        .signup-wrapper {

            min-height: calc(100vh - 150px);

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 55px 20px;
        }


        .signup-card {

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


        /* =================================
           LEFT PANEL
        ================================= */

        .signup-welcome {

            position: relative;

            padding: 55px 50px;

            background:
                linear-gradient(
                    135deg,
                    #198754,
                    #157347
                );

            color: #ffffff;

            display: flex;

            flex-direction: column;

            justify-content: center;

            overflow: hidden;
        }


        .signup-welcome::before {

            content: "";

            position: absolute;

            width: 280px;

            height: 280px;

            border-radius: 50%;

            background: rgba(255,255,255,0.08);

            top: -120px;

            right: -90px;
        }


        .signup-welcome::after {

            content: "";

            position: absolute;

            width: 190px;

            height: 190px;

            border-radius: 50%;

            background: rgba(255,255,255,0.07);

            bottom: -80px;

            left: -60px;
        }


        .signup-content {

            position: relative;

            z-index: 2;
        }


        .signup-brand-icon {

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
        }


        .signup-label {

            font-size: 13px;

            text-transform: uppercase;

            letter-spacing: 2px;

            font-weight: 700;

            opacity: 0.85;

            margin-bottom: 10px;
        }


        .signup-title {

            font-size: 40px;

            line-height: 1.15;

            font-weight: 800;

            margin-bottom: 18px;
        }


        .signup-text {

            font-size: 16px;

            line-height: 1.7;

            opacity: 0.9;

            max-width: 420px;

            margin-bottom: 30px;
        }


        /* =================================
           FEATURES
        ================================= */

        .signup-features {

            display: flex;

            flex-direction: column;

            gap: 14px;
        }


        .signup-feature {

            display: flex;

            align-items: center;

            gap: 12px;

            font-size: 14px;
        }


        .signup-feature-icon {

            width: 34px;

            height: 34px;

            border-radius: 10px;

            background: rgba(255,255,255,0.15);

            display: flex;

            align-items: center;

            justify-content: center;

            flex-shrink: 0;
        }


        /* =================================
           RIGHT FORM
        ================================= */

        .signup-form-section {

            padding: 55px;

            display: flex;

            flex-direction: column;

            justify-content: center;
        }


        .signup-heading {

            margin-bottom: 28px;
        }


        .signup-small-title {

            color: #198754;

            font-size: 13px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 1.5px;

            margin-bottom: 8px;
        }


        .signup-heading h2 {

            font-size: 34px;

            font-weight: 800;

            margin-bottom: 8px;

            color: #212529;
        }


        .signup-heading p {

            margin: 0;

            color: #6c757d;
        }


        /* =================================
           ALERT
        ================================= */

        .signup-alert {

            border: 0;

            border-radius: 12px;

            font-size: 14px;

            padding: 12px 15px;

            margin-bottom: 22px;
        }


        /* =================================
           FORM LABEL
        ================================= */

        .signup-label-form {

            font-weight: 600;

            font-size: 14px;

            margin-bottom: 8px;
        }


        /* =================================
           INPUT
        ================================= */

        .signup-input-group {

            border: 1px solid #dee2e6;

            border-radius: 12px;

            overflow: hidden;

            transition: 0.2s;
        }


        .signup-input-group:focus-within {

            border-color: #198754;

            box-shadow:
                0 0 0 4px rgba(25,135,84,0.10);
        }


        .signup-input-group .input-group-text {

            border: 0;

            background: #f8f9fa;

            color: #6c757d;

            padding-left: 15px;

            padding-right: 12px;
        }


        .signup-input-group .form-control {

            border: 0;

            box-shadow: none;

            padding: 13px 12px;

            font-size: 14px;
        }


        .signup-input-group .form-control:focus {

            box-shadow: none;
        }


        /* =================================
           PASSWORD TOGGLE
        ================================= */

        .password-toggle {

            border: 0;

            background: #ffffff;

            color: #6c757d;

            padding: 0 15px;

            cursor: pointer;
        }


        .password-toggle:hover {

            color: #198754;
        }


        /* =================================
           PASSWORD STRENGTH
        ================================= */

        .password-strength {

            font-size: 12px;

            margin-top: 7px;

            color: #6c757d;
        }


        /* =================================
           CREATE ACCOUNT BUTTON
        ================================= */

        .signup-btn {

            border: 0;

            border-radius: 12px;

            padding: 13px;

            font-weight: 700;

            font-size: 15px;

            background:
                linear-gradient(
                    135deg,
                    #198754,
                    #157347
                );

            box-shadow:
                0 8px 20px rgba(25,135,84,0.22);

            transition: 0.25s;
        }


        .signup-btn:hover {

            transform: translateY(-2px);

            box-shadow:
                0 12px 25px rgba(25,135,84,0.30);
        }


        /* =================================
           LOGIN LINK
        ================================= */

        .login-link {

            text-align: center;

            margin-top: 25px;

            font-size: 14px;

            color: #6c757d;
        }


        .login-link a {

            color: #198754;

            font-weight: 700;

            text-decoration: none;
        }


        .login-link a:hover {

            text-decoration: underline;
        }


        /* =================================
           RESPONSIVE
        ================================= */

        @media (max-width: 850px) {

            .signup-card {

                grid-template-columns: 1fr;
            }


            .signup-welcome {

                padding: 40px 35px;
            }


            .signup-title {

                font-size: 34px;
            }


            .signup-form-section {

                padding: 40px 35px;
            }

        }


        @media (max-width: 500px) {

            .signup-wrapper {

                padding: 30px 15px;
            }


            .signup-card {

                border-radius: 20px;
            }


            .signup-welcome {

                padding: 35px 25px;
            }


            .signup-form-section {

                padding: 35px 25px;
            }


            .signup-title {

                font-size: 30px;
            }


            .signup-heading h2 {

                font-size: 28px;
            }

        }

    </style>

</head>


<body class="auth-page">


<?php include 'navbar.php'; ?>


<!-- =================================
     SIGNUP
================================= -->

<main class="signup-wrapper">


    <div class="signup-card">


        <!-- =================================
             LEFT SIDE
        ================================= -->

        <section class="signup-welcome">


            <div class="signup-content">


                <div class="signup-brand-icon">

                    <i class="bi bi-paw-fill"></i>

                </div>


                <div class="signup-label">

                    Brar Pet Shop

                </div>


                <h1 class="signup-title">

                    Start your pet journey. 🐾

                </h1>


                <p class="signup-text">

                    Create your account and make
                    shopping for your pets simple and convenient.

                </p>


                <div class="signup-features">


                    <div class="signup-feature">

                        <span class="signup-feature-icon">

                            <i class="bi bi-person-check"></i>

                        </span>

                        <span>

                            Personal shopping account

                        </span>

                    </div>


                    <div class="signup-feature">

                        <span class="signup-feature-icon">

                            <i class="bi bi-cart-check"></i>

                        </span>

                        <span>

                            Save pets and products in your cart

                        </span>

                    </div>


                    <div class="signup-feature">

                        <span class="signup-feature-icon">

                            <i class="bi bi-whatsapp"></i>

                        </span>

                        <span>

                            Easy WhatsApp ordering

                        </span>

                    </div>


                </div>


            </div>


        </section>



        <!-- =================================
             RIGHT SIDE
        ================================= -->

        <section class="signup-form-section">


            <div class="signup-heading">


                <div class="signup-small-title">

                    Your Account

                </div>


                <h2>

                    Create Account

                </h2>


                <p>

                    Join Brar Pet Shop today.

                </p>


            </div>



            <!-- =================================
                 MESSAGE
            ================================= -->

            <?php if ($message !== ""): ?>

                <div class="alert alert-<?php echo $messageType; ?> signup-alert"
                     role="alert">

                    <?php if ($messageType === "success"): ?>

                        <i class="bi bi-check-circle me-2"></i>

                    <?php else: ?>

                        <i class="bi bi-exclamation-circle me-2"></i>

                    <?php endif; ?>


                    <?php echo htmlspecialchars($message); ?>


                    <?php if ($messageType === "success"): ?>

                        <div class="mt-2">

                            <a href="login.php"
                               class="btn btn-sm btn-success">

                                Go to Login

                            </a>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endif; ?>



            <!-- =================================
                 FORM
            ================================= -->

            <form method="POST" action="">


                <!-- Name -->

                <div class="mb-3">


                    <label for="signupName"
                           class="form-label signup-label-form">

                        Full Name

                    </label>


                    <div class="input-group signup-input-group">


                        <span class="input-group-text">

                            <i class="bi bi-person"></i>

                        </span>


                        <input
                            type="text"
                            class="form-control"
                            id="signupName"
                            name="name"
                            required
                            autocomplete="name"
                            placeholder="Enter your full name"
                            value="<?php echo htmlspecialchars($_POST["name"] ?? ""); ?>"
                        >


                    </div>


                </div>



                <!-- Email -->

                <div class="mb-3">


                    <label for="signupEmail"
                           class="form-label signup-label-form">

                        Email Address

                    </label>


                    <div class="input-group signup-input-group">


                        <span class="input-group-text">

                            <i class="bi bi-envelope"></i>

                        </span>


                        <input
                            type="email"
                            class="form-control"
                            id="signupEmail"
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


                    <label for="signupPassword"
                           class="form-label signup-label-form">

                        Password

                    </label>


                    <div class="input-group signup-input-group">


                        <span class="input-group-text">

                            <i class="bi bi-lock"></i>

                        </span>


                        <input
                            type="password"
                            class="form-control"
                            id="signupPassword"
                            name="password"
                            required
                            minlength="6"
                            autocomplete="new-password"
                            placeholder="At least 6 characters"
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


                    <div class="password-strength">

                        <i class="bi bi-shield-check me-1"></i>

                        Minimum 6 characters

                    </div>


                </div>



                <!-- Create Account -->

                <button type="submit"
                        class="btn btn-success w-100 signup-btn">


                    <i class="bi bi-person-plus me-2"></i>


                    Create Account


                </button>


            </form>



            <!-- Login -->

            <div class="login-link">


                Already have an account?


                <a href="login.php">

                    Log in

                </a>


            </div>


        </section>


    </div>


</main>



<?php include 'footer.php'; ?>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>


<script>

    // ================================
    // SHOW / HIDE PASSWORD
    // ================================

    const togglePassword =
        document.getElementById("togglePassword");

    const password =
        document.getElementById("signupPassword");


    togglePassword.addEventListener("click", function () {

        const isPassword =
            password.getAttribute("type") === "password";


        password.setAttribute(
            "type",
            isPassword ? "text" : "password"
        );


        this.innerHTML = isPassword
            ? '<i class="bi bi-eye-slash"></i>'
            : '<i class="bi bi-eye"></i>';

    });

</script>


</body>

</html>

