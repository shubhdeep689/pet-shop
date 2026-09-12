
<?php

session_start();

require_once "config/db.php";


// Get products from database
$sql = "SELECT * FROM products ORDER BY id ASC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="Premium Pet Products | Brar Pet Shop">

    <title>Pet Products | Brar Pet Shop</title>


    <!-- Bootstrap 5.3 CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet">


    <!-- Bootstrap Icons -->

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">


    <!-- AOS CSS -->

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css"
          rel="stylesheet">


    <!-- Custom CSS -->

    <link rel="stylesheet"
          href="style.css">

</head>


<body>


<?php include 'navbar.php'; ?>


<!-- Product Showcase Section -->

<section id="products"
         class="py-5 bg-light">

    <div class="container">


        <!-- Section Header -->

        <div class="text-center mb-5"
             data-aos="fade-up">

            <h2 class="display-5 fw-bold text-primary mb-2">

                Product Showcase

            </h2>

            <p class="lead text-muted">

                Premium Pet Products

            </p>

        </div>


        <!-- Product Cards -->

        <div class="row g-4">


        <?php if ($result && $result->num_rows > 0): ?>


            <?php

            $delay = 100;

            while ($product = $result->fetch_assoc()):

            ?>


                <div class="col-lg-3 col-md-6"
                     data-aos="fade-up"
                     data-aos-delay="<?php echo $delay; ?>">


                    <div class="card h-100 product-card border-0 shadow-sm">


                        <!-- Product Image -->

                        <div class="product-image position-relative">

                            <img src="<?php echo htmlspecialchars($product["image"]); ?>"
                                 alt="<?php echo htmlspecialchars($product["name"]); ?>"
                                 class="card-img-top">


                            <?php if (!empty($product["badge"])): ?>

                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">

                                    <?php echo htmlspecialchars($product["badge"]); ?>

                                </span>

                            <?php endif; ?>

                        </div>


                        <!-- Product Information -->

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
                                <i class="bi bi-star-fill text-warning"></i>

                                <small class="text-muted">

                                    Customer Product

                                </small>

                            </div>


                            <!-- Description -->

                            <p class="text-muted small">

                                <?php echo htmlspecialchars($product["description"]); ?>

                            </p>


                            <!-- Price -->

                            <h6 class="text-danger mb-3">

                                ₹<?php echo number_format($product["price"]); ?>


                                <?php if (!empty($product["old_price"])): ?>

                                    <small class="text-muted text-decoration-line-through">

                                        ₹<?php echo number_format($product["old_price"]); ?>

                                    </small>

                                <?php endif; ?>

                            </h6>


                        </div>


                        <?php if ((int)$product["stock"] <= 0): ?>

    <div class="mb-2">
        <span class="badge bg-danger">
            <i class="bi bi-x-circle-fill me-1"></i>
            Out of Stock
        </span>
    </div>
<a
    href="add_to_wishlist.php?product_id=<?php echo (int)$product["id"]; ?>"
    class="btn btn-outline-danger w-100 btn-sm mb-2"
>
    <i class="bi bi-heart me-1"></i>
    Add to Wishlist
</a>
    <button
        type="button"
        class="btn btn-danger w-100 btn-sm"
        disabled>

        <i class="bi bi-x-circle me-1"></i>
        Out of Stock
    </button>
    
    
<?php elseif ((int)$product["stock"] <= 5): ?>

    <div class="mb-2">
        <span class="badge bg-warning text-dark">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            Only <?php echo (int)$product["stock"]; ?> left
        </span>
    </div>
<a
    href="add_to_wishlist.php?product_id=<?php echo (int)$product["id"]; ?>"
    class="btn btn-outline-danger w-100 btn-sm mb-2"
>
    <i class="bi bi-heart me-1"></i>
    Add to Wishlist
</a>
    <a
        href="add_to_cart.php?product_id=<?php echo (int)$product["id"]; ?>"
        class="btn btn-primary w-100 btn-sm">

        <i class="bi bi-cart-plus me-1"></i>
        Add to Cart

    </a>
    

<?php else: ?>

    <div class="mb-2">
        <span class="badge bg-success">
            <i class="bi bi-check-circle-fill me-1"></i>
            In Stock
        </span>
    </div>
    <a
    href="add_to_wishlist.php?product_id=<?php echo (int)$product["id"]; ?>"
    class="btn btn-outline-danger w-100 btn-sm mb-2"
>
    <i class="bi bi-heart me-1"></i>
    Add to Wishlist
</a>
    <a
        href="add_to_cart.php?product_id=<?php echo (int)$product["id"]; ?>"
        class="btn btn-primary w-100 btn-sm">

        <i class="bi bi-cart-plus me-1"></i>
        Add to Cart

    </a>

<?php endif; ?>


                    </div>

                </div>


            <?php

                $delay += 50;

                endwhile;

            ?>


        <?php else: ?>


            <div class="col-12">

                <div class="alert alert-info text-center">

                    <i class="bi bi-info-circle me-2"></i>

                    No products are currently available.

                </div>

            </div>


        <?php endif; ?>


        </div>

    </div>

</section>


<?php include 'footer.php'; ?>


<!-- Back To Top -->

<button id="backToTopBtn"
        class="btn btn-primary rounded-circle position-fixed"
        title="Back to top">

    <i class="bi bi-arrow-up"></i>

</button>


<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>


<!-- AOS JS -->

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js">
</script>


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

