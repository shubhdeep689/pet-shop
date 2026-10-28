
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION["user_id"]);
$userName = $_SESSION["user_name"] ?? "";

?>

<!-- Sticky Navbar -->
<nav
    class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm"
    id="mainNavbar"
    data-bs-spy="scroll"
    data-bs-target="#mainNavbar">

    <div class="container-fluid">

        <!-- Logo -->
        <a
            class="navbar-brand d-flex align-items-center fw-bold"
            href="index.php">

            <img
                src="IMAGES/BRAR%20PET%20SHOP%20LOGO.jpeg"
                alt="Brar Pet Shop Logo"
                width="100"
                height="60"
                class="me-2 rounded-circle">

            <span>Brar Pet Shop <span class="brand-paw">🐾</span></span>
        </a>

        <!-- Hamburger Menu -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarContent"
            aria-controls="navbarContent"
            aria-expanded="false"
            aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div
            class="collapse navbar-collapse"
            id="navbarContent">

            <!-- Main Navigation Links -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-3">

                <!-- Home -->
                <li class="nav-item">
                    <a
                        class="nav-link active"
                        aria-current="page"
                        href="index.php">
                        Home
                    </a>
                </li>

                <!-- About -->
                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="about.php">
                        About
                    </a>
                </li>

                <!-- Pets -->
                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="pets.php">
                        Pets
                    </a>
                </li>

                <!-- Products -->
                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="product.php">
                        Products
                    </a>
                </li>

                <!-- Services -->
                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="index.php#services">
                        Services
                    </a>
                </li>

                <!-- Gallery -->
                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="gallery.php">
                        Gallery
                    </a>
                </li>

                <!-- Contact -->
                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="contact.php">
                        Contact
                    </a>
                </li>

                <!-- More Dropdown -->
                <li class="nav-item dropdown">

                    <a
                        class="nav-link dropdown-toggle"
                        href="#"
                        id="moreDropdown"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        More
                    </a>

                    <ul
                        class="dropdown-menu"
                        aria-labelledby="moreDropdown">

                        <!-- Pet Care Tips -->
                        <li>
                            <a
                                class="dropdown-item"
                                href="index.php#services">
                                <i class="bi bi-heart me-2"></i>
                                Pet Care Tips
                            </a>
                        </li>

                        <!-- Vaccination Guide -->
                        <li>
                            <a
                                class="dropdown-item"
                                href="index.php#services">
                                <i class="bi bi-shield-check me-2"></i>
                                Vaccination Guide
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <!-- Pet Adoption -->
                        <li>
                            <a
                                class="dropdown-item"
                                href="pets.php">
                                <i class="bi bi-hand-thumbs-up me-2"></i>
                                Pet Adoption
                            </a>
                        </li>

                        <!-- Pet Accessories -->
                        <li>
                            <a
                                class="dropdown-item"
                                href="product.php">
                                <i class="bi bi-bag me-2"></i>
                                Pet Accessories
                            </a>
                        </li>

                        <!-- Testimonials -->
                        <li>
                            <a
                                class="dropdown-item"
                                href="index.php#testimonials">
                                <i class="bi bi-chat-heart me-2"></i>
                                Testimonials
                            </a>
                        </li>

                        <!-- FAQ -->
                        <li>
                            <a
                                class="dropdown-item"
                                href="index.php#faq">
                                <i class="bi bi-question-circle me-2"></i>
                                FAQ
                            </a>
                        </li>

                    </ul>
                </li>

            </ul>

            <!-- Offcanvas Menu Toggle -->
            <button
                class="btn btn-outline-secondary btn-sm"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#sideMenu"
                aria-controls="sideMenu"
                title="Menu">

                <i class="bi bi-gear-fill d-lg-none"></i>
                <i class="bi bi-list d-none d-lg-inline"></i>

            </button>

        </div>
    </div>
</nav>


<!-- =========================================
     LEFT SIDE OFFCANVAS MENU
========================================= -->

<div
    class="offcanvas offcanvas-start"
    tabindex="-1"
    id="sideMenu"
    aria-labelledby="sideMenuLabel">

    <!-- Offcanvas Header -->
    <div class="offcanvas-header bg-primary text-white">

        <h5
            class="offcanvas-title"
            id="sideMenuLabel">
            Menu
        </h5>

        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas"
            aria-label="Close">
        </button>

    </div>

    <div class="offcanvas-body p-0">

        <!-- USER PROFILE AREA -->
        <div class="p-3 border-bottom">

            <div class="text-center">

                <img
                    src="IMAGES/BRAR%20PET%20SHOP%20LOGO.jpeg"
                    alt="Brar Pet Shop"
                    class="rounded-circle mb-2"
                    width="100"
                    height="60">

                <?php if ($isLoggedIn): ?>

                    <h6 class="mb-1">
                        Welcome,
                        <?php echo htmlspecialchars($userName); ?>
                    </h6>

                    <small class="text-success">
                        <i
                            class="bi bi-circle-fill"
                            style="font-size: 8px;">
                        </i>
                        Logged in
                    </small>

                <?php else: ?>

                    <h6 class="mb-1">
                        Welcome Guest
                    </h6>

                    <small class="text-muted">
                        Not logged in
                    </small>

                <?php endif; ?>

            </div>

        </div>


        <!-- QUICK ACTIONS -->
        <div class="p-3 border-bottom">

            <h6 class="text-primary fw-bold mb-3">
                Quick Actions
            </h6>

            <!-- Search -->
            <form
                class="d-flex mb-3"
                action="search.php"
                method="GET"
                role="search">

                <input
                    class="form-control form-control-sm me-2"
                    type="search"
                    name="q"
                    id="searchInput"
                    placeholder="Search pets & products"
                    aria-label="Search"
                    autocomplete="off"
                    required>

                <button
                    class="btn btn-primary btn-sm"
                    type="submit"
                    id="searchBtn"
                    title="Search">

                    <i class="bi bi-search"></i>

                </button>

            </form>


            <!-- Action Buttons -->
            <div class="d-grid gap-2">

                <!-- Dark / Light Mode -->
                <button
                    class="btn btn-outline-secondary btn-sm"
                    id="darkModeToggle"
                    type="button"
                    title="Toggle dark mode">

                    <i class="bi bi-moon-fill"></i>
                    Dark / Light Mode

                </button>


                <?php if ($isLoggedIn): ?>

                    <!-- My Profile -->
                    <a
                        href="profile.php"
                        class="btn btn-outline-primary btn-sm"
                        title="My Profile">

                        <i class="bi bi-person-circle"></i>
                        My Profile

                    </a>


                    <!-- My Orders -->
                    <a
                        href="my_orders.php"
                        class="btn btn-outline-success btn-sm"
                        title="My Orders">

                        <i class="bi bi-receipt"></i>
                        My Orders

                    </a>


                    <!-- Wishlist -->
                    <a
                        href="wishlist.php"
                        class="btn btn-outline-danger btn-sm"
                        title="Wishlist">

                        <i class="bi bi-heart-fill"></i>
                        Wishlist

                    </a>


                    <!-- Cart -->
                    <a
                        href="cart.php"
                        class="btn btn-warning btn-sm"
                        title="Shopping cart">

                        <i class="bi bi-cart-fill"></i>
                        Cart

                    </a>


                    <!-- Logout -->
                    <a
                        href="logout.php"
                        class="btn btn-danger btn-sm"
                        title="Logout">

                        <i class="bi bi-box-arrow-right"></i>
                        Logout

                    </a>

                <?php else: ?>

                    <!-- Login -->
                    <a
                        href="login.php"
                        class="btn btn-outline-primary btn-sm"
                        title="Login">

                        <i class="bi bi-person"></i>
                        Login

                    </a>


                    <!-- Register -->
                    <a
                        href="signup.php"
                        class="btn btn-primary btn-sm"
                        title="Register">

                        <i class="bi bi-user-plus"></i>
                        Register

                    </a>

                <?php endif; ?>

            </div>


            <!-- ADMIN LOGIN -->
            <div class="mt-3 pt-3 border-top">

                <a
                    href="admin/login.php"
                    class="btn btn-outline-dark btn-sm w-100"
                    title="Admin Login">

                    <i class="bi bi-shield-lock-fill me-1"></i>
                    Admin Login

                </a>

            </div>

        </div>


        <!-- NAVIGATION LINKS -->
        <ul class="list-unstyled p-3 border-bottom">

            <li class="mb-2">
                <a
                    href="index.php"
                    class="text-decoration-none text-dark">
                    <i class="bi bi-house me-2"></i>
                    Home
                </a>
            </li>

            <li class="mb-2">
                <a
                    href="about.php"
                    class="text-decoration-none text-dark">
                    <i class="bi bi-info-circle me-2"></i>
                    About Us
                </a>
            </li>

            <li class="mb-2">
                <a
                    href="pets.php"
                    class="text-decoration-none text-dark">
                    <i class="bi bi-heart-fill me-2"></i>
                    Pets
                </a>
            </li>

            <li class="mb-2">
                <a
                    href="product.php"
                    class="text-decoration-none text-dark">
                    <i class="bi bi-bag me-2"></i>
                    Products
                </a>
            </li>

            <li class="mb-2">
                <a
                    href="index.php#services"
                    class="text-decoration-none text-dark">
                    <i class="bi bi-gear me-2"></i>
                    Services
                </a>
            </li>

            <li class="mb-2">
                <a
                    href="gallery.php"
                    class="text-decoration-none text-dark">
                    <i class="bi bi-image me-2"></i>
                    Gallery
                </a>
            </li>

            <li class="mb-2">
                <a
                    href="index.php#testimonials"
                    class="text-decoration-none text-dark">
                    <i class="bi bi-chat-left-quote me-2"></i>
                    Testimonials
                </a>
            </li>

            <li class="mb-2">
                <a
                    href="index.php#faq"
                    class="text-decoration-none text-dark">
                    <i class="bi bi-question-circle me-2"></i>
                    FAQ
                </a>
            </li>

            <li class="mb-2">
                <a
                    href="contact.php"
                    class="text-decoration-none text-dark">
                    <i class="bi bi-telephone me-2"></i>
                    Contact
                </a>
            </li>

        </ul>


        <!-- CATEGORIES -->
        <div class="p-3 border-bottom">

            <h6 class="text-primary fw-bold mb-3">
                Categories
            </h6>

            <ul class="list-unstyled">

                <li class="mb-2">
                    <a
                        href="pets.php?category=dog"
                        class="text-decoration-none text-dark">
                        <i class="bi bi-heart-fill me-2"></i>
                        Dogs
                    </a>
                </li>

                <li class="mb-2">
                    <a
                        href="pets.php?category=cat"
                        class="text-decoration-none text-dark">
                        <i class="bi bi-heart-fill me-2"></i>
                        Cats
                    </a>
                </li>

                <li class="mb-2">
                    <a
                        href="pets.php?category=bird"
                        class="text-decoration-none text-dark">
                        <i class="bi bi-chat-right-dots me-2"></i>
                        Birds
                    </a>
                </li>

                <li class="mb-2">
                    <a
                        href="pets.php?category=fish"
                        class="text-decoration-none text-dark">
                        <i class="bi bi-water me-2"></i>
                        Fish
                    </a>
                </li>

                <li class="mb-2">
                    <a
                        href="pets.php?category=small"
                        class="text-decoration-none text-dark">
                        <i class="bi bi-heart me-2"></i>
                        Rabbits & Hamsters
                    </a>
                </li>

                <li class="mb-2">
                    <a
                        href="product.php"
                        class="text-decoration-none text-dark">
                        <i class="bi bi-cup-hot me-2"></i>
                        Pet Food
                    </a>
                </li>

                <li class="mb-2">
                    <a
                        href="product.php"
                        class="text-decoration-none text-dark">
                        <i class="bi bi-backpack me-2"></i>
                        Accessories
                    </a>
                </li>

            </ul>

        </div>


        <!-- CONTACT INFORMATION -->
        <div class="p-3 border-bottom">

            <h6 class="text-primary fw-bold mb-3">
                Contact Info
            </h6>

            <div class="mb-2">

                <small class="text-muted">
                    Phone
                </small>

                <br>

                <a
                    href="tel:+919779654617"
                    class="text-decoration-none text-dark">

                    <i class="bi bi-telephone"></i>
                    +91 97796 54617

                </a>

            </div>


            <div class="mb-2">

                <small class="text-muted">
                    Email
                </small>

                <br>

                <a
                    href="mailto:info@brarpetshop.com"
                    class="text-decoration-none text-dark">

                    <i class="bi bi-envelope"></i>
                    info@brarpetshop.com

                </a>

            </div>


            <div class="mb-2">

                <small class="text-muted">
                    Hours
                </small>

                <br>

                <small>
                    Mon-Sat: 9 AM - 6 PM
                    <br>
                    Sun: 10 AM - 4 PM
                </small>

            </div>

        </div>


        <!-- SOCIAL MEDIA -->
        <div class="p-3 text-center">

            <h6 class="text-primary fw-bold mb-3">
                Follow Us
            </h6>

            <a
                href="https://facebook.com"
                class="btn btn-outline-primary btn-sm mx-1"
                title="Facebook"
                data-bs-toggle="tooltip">

                <i class="bi bi-facebook"></i>

            </a>


            <a
                href="https://instagram.com"
                class="btn btn-outline-danger btn-sm mx-1"
                title="Instagram"
                data-bs-toggle="tooltip">

                <i class="bi bi-instagram"></i>

            </a>


            <a
                href="https://wa.me/919779654617"
                class="btn btn-outline-success btn-sm mx-1"
                title="WhatsApp"
                data-bs-toggle="tooltip">

                <i class="bi bi-whatsapp"></i>

            </a>


            <a
                href="https://youtube.com"
                class="btn btn-outline-danger btn-sm mx-1"
                title="YouTube"
                data-bs-toggle="tooltip">

                <i class="bi bi-youtube"></i>

            </a>


            <a
                href="https://twitter.com"
                class="btn btn-outline-info btn-sm mx-1"
                title="X (Twitter)"
                data-bs-toggle="tooltip">

                <i class="bi bi-twitter"></i>

            </a>

        </div>

    </div>

</div>

