<?php
require_once "config/db.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Brar Pet Shop - Your Trusted Partner for Happy Pets. Premium pets, accessories, and professional pet care services.">
    <meta name="keywords" content="pet shop, dogs, cats, birds, fish, pet accessories, grooming, veterinary">
    <meta name="author" content="Brar Pet Shop">
    
    <title>Brar Pet Shop - Your Trusted Partner for Happy Pets</title>
    
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- AOS (Animate On Scroll) CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

      <!-- inherting navbar -->
     <!--  <div id="navbar"></div> -->
    <?php include 'navbar.php'; ?>

    <!-- Hero Carousel Section -->
    <section id="home" class="hero-carousel">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            <!-- Carousel Indicators -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
            </div>

            <!-- Carousel Inner -->
            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active" style="background-image: url('IMAGES\\BG_WHITE-PETS.jpeg'); background-size: cover; background-position: center; height: 500px;">
                    <div class="carousel-overlay"></div>
                    <div class="carousel-content">
                        <h1 class="display-4 fw-bold mb-3">Welcome to Brar Pet Shop</h1>
                        <p class="lead mb-4">Find your perfect companion and everything your pets need.</p>
                        <div class="gap-2 d-flex justify-content-center flex-wrap">
                            <a href="#pets" class="btn btn-primary btn-lg"><i class="bi bi-paw"></i> Explore Pets</a>
                            <a href="#contact" class="btn btn-outline-light btn-lg"><i class="bi bi-telephone"></i> Contact Us</a>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="carousel-item" style="background-image: url('IMAGES\\OUR_PETS.jpeg'); background-size: cover; background-position: center; height: 500px;">
                    <div class="carousel-overlay"></div>
                    <div class="carousel-content">
                        <h2 class="display-4 fw-bold mb-3">Healthy Pets, Happy Families</h2>
                        <p class="lead mb-4">Premium quality pets and food for your beloved companions.</p>
                        <div class="gap-2 d-flex justify-content-center flex-wrap">
                            <a href="#products" class="btn btn-primary btn-lg"><i class="bi bi-shop"></i> Shop Now</a>
                            <a href="#services" class="btn btn-outline-light btn-lg"><i class="bi bi-info-circle"></i> Learn More</a>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="carousel-item" style="background-image: url('IMAGES\\ACESSORIES.png'); background-size: cover; background-position: center; height: 500px;">
                    <div class="carousel-overlay"></div>
                    <div class="carousel-content">
                        <h2 class="display-4 fw-bold mb-3">Premium Pet Accessories</h2>
                        <p class="lead mb-4">Everything your pet needs for comfort and happiness.</p>
                        <div class="gap-2 d-flex justify-content-center flex-wrap">
                            <a href="#products" class="btn btn-primary btn-lg"><i class="bi bi-collection"></i> View Collection</a>
                            <a href="#" class="btn btn-outline-light btn-lg"><i class="bi bi-bag-check"></i> Order Today</a>
                        </div>
                    </div>
                </div>

                <!-- Slide 4 -->
                <div class="carousel-item" style="background-image: url('IMAGES\\ALL_PETS.jpeg'); background-size: cover; background-position: center; height: 500px;">
                    <div class="carousel-overlay"></div>
                    <div class="carousel-content">
                        <h2 class="display-4 fw-bold mb-3">Professional Pet Care Services</h2>
                        <p class="lead mb-4">Expert grooming, training, and veterinary guidance.</p>
                        <div class="gap-2 d-flex justify-content-center flex-wrap">
                            <a href="#services" class="btn btn-primary btn-lg"><i class="bi bi-calendar-check"></i> Book Appointment</a>
                            <a href="#services" class="btn btn-outline-light btn-lg"><i class="bi bi-info-circle"></i> View Services</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

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
                    
                    <a href="about.php" class="btn btn-primary btn-lg mt-3">
                        <i class="bi bi-arrow-right-circle"></i> Read More
                    </a>
                </div>
            </div>
        </div>
    </section>

  <!-- Featured Pets Section -->
<section id="pets" class="py-5">

    <div class="container">

        <!-- Section Header -->
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold text-primary mb-2">
                Featured Pets
            </h2>

            <p class="lead text-muted">
                Find Your Perfect Companion
            </p>
        </div>

        <!-- Pet Cards Grid -->
        <div class="row g-4">

            <?php

            $petQuery = "SELECT * FROM pets ORDER BY id ASC";
            $petResult = $conn->query($petQuery);

            if ($petResult && $petResult->num_rows > 0):

                $delay = 100;

                while ($pet = $petResult->fetch_assoc()):

                    // Badge based on pet type
                    $badge = "Available";
                    $badgeClass = "bg-success";

                    if ($pet["category"] === "bird") {
                        $badge = "Limited";
                        $badgeClass = "bg-warning text-dark";
                    }

            ?>

                    <!-- Pet Card -->
                    <div class="col-lg-3 col-md-6"
                         data-aos="fade-up"
                         data-aos-delay="<?php echo $delay; ?>">

                        <div class="card h-100 pet-card">

                            <!-- Pet Image -->
                            <img
                                src="<?php echo htmlspecialchars($pet["image"]); ?>"
                                alt="<?php echo htmlspecialchars($pet["name"]); ?>"
                                class="card-img-top"
                            >

                            <!-- Pet Details -->
                            <div class="card-body">

                                <span class="badge <?php echo $badgeClass; ?> mb-2">
                                    <?php echo $badge; ?>
                                </span>

                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($pet["name"]); ?>
                                </h5>

                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars($pet["description"]); ?>
                                </p>

                                <div class="mb-2">

                                    <small>
                                        <strong>Age:</strong>
                                        <?php echo htmlspecialchars($pet["age"]); ?>
                                    </small>

                                    <br>

                                    <small>
                                        <strong>Price:</strong>
                                        <span class="text-danger">
                                            ₹<?php echo number_format((float)$pet["price"], 0); ?>
                                        </span>
                                    </small>

                                </div>

                            </div>

                            <!-- Buttons -->
                            <div class="card-footer bg-light d-grid gap-2">

                                <a
                                    href="product.php?id=<?php echo $pet["id"]; ?>"
                                    class="btn btn-outline-primary btn-sm"
                                >
                                    View Details
                                </a>

                                <a
                                    href="add_to_cart.php?id=<?php echo $pet["id"]; ?>"
                                    class="btn btn-primary btn-sm"
                                >
                                    <i class="bi bi-cart-plus me-1"></i>
                                    Add to Cart
                                </a>

                            </div>

                        </div>

                    </div>

            <?php

                    $delay += 50;

                endwhile;

            else:

            ?>

                <!-- No Pets -->
                <div class="col-12 text-center">

                    <div class="alert alert-info">
                        No pets available right now.
                    </div>

                </div>

            <?php endif; ?>

        </div>

        <!-- View All Pets -->
        <div class="text-center mt-5">

            <a href="pets.php"
               class="btn btn-outline-primary px-4">

                <i class="bi bi-grid me-1"></i>
                View All Pets

            </a>

        </div>

    </div>

</section>

    <!-- Pet Categories Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-2">Pet Categories</h2>
                <p class="lead text-muted">Browse by Type</p>
            </div>

            <!-- Category Cards -->
            <div class="row g-4">
                <!-- Dogs -->
                <div class="col-lg-2 col-md-4 col-sm-6" data-aos="flip-left">
                    <a href="#" class="category-card text-decoration-none">
                        <div class="card text-center h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-dog display-4 text-primary mb-3"></i>
                                <h6 class="card-title">Dogs</h6>
                                <small class="text-muted">15+ Breeds</small>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Cats -->
                <div class="col-lg-2 col-md-4 col-sm-6" data-aos="flip-left" data-aos-delay="100">
                    <a href="#" class="category-card text-decoration-none">
                        <div class="card text-center h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-cat display-4 text-danger mb-3"></i>
                                <h6 class="card-title">Cats</h6>
                                <small class="text-muted">10+ Breeds</small>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Birds -->
                <div class="col-lg-2 col-md-4 col-sm-6" data-aos="flip-left" data-aos-delay="200">
                    <a href="#" class="category-card text-decoration-none">
                        <div class="card text-center h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-chat-right-dots display-4 text-success mb-3"></i>
                                <h6 class="card-title">Birds</h6>
                                <small class="text-muted">8+ Varieties</small>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Fish -->
                <div class="col-lg-2 col-md-4 col-sm-6" data-aos="flip-left" data-aos-delay="300">
                    <a href="#" class="category-card text-decoration-none">
                        <div class="card text-center h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-water display-4 text-info mb-3"></i>
                                <h6 class="card-title">Fish</h6>
                                <small class="text-muted">20+ Species</small>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Rabbits -->
                <div class="col-lg-2 col-md-4 col-sm-6" data-aos="flip-left" data-aos-delay="400">
                    <a href="#" class="category-card text-decoration-none">
                        <div class="card text-center h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-hourglass-split display-4 text-warning mb-3"></i>
                                <h6 class="card-title">Rabbits</h6>
                                <small class="text-muted">5+ Breeds</small>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Accessories -->
                <div class="col-lg-2 col-md-4 col-sm-6" data-aos="flip-left" data-aos-delay="500">
                    <a href="#" class="category-card text-decoration-none">
                        <div class="card text-center h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-bag display-4 text-secondary mb-3"></i>
                                <h6 class="card-title">Accessories</h6>
                                <small class="text-muted">100+ Items</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-2">Our Services</h2>
                <p class="lead text-muted">Professional Pet Care Solutions</p>
            </div>

            <!-- Service Cards -->
            <div class="row g-4">
                <!-- Pet Grooming -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 service-card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">
                                <i class="bi bi-scissors"></i>
                            </div>
                            <h5 class="card-title">Pet Grooming</h5>
                            <p class="card-text text-muted">Professional bathing, trimming, and nail care for your beloved pets.</p>
                        </div>
                                                        <!-- card-footer: Pet Grooming Tips Collapse -->
                                <div class="card-footer bg-light border-0 p-0">

                                <!-- Toggle Button -->
                                <button
                                    class="btn btn-outline-primary w-100 rounded-0 rounded-bottom"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#groomingTips"
                                    aria-expanded="false"
                                    aria-controls="groomingTips">
                                    <i class="bi bi-scissors me-2"></i>
                                    Pet Grooming Tips
                                    <i class="bi bi-chevron-down ms-2 grooming-chevron" style="transition: transform 0.3s;"></i>
                                </button>

                                <!-- Collapsible Content -->
                                <div class="collapse" id="groomingTips">
                                    <div class="p-3 border-top">

                                    <!-- ── 1. BATHING ── -->
                                    <div class="mb-3">
                                        <h6 class="fw-semibold text-primary d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-droplet-fill text-info"></i> Bathing
                                        </h6>
                                        <p class="text-muted small mb-1">
                                        <strong>Frequency:</strong> Dogs (short coat) every 6–8 weeks · long coat every 4–6 weeks · Cats only when necessary.
                                        </p>
                                        <ol class="small text-muted ps-3 mb-1">
                                        <li><strong>Brush first</strong> — remove all mats before wetting; water tightens knots.</li>
                                        <li><strong>Use lukewarm water</strong> — test on your wrist; avoid hot water which irritates skin.</li>
                                        <li><strong>Pet-specific shampoo only</strong> — human shampoo pH damages animal skin; lather neck downward, avoid eyes and ears.</li>
                                        <li><strong>Rinse until water runs clear</strong> — residue causes itching and dandruff.</li>
                                        <li><strong>Dry thoroughly</strong> — towel first, then low-heat dryer kept moving; never hold in one spot.</li>
                                        </ol>
                                        <div class="alert alert-warning py-1 px-2 small mb-0">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Never bathe more than once a week — strips natural oils and causes dry, flaky skin.
                                        </div>
                                    </div>

                                    <hr class="my-2">

                                    <!-- ── 2. BRUSHING ── -->
                                    <div class="mb-3">
                                        <h6 class="fw-semibold text-primary d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-brush-fill text-purple"></i> Brushing & Coat Care
                                        </h6>
                                        <p class="text-muted small mb-1"><strong>Choose the right tool:</strong></p>
                                        <ul class="small text-muted ps-3 mb-1">
                                        <li><strong>Slicker brush</strong> — long or wavy coats</li>
                                        <li><strong>Bristle brush</strong> — short or smooth coats</li>
                                        <li><strong>Deshedding tool</strong> — double coats (Huskies, Labs)</li>
                                        <li><strong>Wide-tooth comb</strong> — curly coats (Poodles, Doodles)</li>
                                        </ul>
                                        <p class="text-muted small mb-1"><strong>Technique:</strong></p>
                                        <ol class="small text-muted ps-3 mb-1">
                                        <li>Always brush <strong>in the direction of hair growth</strong>.</li>
                                        <li>Section long coats — work from tips to root to avoid ripping.</li>
                                        <li>Focus on problem areas: behind ears, under armpits, around collar and tail base.</li>
                                        <li>Spray detangler on mats, wait 30 sec, then work from the outer edge inward.</li>
                                        </ol>
                                        <p class="text-muted small mb-0">
                                        <strong>Frequency:</strong> Short coat — weekly · Long coat — daily · Double coat — 3× per week.
                                        </p>
                                    </div>

                                    <hr class="my-2">

                                    <!-- ── 3. NAIL TRIMMING ── -->
                                    <div class="mb-3">
                                        <h6 class="fw-semibold text-primary d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-scissors text-danger"></i> Nail Trimming
                                        </h6>
                                        <ol class="small text-muted ps-3 mb-1">
                                        <li><strong>Right clipper:</strong> Scissor-type for large breeds, guillotine-type for small dogs and cats. Keep blades sharp.</li>
                                        <li><strong>Find the quick:</strong> Light nails — visible pink blood vessel. Dark nails — cut small amounts; stop when a dark dot appears at the centre.</li>
                                        <li><strong>Cut at 45°</strong> — angled slightly toward the underside; never straight across.</li>
                                        <li><strong>File after cutting</strong> — smooth sharp edges with a nail file or grinder.</li>
                                        <li><strong>Check dewclaws</strong> — they never self-wear and will curl into the skin if neglected.</li>
                                        </ol>
                                        <div class="alert alert-danger py-1 px-2 small mb-0">
                                        <i class="bi bi-heart-pulse-fill me-1"></i>
                                        Keep styptic powder on hand. If you nick the quick, apply directly to the nail and hold 30 sec to stop bleeding.
                                        </div>
                                        <p class="text-muted small mt-1 mb-0">
                                        <strong>Frequency:</strong> Dogs every 3–4 weeks · Cats every 2–3 weeks.
                                        </p>
                                    </div>

                                    <hr class="my-2">

                                    <!-- ── 4. EAR CLEANING ── -->
                                    <div class="mb-3">
                                        <h6 class="fw-semibold text-primary d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-ear text-primary"></i> Ear Cleaning
                                        </h6>
                                        <ol class="small text-muted ps-3 mb-1">
                                        <li><strong>Inspect first</strong> — redness, foul odour or dark discharge means infection; see a vet, don't clean.</li>
                                        <li><strong>Use vet-approved ear cleaner only</strong> — never water, hydrogen peroxide or cotton swabs inside the canal.</li>
                                        <li><strong>Massage the base</strong> for 20–30 seconds after applying cleaner to loosen wax.</li>
                                        <li><strong>Let them shake</strong> — it brings debris to the outer ear.</li>
                                        <li><strong>Wipe visible area only</strong> with a cotton ball — never insert anything into the canal.</li>
                                        </ol>
                                        <div class="alert alert-info py-1 px-2 small mb-0">
                                        <i class="bi bi-info-circle-fill me-1"></i>
                                        Floppy-eared breeds and dogs that swim frequently need checks every 1–2 weeks — far more prone to yeast infections.
                                        </div>
                                    </div>

                                    <hr class="my-2">

                                    <!-- ── 5. DENTAL CARE ── -->
                                    <div class="mb-3">
                                        <h6 class="fw-semibold text-primary d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-emoji-smile text-success"></i> Teeth & Dental Care
                                        </h6>
                                        <ol class="small text-muted ps-3 mb-1">
                                        <li><strong>Pet toothpaste only</strong> — fluoride and xylitol in human toothpaste are toxic to pets.</li>
                                        <li><strong>Start with a finger</strong> — rub along the gum line first before introducing a brush.</li>
                                        <li><strong>Brush outer surfaces</strong> in small circular motions — the tongue handles the inner side.</li>
                                        <li><strong>Supplement with VOHC-approved dental chews</strong> to reduce plaque between brushings.</li>
                                        </ol>
                                        <p class="text-muted small mb-1"><strong>Warning signs — see your vet:</strong></p>
                                        <div class="d-flex flex-wrap gap-1 mb-1">
                                        <span class="badge bg-danger-subtle text-danger-emphasis">Yellow/brown tartar</span>
                                        <span class="badge bg-danger-subtle text-danger-emphasis">Persistent bad breath</span>
                                        <span class="badge bg-danger-subtle text-danger-emphasis">Red or bleeding gums</span>
                                        <span class="badge bg-danger-subtle text-danger-emphasis">Difficulty chewing</span>
                                        <span class="badge bg-danger-subtle text-danger-emphasis">Loose or missing teeth</span>
                                        </div>
                                        <p class="text-muted small mb-0">
                                        <strong>Frequency:</strong> Brush daily (minimum 3× a week) · Vet dental check once a year.
                                        </p>
                                    </div>

                                    <hr class="my-2">

                                    <!-- ── 6. EYE CARE ── -->
                                    <div class="mb-0">
                                        <h6 class="fw-semibold text-primary d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-eye-fill text-secondary"></i> Eye Care
                                        </h6>
                                        <ol class="small text-muted ps-3 mb-1">
                                        <li><strong>Daily visual check</strong> — clear, bright eyes with minimal discharge is normal; large amounts are not.</li>
                                        <li><strong>Wipe from inner corner outward</strong> using a damp cotton ball; use a fresh one per eye.</li>
                                        <li><strong>Tear stain treatment</strong> — use vet-approved remover daily for flat-faced breeds; trim hair that falls across the eyes.</li>
                                        <li><strong>Protect during baths</strong> — apply a thin line of sterile eye lubricant along the lower eyelid before bathing.</li>
                                        </ol>
                                        <div class="alert alert-danger py-1 px-2 small mb-0">
                                        <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                        Yellow/green discharge, squinting, pawing at the eye or cloudiness = immediate vet visit. Do not treat at home.
                                        </div>
                                    </div>

                                    </div><!-- /.p-3 -->
                                </div><!-- /.collapse -->

                                </div><!-- /.card-footer -->

                    </div><!-- /.card -->
                </div><!-- /.col -->

                <!-- Vaccination -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="150">
                    <div class="card h-100 service-card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h5 class="card-title">Vaccination Assistance</h5>
                            <p class="card-text text-muted">Complete vaccination guidance and health check-up services.</p>
                        </div>
                        <!-- Vaccination Guide Collapse -->
                            <div class="card-footer bg-light border-0 p-0">

                                <!-- Toggle Button -->
                                <button class="btn btn-outline-success w-100 rounded-0 rounded-bottom"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#vaccinationGuideCollapse"
                                        aria-expanded="false"
                                        aria-controls="vaccinationGuideCollapse">

                                    <i class="bi bi-shield-check me-2"></i>
                                    Vaccination Guide
                                    <i class="bi bi-chevron-down ms-2 vaccination-chevron"
                                    style="transition: transform 0.3s;"></i>
                                </button>

                                <!-- Collapsible Content -->
                                <div class="collapse" id="vaccinationGuideCollapse">
                                    <div class="p-3 border-top">

                                        <!-- Your vaccination guide content goes here -->
                                             <!-- ── 1. WHY VACCINES ARE IMPORTANT ── -->
                                                    <div class="mb-3">
                                                                            <h6 class="fw-semibold text-success d-flex align-items-center gap-2 mb-2">
                                                                                <i class="bi bi-shield-check"></i> Why Vaccinations Matter
                                                                            </h6>

                                                                            <p class="text-muted small mb-0">
                                                                                Vaccines protect pets from serious and life-threatening diseases.
                                                                                They strengthen your pet's immune system and help prevent the spread
                                                                                of infections to other animals.
                                                                            </p>
                                                                        </div>

                                                                        <hr class="my-2">

                                                                        <!-- ── 2. DOG VACCINATION SCHEDULE ── -->
                                                                        <div class="mb-3">
                                                                            <h6 class="fw-semibold text-success d-flex align-items-center gap-2 mb-2">
                                                                                <i class="bi bi-heart-pulse-fill text-danger"></i> Puppy Vaccination Schedule
                                                                            </h6>

                                                                            <ul class="small text-muted ps-3 mb-0">
                                                                                <li><strong>6–8 Weeks:</strong> DHPP (Distemper, Hepatitis, Parvovirus, Parainfluenza)</li>
                                                                                <li><strong>10–12 Weeks:</strong> DHPP Booster</li>
                                                                                <li><strong>12–16 Weeks:</strong> Rabies Vaccine</li>
                                                                                <li><strong>14–16 Weeks:</strong> Final DHPP Booster</li>
                                                                                <li><strong>1 Year:</strong> Annual Booster & Rabies</li>
                                                                            </ul>
                                                                        </div>

                                                                        <hr class="my-2">

                                                                        <!-- ── 3. CAT VACCINATION SCHEDULE ── -->
                                                                        <div class="mb-3">
                                                                            <h6 class="fw-semibold text-success d-flex align-items-center gap-2 mb-2">
                                                                                <i class="bi bi-heart-fill text-warning"></i> Kitten Vaccination Schedule
                                                                            </h6>

                                                                            <ul class="small text-muted ps-3 mb-0">
                                                                                <li><strong>6–8 Weeks:</strong> FVRCP Vaccine</li>
                                                                                <li><strong>10–12 Weeks:</strong> FVRCP Booster</li>
                                                                                <li><strong>12–16 Weeks:</strong> Rabies Vaccine</li>
                                                                                <li><strong>14–16 Weeks:</strong> Final FVRCP Booster</li>
                                                                                <li><strong>1 Year:</strong> Annual Booster</li>
                                                                            </ul>
                                                                        </div>

                                                                        <hr class="my-2">

                                                                        <!-- ── 4. BEFORE VACCINATION ── -->
                                                                        <div class="mb-3">
                                                                            <h6 class="fw-semibold text-success d-flex align-items-center gap-2 mb-2">
                                                                                <i class="bi bi-clipboard-check"></i> Before Vaccination
                                                                            </h6>

                                                                            <ol class="small text-muted ps-3 mb-0">
                                                                                <li>Ensure your pet is healthy and active.</li>
                                                                                <li>Inform the veterinarian about any previous reactions.</li>
                                                                                <li>Carry your pet's vaccination record.</li>
                                                                                <li>Ask your vet if deworming is needed first.</li>
                                                                            </ol>
                                                                        </div>

                                                                        <hr class="my-2">

                                                                        <!-- ── 5. AFTER VACCINATION CARE ── -->
                                                                        <div class="mb-3">
                                                                            <h6 class="fw-semibold text-success d-flex align-items-center gap-2 mb-2">
                                                                                <i class="bi bi-house-heart-fill text-primary"></i> After Vaccination Care
                                                                            </h6>

                                                                            <ol class="small text-muted ps-3 mb-1">
                                                                                <li>Allow your pet to rest.</li>
                                                                                <li>Provide plenty of fresh water.</li>
                                                                                <li>Avoid intense exercise for 24 hours.</li>
                                                                                <li>Monitor for mild fever or temporary tiredness.</li>
                                                                            </ol>

                                                                            <div class="alert alert-warning py-1 px-2 small mb-0">
                                                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                                                Contact your veterinarian immediately if your pet develops facial swelling,
                                                                                difficulty breathing, repeated vomiting, or collapses after vaccination.
                                                                            </div>
                                                                        </div>

                                                                        <hr class="my-2">

                                                                        <!-- ── 6. REMINDER ── -->
                                                                        <div class="mb-0">
                                                                            <h6 class="fw-semibold text-success d-flex align-items-center gap-2 mb-2">
                                                                                <i class="bi bi-calendar-check-fill"></i> Annual Reminder
                                                                            </h6>

                                                                            <p class="text-muted small mb-0">
                                                                                Keep a vaccination record and schedule yearly booster shots as recommended
                                                                                by your veterinarian to ensure lifelong protection for your pet.
                                                                            </p>
                                                                        </div>
                                    </div>
                                </div>

                            </div>
                    </div>
                </div>

                <!-- Health Consultation -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100 service-card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">
                                <i class="bi bi-heart-pulse"></i>
                            </div>
                            <h5 class="card-title">Health Consultation</h5>
                            <p class="card-text text-muted">Expert veterinary advice and health consultation services.</p>
                        </div>
                        <div class="card-footer bg-light border-0 p-0">
                            <button class="btn btn-outline-primary w-100 rounded-0 rounded-bottom"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#healthConsultationDetails"
                                    aria-expanded="false"
                                    aria-controls="healthConsultationDetails">
                                <i class="bi bi-heart-pulse me-2"></i>
                                Learn More
                                <i class="bi bi-chevron-down ms-2"></i>
                            </button>
                            <div class="collapse" id="healthConsultationDetails">
                                <div class="p-3 border-top text-start small text-muted">
                                    <p><strong>What we offer:</strong></p>
                                    <ul class="ps-3 mb-2">
                                        <li>Virtual and in-store vet consultations.</li>
                                        <li>Health screening and nutrition advice.</li>
                                        <li>Behavioral assessments and wellness planning.</li>
                                    </ul>
                                    <p class="mb-0"><strong>Ideal for:</strong> new pet owners, elderly pets, and preventive care.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pet Training -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="250">
                    <div class="card h-100 service-card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">
                                <i class="bi bi-play-circle"></i>
                            </div>
                            <h5 class="card-title">Pet Training</h5>
                            <p class="card-text text-muted">Professional training programs for obedience and behavior.</p>
                        </div>
                        <div class="card-footer bg-light border-0 p-0">
                            <button class="btn btn-outline-primary w-100 rounded-0 rounded-bottom"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#trainingDetails"
                                    aria-expanded="false"
                                    aria-controls="trainingDetails">
                                <i class="bi bi-play-circle me-2"></i>
                                Learn More
                                <i class="bi bi-chevron-down ms-2"></i>
                            </button>
                            <div class="collapse" id="trainingDetails">
                                <div class="p-3 border-top text-start small text-muted">
                                    <p><strong>Training programs include:</strong></p>
                                    <ul class="ps-3 mb-2">
                                        <li>Basic obedience and leash training.</li>
                                        <li>House training and crate familiarization.</li>
                                        <li>Socialization classes for puppies and adults.</li>
                                    </ul>
                                    <p class="mb-0"><strong>Perfect for:</strong> new puppies, busy families, and rescue pets.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pet Boarding -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="card h-100 service-card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">
                                <i class="bi bi-house-heart"></i>
                            </div>
                            <h5 class="card-title">Pet Boarding</h5>
                            <p class="card-text text-muted">Safe and comfortable boarding facilities for your pets.</p>
                        </div>
                        <div class="card-footer bg-light border-0 p-0">
                            <button class="btn btn-outline-primary w-100 rounded-0 rounded-bottom"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#boardingDetails"
                                    aria-expanded="false"
                                    aria-controls="boardingDetails">
                                <i class="bi bi-house-heart me-2"></i>
                                Learn More
                                <i class="bi bi-chevron-down ms-2"></i>
                            </button>
                            <div class="collapse" id="boardingDetails">
                                <div class="p-3 border-top text-start small text-muted">
                                    <p><strong>Boarding features:</strong></p>
                                    <ul class="ps-3 mb-2">
                                        <li>Climate-controlled suites and supervised playtimes.</li>
                                        <li>Daily feeding and medication administration.</li>
                                        <li>Optional grooming and exercise add-ons.</li>
                                    </ul>
                                    <p class="mb-0"><strong>Great for:</strong> vacations, business trips, and short-term stays.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Home Delivery -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="350">
                    <div class="card h-100 service-card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">
                                <i class="bi bi-truck"></i>
                            </div>
                            <h5 class="card-title">Home Delivery</h5>
                            <p class="card-text text-muted">Quick and reliable delivery of pets and products to your home.</p>
                        </div>
                        <div class="card-footer bg-light border-0 p-0">
                            <button class="btn btn-outline-primary w-100 rounded-0 rounded-bottom"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#deliveryDetails"
                                    aria-expanded="false"
                                    aria-controls="deliveryDetails">
                                <i class="bi bi-truck me-2"></i>
                                Learn More
                                <i class="bi bi-chevron-down ms-2"></i>
                            </button>
                            <div class="collapse" id="deliveryDetails">
                                <div class="p-3 border-top text-start small text-muted">
                                    <p><strong>Delivery options:</strong></p>
                                    <ul class="ps-3 mb-2">
                                        <li>Same-day delivery for accessories and supplies.</li>
                                        <li>Carefully packaged products and live pet pickup coordination.</li>
                                        <li>Tracking updates and dedicated customer support.</li>
                                    </ul>
                                    <p class="mb-0"><strong>Available for:</strong> local customers and nearby neighborhoods.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold mb-2">Why Choose Us</h2>
                <p class="lead">Trusted by Thousands of Pet Lovers</p>
            </div>

            <!-- Counter Cards -->
            <div class="row g-4 text-center">
                <!-- Happy Customers -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="counter-card">
                        <div class="counter-icon mb-3">
                            <i class="bi bi-people-fill display-3"></i>
                        </div>
                        <h3 class="counter-number" data-target="5000">0</h3>
                        <p class="counter-label">Happy Customers</p>
                    </div>
                </div>

                <!-- Pets Sold -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="150">
                    <div class="counter-card">
                        <div class="counter-icon mb-3">
                            <i class="bi bi-paw-fill display-3"></i>
                        </div>
                        <h3 class="counter-number" data-target="2000">0</h3>
                        <p class="counter-label">Pets Sold</p>
                    </div>
                </div>

                <!-- Years Experience -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="counter-card">
                        <div class="counter-icon mb-3">
                            <i class="bi bi-clock-history display-3"></i>
                        </div>
                        <h3 class="counter-number" data-target="10">0</h3>
                        <p class="counter-label">Years Experience</p>
                    </div>
                </div>

                <!-- Support 24/7 -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="250">
                    <div class="counter-card">
                        <div class="counter-icon mb-3">
                            <i class="bi bi-headset display-3"></i>
                        </div>
                        <h3 class="counter-label">24/7 Support</h3>
                        <p class="counter-label">Always Available</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


<!-- Product Showcase Section -->
<section id="products" class="py-5 bg-light">

    <div class="container">

        <!-- Section Header -->
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold text-primary mb-2">
                Product Showcase
            </h2>

            <p class="lead text-muted">
                Premium Pet Products
            </p>
        </div>


        <!-- Product Cards -->
        <div class="row g-4">

            <?php

            $productQuery = "SELECT * FROM products ORDER BY id ASC";
            $productResult = $conn->query($productQuery);

            if ($productResult && $productResult->num_rows > 0):

                $delay = 100;

                while ($product = $productResult->fetch_assoc()):

            ?>

                    <div class="col-lg-3 col-md-6"
                         data-aos="fade-up"
                         data-aos-delay="<?php echo $delay; ?>">

                        <div class="card h-100 product-card border-0 shadow-sm">


                            <!-- Product Image -->
                            <div class="product-image position-relative">

                                <img
                                    src="<?php echo htmlspecialchars($product["image"]); ?>"
                                    alt="<?php echo htmlspecialchars($product["name"]); ?>"
                                    class="card-img-top"
                                >

                                <?php if (!empty($product["badge"])): ?>

                                    <span class="badge
                                        <?php
                                        echo ($product["badge"] === "NEW")
                                            ? "bg-success"
                                            : "bg-danger";
                                        ?>
                                        position-absolute top-0 end-0 m-2">

                                        <?php echo htmlspecialchars($product["badge"]); ?>

                                    </span>

                                <?php endif; ?>

                            </div>


                            <!-- Product Details -->
                            <div class="card-body">

                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($product["name"]); ?>
                                </h5>


                                <!-- Rating -->
                                <div class="mb-2">

                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-half text-warning"></i>

                                    <small class="text-muted">
                                        (Popular)
                                    </small>

                                </div>


                                <!-- Price -->
                                <h6 class="text-danger mb-3">

                                    ₹<?php
                                    echo number_format(
                                        (float)$product["price"],
                                        0
                                    );
                                    ?>


                                    <?php if (!empty($product["old_price"])): ?>

                                        <small class="text-muted text-decoration-line-through">

                                            ₹<?php
                                            echo number_format(
                                                (float)$product["old_price"],
                                                0
                                            );
                                            ?>

                                        </small>

                                    <?php endif; ?>

                                </h6>

                            </div>


                            <!-- Add To Cart -->
                            <div class="card-footer bg-light border-0">

                                <a
                                    href="add_to_cart.php?product_id=<?php echo $product["id"]; ?>"
                                    class="btn btn-primary w-100 btn-sm"
                                >

                                    <i class="bi bi-cart-plus me-1"></i>

                                    Add to Cart

                                </a>

                            </div>

                        </div>

                    </div>


            <?php

                    $delay += 50;

                endwhile;

            else:

            ?>

                <!-- No Products -->
                <div class="col-12 text-center">

                    <div class="alert alert-info">
                        No products available right now.
                    </div>

                </div>

            <?php endif; ?>

        </div>


        <!-- View All Products -->
        <div class="text-center mt-5">

            <a href="product.php"
               class="btn btn-outline-primary px-4">

                <i class="bi bi-grid me-1"></i>
                View All Products

            </a>

        </div>

    </div>

</section>


    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-2">Testimonials</h2>
                <p class="lead text-muted">What Our Customers Say</p>
            </div>

            <!-- Testimonials Carousel -->
            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Carousel Indicators -->
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
                    <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="2"></button>
                    <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="3"></button>
                </div>

                <!-- Carousel Inner -->
                <div class="carousel-inner">
                    <!-- Testimonial 1 -->
                    <div class="carousel-item active">
                        <div class="text-center">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&h=80&fit=crop" alt="Priya Sharma" class="rounded-circle mb-3" width="80" height="80">
                            <h5>Priya Sharma</h5>
                            <p class="text-muted mb-3">Dog Owner</p>
                            <div class="mb-3">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                            </div>
                            <blockquote class="blockquote">
                                <p>"Brar Pet Shop is amazing! I found my perfect companion here. The staff is very knowledgeable and caring. Highly recommended!"</p>
                            </blockquote>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="carousel-item">
                        <div class="text-center">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop" alt="Raj Patel" class="rounded-circle mb-3" width="80" height="80">
                            <h5>Raj Patel</h5>
                            <p class="text-muted mb-3">Cat & Bird Owner</p>
                            <div class="mb-3">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-half text-warning"></i>
                            </div>
                            <blockquote class="blockquote">
                                <p>"Excellent service and reasonable prices. My pets are very happy and healthy. The grooming service is top-notch!"</p>
                            </blockquote>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="carousel-item">
                        <div class="text-center">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=80&h=80&fit=crop" alt="Ananya Gupta" class="rounded-circle mb-3" width="80" height="80">
                            <h5>Ananya Gupta</h5>
                            <p class="text-muted mb-3">Multiple Pets Owner</p>
                            <div class="mb-3">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                            </div>
                            <blockquote class="blockquote">
                                <p>"I trust Brar Pet Shop with all my pets. They have a wide variety of products and their customer service is outstanding!"</p>
                            </blockquote>
                        </div>
                    </div>

                    <!-- Testimonial 4 -->
                    <div class="carousel-item">
                        <div class="text-center">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=80&h=80&fit=crop" alt="Vikram Singh" class="rounded-circle mb-3" width="80" height="80">
                            <h5>Vikram Singh</h5>
                            <p class="text-muted mb-3">New Pet Owner</p>
                            <div class="mb-3">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                            </div>
                            <blockquote class="blockquote">
                                <p>"Best pet shop in the city! They provided excellent guidance in choosing my first pet. Very professional and friendly!"</p>
                            </blockquote>
                        </div>
                    </div>
                </div>

                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5 bg-light">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-2">Frequently Asked Questions</h2>
                <p class="lead text-muted">Get Answers to Common Questions</p>
            </div>

            <!-- FAQ Accordion -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <!-- FAQ 1 -->
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="100">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                    <i class="bi bi-question-circle-fill me-2 text-primary"></i> Do you provide vaccination guidance?
                                </button>
                            </h2>
                            <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, we provide comprehensive vaccination guidance for all pets. Our experienced staff can advise you on the vaccination schedule and requirements for your specific pet. We work closely with veterinarians to ensure your pet's health.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="150">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                    <i class="bi bi-question-circle-fill me-2 text-primary"></i> Are pets health checked?
                                </button>
                            </h2>
                            <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Absolutely! All our pets undergo thorough health checks before being offered for sale. We maintain high standards to ensure only healthy and well-cared-for pets are available to our customers.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
                            <h2 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                    <i class="bi bi-question-circle-fill me-2 text-primary"></i> Do you offer home delivery?
                                </button>
                            </h2>
                            <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, we offer convenient home delivery for pet food, accessories, and other products. For live pets, we ensure safe and secure delivery with proper care. Contact us for delivery details and charges.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="250">
                            <h2 class="accordion-header" id="faqHeading4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                    <i class="bi bi-question-circle-fill me-2 text-primary"></i> Can I book grooming online?
                                </button>
                            </h2>
                            <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, you can book grooming appointments online or by calling us. We offer flexible scheduling and professional grooming services. Our experienced groomers ensure your pet is well cared for.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="300">
                            <h2 class="accordion-header" id="faqHeading5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                    <i class="bi bi-question-circle-fill me-2 text-primary"></i> What payment methods do you accept?
                                </button>
                            </h2>
                            <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We accept multiple payment methods including cash, credit cards, debit cards, net banking, and digital wallets like Google Pay, Apple Pay, and PhonePe. Choose the option that's most convenient for you.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="py-5">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-2">Gallery</h2>
                <p class="lead text-muted">Moments from Brar Pet Shop</p>
            </div>
     <!-- Gallery Grid -->
            <div class="row g-3">

                <!-- Item 1: Dogs -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow" role="group" aria-label="Dogs">
                        <img src="IMAGES/AMERICAN_BULLY.jpeg" alt="A dog at Brar Pet Shop">
                        <div class="gallery-overlay">
                            <h6 class="text-white mb-2">Dogs</h6>
                            <button type="button"
                                class="btn btn-light btn-sm view-btn"
                                data-image="IMAGES/AMERICAN_BULLY.jpeg"
                                data-caption="Dogs"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal">
                                <i class="bi bi-zoom-in"></i> View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item 2: Cats -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="150">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow" role="group" aria-label="Cats">
                        <img src="IMAGES/cat.jpeg" alt="A cat at Brar Pet Shop">
                        <div class="gallery-overlay">
                            <h6 class="text-white mb-2">Cats</h6>
                            <button type="button"
                                class="btn btn-light btn-sm view-btn"
                                data-image="IMAGES/cat.jpeg"
                                data-caption="Cats"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal">
                                <i class="bi bi-zoom-in"></i> View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item 3: Birds -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow" role="group" aria-label="Birds">
                        <img src="IMAGES/grey-parrot-on-branch-free-photo.jpg" alt="A bird at Brar Pet Shop">
                        <div class="gallery-overlay">
                            <h6 class="text-white mb-2">Birds</h6>
                            <button type="button"
                                class="btn btn-light btn-sm view-btn"
                                data-image="IMAGES/grey-parrot-on-branch-free-photo.jpg"
                                data-caption="Birds"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal">
                                <i class="bi bi-zoom-in"></i> View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item 4: Grooming -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="250">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow" role="group" aria-label="Grooming">
                        <img src="IMAGES/groom.jpg" alt="Grooming service at Brar Pet Shop">
                        <div class="gallery-overlay">
                            <h6 class="text-white mb-2">Grooming</h6>
                            <button type="button"
                                class="btn btn-light btn-sm view-btn"
                                data-image="IMAGES/groom.jpg"
                                data-caption="Grooming"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal">
                                <i class="bi bi-zoom-in"></i> View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item 5: Store Interior -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow" role="group" aria-label="Store Interior">
                        <img src="IMAGES/inerior.jpg" alt="Store interior of Brar Pet Shop">
                        <div class="gallery-overlay">
                            <h6 class="text-white mb-2">Store Interior</h6>
                            <button type="button"
                                class="btn btn-light btn-sm view-btn"
                                data-image="IMAGES/inerior.jpg"
                                data-caption="Store Interior"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal">
                                <i class="bi bi-zoom-in"></i> View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item 6: Accessories -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="350">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow" role="group" aria-label="Accessories">
                        <img src="IMAGES/ACESSORIES.png" alt="Pet accessories at Brar Pet Shop">
                        <div class="gallery-overlay">
                            <h6 class="text-white mb-2">Accessories</h6>
                            <button type="button"
                                class="btn btn-light btn-sm view-btn"
                                data-image="IMAGES/ACESSORIES.png"
                                data-caption="Accessories"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal">
                                <i class="bi bi-zoom-in"></i> View
                            </button>
                        </div>
                    </div>
                </div>

            </div><!-- /.row -->
        </div><!-- /.container -->
    </section>

    <!-- ===== Gallery Lightbox Modal ===== -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-semibold" id="galleryModalLabel"></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-2 text-center">
                    <img id="galleryModalImage" src="" alt="" class="img-fluid rounded">
                </div>

                <div class="modal-caption" id="galleryModalCaption"></div>

            </div>
        </div>
    </div>

    <!-- Call To Action Section -->
    <section class="cta-section py-5" style="background-image: url('https://images.unsplash.com/photo-1558605780-38269a67b529?w=1200&h=500&fit=crop'); background-size: cover; background-position: center;">
        <div class="cta-overlay"></div>
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center text-white" data-aos="fade-up">
                    <h2 class="display-4 fw-bold mb-3">Give Your Pets the Love They Deserve</h2>
                    <p class="lead mb-4">Visit Brar Pet Shop today and discover the perfect companion and products for your beloved pets.</p>
                    <div class="gap-3 d-flex justify-content-center flex-wrap">
                        <a href="#" class="btn btn-primary btn-lg">
                            <i class="bi bi-shop"></i> Visit Store
                        </a>
                        <a href="tel:+919876543210" class="btn btn-light btn-lg">
                            <i class="bi bi-telephone"></i> Call Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-2">Contact Us</h2>
                <p class="lead text-muted">Get in Touch with Us</p>
            </div>

            <!-- Contact Form and Info -->
            <div class="row g-4">
                <!-- Contact Form -->
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Send us a Message</h5>
                            <form id="contactForm" novalidate>
                                <!-- Full Name -->
                                <div class="mb-3">
                                    <label for="fullName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="fullName" placeholder="Your Full Name" required>
                                    <div class="invalid-feedback">Please provide a valid full name.</div>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" placeholder="your@email.com" required>
                                    <div class="invalid-feedback">Please provide a valid email address.</div>
                                </div>

                                <!-- Phone -->
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" placeholder="+91 XXXXXXXXXX" required>
                                    <div class="invalid-feedback">Please provide a valid phone number.</div>
                                </div>

                                <!-- Pet Type -->
                                <div class="mb-3">
                                    <label for="petType" class="form-label">Pet Type</label>
                                    <select class="form-select" id="petType" required>
                                        <option value="">Select Pet Type</option>
                                        <option value="dog">Dog</option>
                                        <option value="cat">Cat</option>
                                        <option value="bird">Bird</option>
                                        <option value="fish">Fish</option>
                                        <option value="rabbit">Rabbit</option>
                                        <option value="hamster">Hamster</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a pet type.</div>
                                </div>

                                <!-- Subject -->
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="subject" placeholder="Inquiry Subject" required>
                                    <div class="invalid-feedback">Please provide a subject.</div>
                                </div>

                                <!-- Message -->
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" rows="4" placeholder="Your message here..." required></textarea>
                                    <div class="invalid-feedback">Please provide a message.</div>
                                </div>

                                <!-- Submit & Reset Buttons -->
                                <div class="gap-2 d-flex">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="bi bi-send"></i> Submit
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary flex-grow-1">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Contact Information</h5>
                            
                            <!-- Address -->
                            <div class="mb-4">
                                <h6 class="text-primary mb-2"><i class="bi bi-geo-alt"></i> Address</h6>
                                <p class="text-muted">
                                    Brar Pet Shop<br>
                                    Old Fazilka, Abohar<br>
                                    Punjab 152116
                                </p>
                            </div>

                            <!-- Phone -->
                            <div class="mb-4">
                                <h6 class="text-primary mb-2"><i class="bi bi-telephone"></i> Phone</h6>
                                <a href="tel:+919779654617" class="text-decoration-none text-dark">+91 97796 54617
                        
                                </a>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <h6 class="text-primary mb-2"><i class="bi bi-envelope"></i> Email</h6>
                                <a href="mailto:info@brarpetshop.com" class="text-decoration-none text-dark">info@brarpetshop.com</a>
                            </div>

                            <!-- Opening Hours -->
                            <div class="mb-4">
                                <h6 class="text-primary mb-2"><i class="bi bi-clock"></i> Opening Hours</h6>
                                <p class="text-muted mb-1">Monday - Saturday: 9:00 AM - 6:00 PM</p>
                                <p class="text-muted">Sunday: 10:00 AM - 4:00 PM</p>
                            </div>

                            <!-- Quick Links -->
                            <div>
                                <h6 class="text-primary mb-2">Follow Us</h6>
                                <div class="gap-2 d-flex flex-wrap">
                                    <a href="https://facebook.com" class="btn btn-outline-primary btn-sm" title="Facebook" data-bs-toggle="tooltip">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                    <a href="https://instagram.com" class="btn btn-outline-danger btn-sm" title="Instagram" data-bs-toggle="tooltip">
                                        <i class="bi bi-instagram"></i>
                                    </a>
                                    <a href="https://wa.me/91977965617" class="btn btn-outline-success btn-sm" title="WhatsApp" data-bs-toggle="tooltip">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                    <a href="https://youtube.com" class="btn btn-outline-danger btn-sm" title="YouTube" data-bs-toggle="tooltip">
                                        <i class="bi bi-youtube"></i>
                                    </a>
                                    <a href="https://twitter.com" class="btn btn-outline-info btn-sm" title="X (Twitter)" data-bs-toggle="tooltip">
                                        <i class="bi bi-twitter"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Google Map Section -->
    <section class="py-5">
        <div class="container-fluid">
            <div class="row g-0">
                <div class="col-12">
                    <h3 class="text-center mb-4 text-primary fw-bold">Find Us on Map</h3>
                    <!-- Google Map Embed -->
                    <div class="ratio ratio-16x9">
                       <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3450.132240939842!2d74.18577599999999!3d30.147634999999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMzDCsDA4JzUxLjUiTiA3NMKwMTEnMDguOCJF!5e0!3m2!1sen!2sin!4v1781019105421!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <div class="text-center mt-3">
                        <a href="https://maps.google.com" target="_blank" class="btn btn-primary btn-sm">
                            <i class="bi bi-geo-alt"></i> Get Directions
                        </a>
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
