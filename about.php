<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>about us</title>
     
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- AOS (Animate On Scroll) CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">

    <script>
    AOS.init({
        duration: 1000,
        once: true
    });
</script>
</head>
<body>
     <!-- inherting navbar -->
     <!--  <div id="navbar"></div> -->
    <?php include 'navbar.php'; ?>

      <!-- About Us Section -->
    <section id="about" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center g-4">
                <!-- Image -->
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="IMAGES/BRAR%20PET%20SHOP%20LOGO.jpeg" alt="About Brar Pet Shop" class="img-fluid rounded shadow">
                </div>
                
                <!-- Content -->
                <div class="col-lg-6" data-aos="fade-left">
                    <h2 class="display-5 fw-bold mb-3 text-primary">About Brar Pet Shop</h2>
                    <p class="lead mb-3"><strong>"Your Trusted Partner for Happy Pets"</strong></p>
                    <p class="mb-2">At Brar Pet Shop, we believe that pets are family. Since our establishment, we've been dedicated to providing:</p>
                    
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Healthy, well-cared-for pets</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Premium quality pet food and nutrition</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Wide range of pet accessories</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Professional grooming services</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Expert veterinary guidance</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Friendly and knowledgeable customer service</li>
                    </ul>
                    
                    <p>Our team of passionate pet lovers is committed to ensuring every pet finds the perfect home and receives the best care possible.</p>
                    
                   <a class="btn btn-primary btn-lg mt-3"
                            id="readMoreBtn"
                            data-bs-toggle="collapse"
                            href="#aboutMore">
                        <i class="bi bi-arrow-down-circle"></i> Read More
                    </a>

                    <div class="collapse mt-4" id="aboutMore">
    <div class="card card-body border-0 shadow-sm">
        <p>
            At Brar Pet Shop, our mission is to provide loving homes for pets and
            quality products for pet owners. We carefully select our pets and products
            to ensure the highest standards of health and care.
        </p>

        <p>
            We also offer expert guidance on pet nutrition, grooming, vaccinations,
            and daily care to help your furry friends live happy and healthy lives.
        </p>

        <p class="mb-0">
            Thank you for trusting Brar Pet Shop as your partner in your pet parenting journey.
        </p>
    </div>
</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Who We Are, Mission & Why Choose Us -->
<section class="py-5 bg-white">
    <div class="container">

        <!-- Section Heading -->
        <div class="text-center mb-5">
            <h2 class="fw-bold text-primary">Why Brar Pet Shop?</h2>
            <p class="text-muted">
                Passionate about pets and committed to providing the best care.
            </p>
        </div>

        <div class="row g-4">

            <!-- Who We Are -->
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill display-4 text-primary mb-3"></i>

                        <h4 class="fw-bold">Who We Are</h4>

                        <p class="text-muted">
                            Brar Pet Shop is a trusted destination for pet lovers.
                            We provide healthy pets, quality products, and expert
                            guidance to help every pet owner give the best life to
                            their furry friends.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Our Mission -->
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="bi bi-bullseye display-4 text-success mb-3"></i>

                        <h4 class="fw-bold">Our Mission</h4>

                        <p class="text-muted">
                            Our mission is to ensure every pet receives love,
                            proper care, nutritious food, and a safe environment
                            while helping families find their perfect companion.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Why Choose Us -->
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-center">
                            <i class="bi bi-star-fill display-4 text-warning mb-3"></i>

                            <h4 class="fw-bold">Why Choose Us?</h4>
                        </div>

                        <ul class="list-unstyled mt-4">
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                Healthy and well-cared-for pets
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                Premium pet food and accessories
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                Friendly and knowledgeable staff
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                Affordable prices
                            </li>

                            <li>
                                <i class="bi bi-check-circle-fill text-success"></i>
                                Trusted by hundreds of pet lovers
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
     <!-- inherting footer -->
     <!--  <div id="footer"></div> -->
     <?php include 'footer.php'; ?>
 <!-- Back to Top Button -->
    <button id="backToTopBtn" class="btn btn-primary rounded-circle position-fixed" title="Back to top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS (Animate On Scroll) JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom Script -->
    <script src="script.js"></script>
</body>
</html>