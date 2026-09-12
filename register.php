<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create your Brar Pet Shop account.">
    <title>Register | Brar Pet Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
      <!-- inherting navbar -->
     <!--  <div id="navbar"></div> -->
    <?php include 'navbar.php'; ?>

    <main class="auth-shell">
        <section class="auth-panel auth-panel-copy" aria-labelledby="registerHeading">
            <div class="auth-brand-mark"><i class="bi bi-stars"></i></div>
            <p class="auth-eyebrow">Join the family</p>
            <h1 id="registerHeading">More care for every companion.</h1>
            <p class="auth-copy">Create an account and make your next visit to Brar Pet Shop simple.</p>
            <div class="auth-detail"><i class="bi bi-bag-heart"></i><span>Keep your pet and product choices together.</span></div>
            <div class="auth-detail"><i class="bi bi-chat-heart"></i><span>Reach our team quickly when you need us.</span></div>
        </section>

        <section class="auth-panel auth-panel-form" aria-labelledby="registerFormHeading">
            <div class="auth-form-heading">
                <p class="auth-eyebrow">New account</p>
                <h2 id="registerFormHeading">Create account</h2>
                <p class="text-muted">It only takes a minute.</p>
            </div>
            <div id="registerMessage" class="auth-page-message d-none" role="alert"></div>
            <form id="registerPageForm">
                <div class="mb-3">
                    <label for="registerName" class="form-label">Full name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="registerName" required autocomplete="name" placeholder="Your full name">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="registerEmail" class="form-label">Email address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="registerEmail" required autocomplete="email" placeholder="you@example.com">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="registerPassword" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="registerPassword" required minlength="6" autocomplete="new-password" placeholder="At least 6 characters">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="registerConfirmPassword" class="form-label">Confirm password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-check2-circle"></i></span>
                        <input type="password" class="form-control" id="registerConfirmPassword" required minlength="6" autocomplete="new-password" placeholder="Repeat your password">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 auth-submit"><i class="bi bi-person-plus me-2"></i>Create account</button>
            </form>
            <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
        </section>
    </main>

     <!-- inherting footer -->
     <!--  <div id="footer"></div> -->
     <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
