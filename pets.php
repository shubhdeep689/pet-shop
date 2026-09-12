<?php
require_once "config/db.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pets</title>
     
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
        <!-- Featured Pets Section -->
    <section id="pets" class="py-5">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-2">Featured Pets</h2>
                <p class="lead text-muted">Find Your Perfect Companion</p>
            </div>

            <!-- Search and Filter Section -->
            <div class="search-filter-container mb-5 p-4 bg-light rounded-3 shadow-sm" data-aos="fade-up">
                <div class="row g-3">
                    <!-- Search Bar -->
                    <div class="col-lg-4 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-primary border-primary"><i class="bi bi-search text-white"></i></span>
                            <input type="text" id="petSearchInput" class="form-control border-primary" placeholder="Search by pet name..." aria-label="Search pets">
                        </div>
                    </div>

                    <!-- Pet Type Filter -->
                    <div class="col-lg-3 col-md-6">
                        <select id="petTypeFilter" class="form-select" aria-label="Filter by pet type">
                            <option value="">All Pet Types</option>
                            <option value="dogs">Dogs</option>
                            <option value="cats">Cats</option>
                            <option value="birds">Birds</option>
                            <option value="fish">Fish</option>
                            <option value="small">Small Pets</option>
                        </select>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="col-lg-3 col-md-6">
                        <select id="priceRangeFilter" class="form-select" aria-label="Filter by price range">
                            <option value="">All Prices</option>
                            <option value="0-5000">₹0 - ₹5,000</option>
                            <option value="5000-20000">₹5,000 - ₹20,000</option>
                            <option value="20000-50000">₹20,000 - ₹50,000</option>
                            <option value="50000+">₹50,000+</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-lg-2 col-md-6">
                        <button id="resetFiltersBtn" class="btn btn-outline-primary w-100" title="Clear all filters">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- No Results Message -->
            <div id="noResultsMessage" class="alert alert-info text-center d-none mb-4" role="alert">
                <i class="bi bi-info-circle me-2"></i>No pets found matching your criteria. Try adjusting your filters.
            </div>
<!-- Pet Cards Grid -->
<div class="row g-4" id="petCardsContainer">

<?php

$sql = "SELECT * FROM pets ORDER BY id ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0):

    $delay = 100;

    while ($pet = $result->fetch_assoc()):

        $category = strtolower($pet["category"]);

?>

    <div class="col-lg-3 col-md-6 pet-card-wrapper"
         data-pet-type="<?php echo htmlspecialchars($category); ?>"
         data-pet-name="<?php echo htmlspecialchars($pet["name"]); ?>"
         data-pet-price="<?php echo htmlspecialchars($pet["price"]); ?>"
         data-aos="fade-up"
         data-aos-delay="<?php echo $delay; ?>">

        <div class="card h-100 pet-card">

            <!-- Pet Image -->
            <img src="<?php echo htmlspecialchars($pet["image"]); ?>"
                 alt="<?php echo htmlspecialchars($pet["name"]); ?>"
                 class="card-img-top">

            <div class="card-body">

                <!-- Availability -->
                <span class="badge bg-success mb-2">
                    Available
                </span>

                <!-- Pet Name -->
                <h5 class="card-title">
                    <?php echo htmlspecialchars($pet["name"]); ?>
                </h5>

                <!-- Description -->
                <p class="card-text text-muted">
                    <?php echo htmlspecialchars($pet["description"]); ?>
                </p>

                <!-- Age -->
                <div class="mb-2">
                    <small>
                        <strong>Age:</strong>
                        <?php echo htmlspecialchars($pet["age"]); ?>
                    </small>
                </div>

                <!-- Price -->
                <div class="mb-2">
                    <small>
                        <strong>Price:</strong>

                        <span class="text-danger">
                            ₹<?php echo number_format($pet["price"]); ?>
                        </span>
                    </small>
                </div>

            </div>

            <div class="card-footer bg-light d-grid gap-2">

                <!-- View Details -->
                <a href="product.php?id=<?php echo $pet["id"]; ?>"
                   class="btn btn-outline-primary btn-sm">

                    View Details

                </a>

                <!-- Add to Cart -->
                <a href="add_to_cart.php?id=<?php echo $pet["id"]; ?>"
                   class="btn btn-primary btn-sm">

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

    <div class="col-12">

        <div class="alert alert-info text-center">

            <i class="bi bi-info-circle me-2"></i>

            No pets are currently available.

        </div>

    </div>

<?php endif; ?>

</div>

</div>
             <!-- Pagination -->
                <nav class="mt-5" aria-label="Pet Pagination">
                    <ul class="pagination justify-content-center" id="pagination"></ul>
                </nav>
           
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