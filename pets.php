
<?php

require_once "config/db.php";

/* =========================
   CATEGORY FILTER
========================= */

$category = trim($_GET["category"] ?? "");

/* Allowed categories */

$allowedCategories = [
    "dog",
    "cat",
    "bird",
    "fish",
    "small"
];

/* Prevent invalid category */

if ($category !== "" && !in_array($category, $allowedCategories, true)) {
    $category = "";
}


/* =========================
   GET PETS
========================= */

if ($category !== "") {

    $stmt = $conn->prepare(
        "SELECT *
         FROM pets
         WHERE category = ?
         ORDER BY id ASC"
    );

    if (!$stmt) {
        die("Database query failed: " . $conn->error);
    }

    $stmt->bind_param("s", $category);

} else {

    $stmt = $conn->prepare(
        "SELECT *
         FROM pets
         ORDER BY id ASC"
    );

    if (!$stmt) {
        die("Database query failed: " . $conn->error);
    }
}

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Pets | Brar Pet Shop</title>


    <!-- Bootstrap 5.3 CSS -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">


    <!-- AOS CSS -->

    <link
        href="https://unpkg.com/aos@2.3.1/dist/aos.css"
        rel="stylesheet">


    <!-- Custom CSS -->

    <link
        rel="stylesheet"
        href="style.css">

</head>


<body>


    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <?php include 'navbar.php'; ?>


    <!-- =====================================================
         FEATURED PETS SECTION
    ====================================================== -->

    <section id="pets" class="py-5">

        <div class="container">


            <!-- SECTION HEADER -->

            <div
                class="text-center mb-5"
                data-aos="fade-up">

                <h2 class="display-5 fw-bold text-primary mb-2">

                    Featured Pets

                </h2>

                <p class="lead text-muted">

                    Find Your Perfect Companion

                </p>

            </div>


            <!-- =================================================
                 SEARCH AND FILTER
            ================================================== -->

            <div
                class="search-filter-container mb-5 p-4 bg-light rounded-3 shadow-sm"
                data-aos="fade-up">

                <div class="row g-3">


                    <!-- SEARCH -->

                    <div class="col-lg-4 col-md-6">

                        <div class="input-group">

                            <span
                                class="input-group-text bg-primary border-primary">

                                <i class="bi bi-search text-white"></i>

                            </span>

                            <input
                                type="text"
                                id="petSearchInput"
                                class="form-control border-primary"
                                placeholder="Search by pet name..."
                                aria-label="Search pets">

                        </div>

                    </div>


                    <!-- PET TYPE -->

                    <div class="col-lg-3 col-md-6">

                        <select
                            id="petTypeFilter"
                            class="form-select"
                            aria-label="Filter by pet type"
                            onchange="
                                if(this.value) {
                                    window.location.href =
                                    'pets.php?category=' +
                                    encodeURIComponent(this.value);
                                } else {
                                    window.location.href =
                                    'pets.php';
                                }
                            ">

                            <option
                                value=""
                                <?php echo $category === "" ? "selected" : ""; ?>>

                                All Pet Types

                            </option>

                            <option
                                value="dog"
                                <?php echo $category === "dog" ? "selected" : ""; ?>>

                                Dogs

                            </option>

                            <option
                                value="cat"
                                <?php echo $category === "cat" ? "selected" : ""; ?>>

                                Cats

                            </option>

                            <option
                                value="bird"
                                <?php echo $category === "bird" ? "selected" : ""; ?>>

                                Birds

                            </option>

                            <option
                                value="fish"
                                <?php echo $category === "fish" ? "selected" : ""; ?>>

                                Fish

                            </option>

                            <option
                                value="small"
                                <?php echo $category === "small" ? "selected" : ""; ?>>

                                Small Pets

                            </option>

                        </select>

                    </div>


                    <!-- PRICE RANGE -->

                    <div class="col-lg-3 col-md-6">

                        <select
                            id="priceRangeFilter"
                            class="form-select"
                            aria-label="Filter by price range">

                            <option value="">

                                All Prices

                            </option>

                            <option value="0-5000">

                                ₹0 - ₹5,000

                            </option>

                            <option value="5000-20000">

                                ₹5,000 - ₹20,000

                            </option>

                            <option value="20000-50000">

                                ₹20,000 - ₹50,000

                            </option>

                            <option value="50000+">

                                ₹50,000+

                            </option>

                        </select>

                    </div>


                    <!-- RESET -->

                    <div class="col-lg-2 col-md-6">

                        <button
                            id="resetFiltersBtn"
                            class="btn btn-outline-primary w-100"
                            title="Clear all filters">

                            <i class="bi bi-arrow-counterclockwise me-2"></i>

                            Reset

                        </button>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 SELECTED CATEGORY
            ================================================== -->

            <?php if ($category !== ""): ?>

                <div
                    class="alert alert-primary d-flex align-items-center justify-content-between mb-4"
                    data-aos="fade-up">

                    <div>

                        <i class="bi bi-funnel-fill me-2"></i>

                        Showing:

                        <strong>

                            <?php

                            $categoryNames = [
                                "dog" => "Dogs",
                                "cat" => "Cats",
                                "bird" => "Birds",
                                "fish" => "Fish",
                                "small" => "Small Pets"
                            ];

                            echo htmlspecialchars(
                                $categoryNames[$category] ?? "Pets"
                            );

                            ?>

                        </strong>

                    </div>


                    <a
                        href="pets.php"
                        class="btn btn-sm btn-outline-primary">

                        <i class="bi bi-x-lg me-1"></i>

                        Clear Filter

                    </a>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 NO RESULTS MESSAGE
            ================================================== -->

            <div
                id="noResultsMessage"
                class="alert alert-info text-center d-none mb-4"
                role="alert">

                <i class="bi bi-info-circle me-2"></i>

                No pets found matching your criteria.
                Try adjusting your filters.

            </div>


            <!-- =================================================
                 PET CARDS
            ================================================== -->

            <div
                class="row g-4"
                id="petCardsContainer">


                <?php if ($result && $result->num_rows > 0): ?>

                    <?php

                    $delay = 100;

                    while ($pet = $result->fetch_assoc()):

                        $petCategory = strtolower(
                            trim($pet["category"])
                        );

                    ?>


                        <!-- PET CARD -->

                        <div
                            class="col-lg-3 col-md-6 pet-card-wrapper"
                            data-pet-type="<?php echo htmlspecialchars($petCategory); ?>"
                            data-pet-name="<?php echo htmlspecialchars($pet["name"]); ?>"
                            data-pet-price="<?php echo htmlspecialchars($pet["price"]); ?>"
                            data-aos="fade-up"
                            data-aos-delay="<?php echo $delay; ?>">


                            <div class="card h-100 pet-card">


                                <!-- PET IMAGE -->

                                <img
                                    src="<?php echo htmlspecialchars($pet["image"]); ?>"
                                    alt="<?php echo htmlspecialchars($pet["name"]); ?>"
                                    class="card-img-top"
                                    onerror="
                                        this.onerror=null;
                                        this.src='https://via.placeholder.com/500x350?text=Pet';
                                    ">


                                <!-- CARD BODY -->

                                <div class="card-body">


                                    <!-- =================================================
                                         AVAILABILITY
                                    ================================================== -->

                                    <?php if ($pet["availability"] === "Available"): ?>

                                        <span class="badge bg-success mb-2">

                                            <i class="bi bi-check-circle me-1"></i>

                                            Available

                                        </span>


                                    <?php elseif ($pet["availability"] === "Reserved"): ?>

                                        <span
                                            class="badge bg-warning text-dark mb-2">

                                            <i class="bi bi-clock me-1"></i>

                                            Reserved

                                        </span>


                                    <?php else: ?>

                                        <span class="badge bg-danger mb-2">

                                            <i class="bi bi-x-circle me-1"></i>

                                            Sold

                                        </span>

                                    <?php endif; ?>


                                    <!-- PET NAME -->

                                    <h5 class="card-title">

                                        <?php

                                        echo htmlspecialchars(
                                            $pet["name"]
                                        );

                                        ?>

                                    </h5>


                                    <!-- DESCRIPTION -->

                                    <p class="card-text text-muted">

                                        <?php

                                        echo htmlspecialchars(
                                            $pet["description"]
                                        );

                                        ?>

                                    </p>


                                    <!-- AGE -->

                                    <div class="mb-2">

                                        <small>

                                            <strong>Age:</strong>

                                            <?php

                                            echo htmlspecialchars(
                                                $pet["age"]
                                            );

                                            ?>

                                        </small>

                                    </div>


                                    <!-- CATEGORY -->

                                    <div class="mb-2">

                                        <small>

                                            <strong>Type:</strong>

                                            <?php

                                            $categoryNames = [
                                                "dog" => "Dog",
                                                "cat" => "Cat",
                                                "bird" => "Bird",
                                                "fish" => "Fish",
                                                "small" => "Small Pet"
                                            ];

                                            echo htmlspecialchars(
                                                $categoryNames[$petCategory]
                                                ?? ucfirst($petCategory)
                                            );

                                            ?>

                                        </small>

                                    </div>


                                    <!-- PRICE -->

                                    <div class="mb-2">

                                        <small>

                                            <strong>Price:</strong>

                                            <span class="text-danger">

                                                ₹<?php

                                                echo number_format(
                                                    (float)$pet["price"]
                                                );

                                                ?>

                                            </span>

                                        </small>

                                    </div>


                                </div>


                                <!-- =================================================
                                     CARD FOOTER
                                ================================================== -->

                                <div
                                    class="card-footer bg-light d-grid gap-2">


                                    <!-- VIEW DETAILS -->

                                    <a
                                        href="product.php?id=<?php echo (int)$pet["id"]; ?>"
                                        class="btn btn-outline-primary btn-sm">

                                        <i class="bi bi-eye me-1"></i>

                                        View Details

                                    </a>


                                    <!-- =================================================
                                         ADD TO CART
                                    ================================================== -->

                                    <?php if ($pet["availability"] === "Available"): ?>

                                        <a
                                            href="add_to_cart.php?id=<?php echo (int)$pet["id"]; ?>"
                                            class="btn btn-primary btn-sm">

                                            <i class="bi bi-cart-plus me-1"></i>

                                            Add to Cart

                                        </a>
                                        <a
    href="add_to_wishlist.php?id=<?php echo (int)$pet["id"]; ?>"
    class="btn btn-outline-danger w-100 btn-sm mb-2"
>
    <i class="bi bi-heart me-1"></i>
    Add to Wishlist
</a>


                                    <?php elseif ($pet["availability"] === "Reserved"): ?>

                                        <button
                                            type="button"
                                            class="btn btn-warning btn-sm"
                                            disabled>

                                            <i class="bi bi-clock me-1"></i>

                                            Reserved

                                        </button>


                                    <?php else: ?>

                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm"
                                            disabled>

                                            <i class="bi bi-x-circle me-1"></i>

                                            Sold Out

                                        </button>

                                    <?php endif; ?>


                                </div>

                            </div>

                        </div>


                    <?php

                        $delay += 50;

                    endwhile;

                    ?>


                <?php else: ?>


                    <!-- NO DATABASE RESULTS -->

                    <div class="col-12">

                        <div
                            class="alert alert-info text-center">

                            <i class="bi bi-info-circle me-2"></i>

                            <?php if ($category !== ""): ?>

                                No pets found in this category.

                            <?php else: ?>

                                No pets are currently available.

                            <?php endif; ?>

                        </div>

                    </div>


                <?php endif; ?>


            </div>


            <!-- =================================================
                 PAGINATION
            ================================================== -->

            <nav
                class="mt-5"
                aria-label="Pet Pagination">

                <ul
                    class="pagination justify-content-center"
                    id="pagination">

                </ul>

            </nav>


        </div>

    </section>


    <!-- =====================================================
         PET CATEGORIES SECTION
    ====================================================== -->

    <section class="py-5 bg-light">

        <div class="container">


            <!-- SECTION HEADER -->

            <div
                class="text-center mb-5"
                data-aos="fade-up">

                <h2 class="display-5 fw-bold text-primary mb-2">

                    Pet Categories

                </h2>

                <p class="lead text-muted">

                    Browse by Type

                </p>

            </div>


            <!-- CATEGORY CARDS -->

            <div class="row g-4">


                <!-- DOGS -->

                <div
                    class="col-lg-2 col-md-4 col-sm-6"
                    data-aos="flip-left">

                    <a
                        href="pets.php?category=dog"
                        class="category-card text-decoration-none">

                        <div
                            class="card text-center h-100 border-0 shadow-sm">

                            <div class="card-body">

                                <i
                                    class="bi bi-heart-fill display-4 text-primary mb-3">
                                </i>

                                <h6 class="card-title">

                                    Dogs

                                </h6>

                                <small class="text-muted">

                                    Browse Dogs

                                </small>

                            </div>

                        </div>

                    </a>

                </div>


                <!-- CATS -->

                <div
                    class="col-lg-2 col-md-4 col-sm-6"
                    data-aos="flip-left"
                    data-aos-delay="100">

                    <a
                        href="pets.php?category=cat"
                        class="category-card text-decoration-none">

                        <div
                            class="card text-center h-100 border-0 shadow-sm">

                            <div class="card-body">

                                <i
                                    class="bi bi-house-heart-fill display-4 text-danger mb-3">
                                </i>

                                <h6 class="card-title">

                                    Cats

                                </h6>

                                <small class="text-muted">

                                    Browse Cats

                                </small>

                            </div>

                        </div>

                    </a>

                </div>


                <!-- BIRDS -->

                <div
                    class="col-lg-2 col-md-4 col-sm-6"
                    data-aos="flip-left"
                    data-aos-delay="200">

                    <a
                        href="pets.php?category=bird"
                        class="category-card text-decoration-none">

                        <div
                            class="card text-center h-100 border-0 shadow-sm">

                            <div class="card-body">

                                <i
                                    class="bi bi-twitter display-4 text-success mb-3">
                                </i>

                                <h6 class="card-title">

                                    Birds

                                </h6>

                                <small class="text-muted">

                                    Browse Birds

                                </small>

                            </div>

                        </div>

                    </a>

                </div>


                <!-- FISH -->

                <div
                    class="col-lg-2 col-md-4 col-sm-6"
                    data-aos="flip-left"
                    data-aos-delay="300">

                    <a
                        href="pets.php?category=fish"
                        class="category-card text-decoration-none">

                        <div
                            class="card text-center h-100 border-0 shadow-sm">

                            <div class="card-body">

                                <i
                                    class="bi bi-water display-4 text-info mb-3">
                                </i>

                                <h6 class="card-title">

                                    Fish

                                </h6>

                                <small class="text-muted">

                                    Browse Fish

                                </small>

                            </div>

                        </div>

                    </a>

                </div>


                <!-- SMALL PETS -->

                <div
                    class="col-lg-2 col-md-4 col-sm-6"
                    data-aos="flip-left"
                    data-aos-delay="400">

                    <a
                        href="pets.php?category=small"
                        class="category-card text-decoration-none">

                        <div
                            class="card text-center h-100 border-0 shadow-sm">

                            <div class="card-body">

                                <i
                                    class="bi bi-stars display-4 text-warning mb-3">
                                </i>

                                <h6 class="card-title">

                                    Small Pets

                                </h6>

                                <small class="text-muted">

                                    Rabbits & Hamsters

                                </small>

                            </div>

                        </div>

                    </a>

                </div>


                <!-- ACCESSORIES -->

                <div
                    class="col-lg-2 col-md-4 col-sm-6"
                    data-aos="flip-left"
                    data-aos-delay="500">

                    <a
                        href="product.php"
                        class="category-card text-decoration-none">

                        <div
                            class="card text-center h-100 border-0 shadow-sm">

                            <div class="card-body">

                                <i
                                    class="bi bi-bag display-4 text-secondary mb-3">
                                </i>

                                <h6 class="card-title">

                                    Accessories

                                </h6>

                                <small class="text-muted">

                                    Pet Products

                                </small>

                            </div>

                        </div>

                    </a>

                </div>


            </div>

        </div>

    </section>


    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <?php include 'footer.php'; ?>


    <!-- =====================================================
         BACK TO TOP
    ====================================================== -->

    <button
        id="backToTopBtn"
        class="btn btn-primary rounded-circle position-fixed"
        title="Back to top">

        <i class="bi bi-arrow-up"></i>

    </button>


    <!-- Bootstrap JS -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    </script>


    <!-- AOS JS -->

    <script
        src="https://unpkg.com/aos@2.3.1/dist/aos.js">
    </script>


    <!-- Initialize AOS -->

    <script>

        AOS.init({
            duration: 1000,
            once: true
        });

    </script>


    <!-- Custom Script -->

    <script src="script.js"></script>


</body>

</html>

