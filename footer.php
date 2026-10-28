 <!-- Footer -->
    <footer class="bg-dark text-light py-5">
        <div class="container">
            <div class="row g-4 mb-4">
                <!-- Column 1: About -->
                <div class="col-lg-3 col-md-6 ">
                    <h6 class="text-primary fw-bold mb-3">About Brar Pet Shop</h6>
                    <p class="text ">Your trusted partner for happy pets. We provide premium quality pets, accessories, and professional pet care services.</p>
                    <div class="gap-2 d-flex mt-3">
                        <a href="https://facebook.com" class="btn btn-outline-light btn-sm" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://instagram.com" class="btn btn-outline-light btn-sm" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="https://wa.me/+91977965617" class="btn btn-outline-light btn-sm" title="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        <a href="https://youtube.com" class="btn btn-outline-light btn-sm" title="YouTube">
                            <i class="bi bi-youtube"></i>
                        </a>
                        <a href="https://twitter.com" class="btn btn-outline-light btn-sm" title="X (Twitter)">
                            <i class="bi bi-twitter"></i>
                        </a>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="col-lg-3  col-md-6">
                    <h6 class="text-primary fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled  p-3 rounded">
                        <li class="mb-2 "><a href="#home" class="text-white text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="#about" class="text-white text-decoration-none   ">About Us</a></li>
                        <li class="mb-2"><a href="#pets" class="text-white text-decoration-none">Pets</a></li>
                        <li class="mb-2"><a href="#products" class="text-white text-decoration-none">Products</a></li>
                        <li class="mb-2"><a href="#services" class="text-white text-decoration-none">Services</a></li>
                        <li class="mb-2"><a href="#contact" class="text-white text-decoration-none">Contact</a></li>
                    </ul>
                </div>

                <!-- Column 3: Services -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-primary fw-bold mb-3">Services</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Pet Grooming</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Vaccination</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Health Consultation</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Pet Training</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Pet Boarding</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Home Delivery</a></li>
                    </ul>
                </div>
                
<?php if (isset($_SESSION['newsletter_message'])): ?>
    <div class="alert alert-<?php
        echo htmlspecialchars($_SESSION['newsletter_status']);
    ?> alert-dismissible fade show" role="alert">

        <?php
        echo htmlspecialchars($_SESSION['newsletter_message']);
        ?>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"></button>
    </div>
    <?php
    unset($_SESSION['newsletter_message'], $_SESSION['newsletter_status']);
    ?>
<?php endif; ?>
<!-- Column 4: Newsletter -->
<div class="col-lg-3 col-md-6">
    <h6 class="text-primary fw-bold mb-3">Newsletter</h6>

    <p class="text mb-3">
        Subscribe to our newsletter to get updates on new pets and products.
    </p>

    <form class="input-group" action="subscribe.php" method="POST">
        <input
            type="email"
            name="email"
            class="form-control"
            placeholder="Your email"
            aria-label="Your email"
            required
        >

        <button class="btn btn-primary" type="submit">
            Subscribe
        </button>
    </form>
</div>


            </div>

            <!-- Footer Bottom -->
            <hr class="bg-secondary">
            <div class="row">
                <div class="col-md-6">
                    <p class="text mb-0">&copy; 2024 Brar Pet Shop. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                        <a href="#" class="texttext-decoration-none">Privacy Policy</a> |
                        <a href="#" class="text text-decoration-none">Terms of Service</a> |
                        <a href="#" class="text text-decoration-none">Cookie Policy</a>
                    </small>
                </div>
            </div>
        </div>
    </footer>