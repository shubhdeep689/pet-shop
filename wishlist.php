
<?php

session_start();

require_once "config/db.php";

// Login check
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = (int)$_SESSION["user_id"];

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";


// Get wishlist items
$sql = "
    SELECT
        w.id AS wishlist_id,
        w.pet_id,
        w.product_id,

        p.name AS pet_name,
        p.price AS pet_price,
        p.image AS pet_image,
        p.description AS pet_description,
        p.availability AS pet_availability,

        pr.name AS product_name,
        pr.price AS product_price,
        pr.image AS product_image,
        pr.description AS product_description,
        pr.stock AS product_stock

    FROM wishlist w

    LEFT JOIN pets p
        ON w.pet_id = p.id

    LEFT JOIN products pr
        ON w.product_id = pr.id

    WHERE w.user_id = ?

    ORDER BY w.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
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

    <meta
        name="description"
        content="My Wishlist | Brar Pet Shop">

    <title>My Wishlist | Brar Pet Shop</title>


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <!-- AOS -->
    <link
        href="https://unpkg.com/aos@2.3.1/dist/aos.css"
        rel="stylesheet">


    <!-- Custom CSS -->
    <link
        rel="stylesheet"
        href="style.css">

</head>


<body>

<?php include "navbar.php"; ?>


<!-- Wishlist Section -->
<section class="py-5 bg-light">

    <div class="container">


        <!-- Alerts -->

        <?php if ($success === "added"): ?>

            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert">

                <i class="bi bi-heart-fill me-2"></i>

                Item added to your wishlist successfully.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>


        <?php elseif ($success === "removed"): ?>

            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert">

                <i class="bi bi-check-circle-fill me-2"></i>

                Item removed from your wishlist.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>


        <?php elseif ($error === "already_wishlisted"): ?>

            <div
                class="alert alert-warning alert-dismissible fade show"
                role="alert">

                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                This item is already in your wishlist.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>


        <?php elseif ($error === "remove_failed"): ?>

            <div
                class="alert alert-danger alert-dismissible fade show"
                role="alert">

                <i class="bi bi-x-circle-fill me-2"></i>

                The item could not be removed. Please try again.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

        <?php endif; ?>


        <!-- Header -->

        <div
            class="text-center mb-5"
            data-aos="fade-up">

            <h1 class="display-5 fw-bold text-primary">

                <i class="bi bi-heart-fill me-2"></i>

                My Wishlist

            </h1>

            <p class="text-muted mb-0">

                Your saved pets and products

            </p>

        </div>


        <?php if ($result->num_rows > 0): ?>


            <!-- Wishlist Cards -->

            <div class="row g-4">


                <?php while ($item = $result->fetch_assoc()): ?>


                    <?php

                    // Determine item type

                    if ($item["pet_id"] !== null) {

                        $itemType = "Pet";

                        $itemId = (int)$item["pet_id"];

                        $name = $item["pet_name"];

                        $price = (float)$item["pet_price"];

                        $image = $item["pet_image"];

                        $description = $item["pet_description"];

                        $availability = $item["pet_availability"];

                        $isAvailable = ($availability === "Available");

                    } else {

                        $itemType = "Product";

                        $itemId = (int)$item["product_id"];

                        $name = $item["product_name"];

                        $price = (float)$item["product_price"];

                        $image = $item["product_image"];

                        $description = $item["product_description"];

                        $stock = (int)$item["product_stock"];

                        $isAvailable = ($stock > 0);

                    }

                    ?>


                    <!-- Card Column -->

                    <div
                        class="col-lg-4 col-md-6"
                        data-aos="fade-up">


                        <div class="card h-100 border-0 shadow-sm">


                            <!-- Image -->

                            <div
                                style="height:220px; overflow:hidden;">

                                <img
                                    src="<?php echo htmlspecialchars($image); ?>"
                                    alt="<?php echo htmlspecialchars($name); ?>"
                                    class="w-100 h-100"
                                    style="object-fit:cover;">

                            </div>


                            <!-- Card Body -->

                            <div class="card-body">


                                <!-- Type + Remove -->

                                <div
                                    class="d-flex justify-content-between align-items-start">

                                    <span class="badge bg-primary">

                                        <?php echo htmlspecialchars($itemType); ?>

                                    </span>


                                    <a
                                        href="remove_from_wishlist.php?id=<?php echo (int)$item["wishlist_id"]; ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Remove from Wishlist">

                                        <i class="bi bi-trash"></i>

                                    </a>

                                </div>


                                <!-- Name -->

                                <h5 class="card-title mt-3">

                                    <?php echo htmlspecialchars($name); ?>

                                </h5>


                                <!-- Description -->

                                <p class="text-muted small">

                                    <?php
                                    echo htmlspecialchars(
                                        $description ?? ""
                                    );
                                    ?>

                                </p>


                                <!-- Price -->

                                <h5 class="text-danger">

                                    ₹<?php echo number_format($price); ?>

                                </h5>


                                <!-- Availability -->

                                <?php if ($itemType === "Pet"): ?>


                                    <?php if ($availability === "Available"): ?>

                                        <span class="badge bg-success">

                                            <i class="bi bi-check-circle me-1"></i>

                                            Available

                                        </span>


                                    <?php elseif ($availability === "Reserved"): ?>

                                        <span
                                            class="badge bg-warning text-dark">

                                            <i class="bi bi-clock me-1"></i>

                                            Reserved

                                        </span>


                                    <?php else: ?>

                                        <span class="badge bg-danger">

                                            <i class="bi bi-x-circle me-1"></i>

                                            Sold

                                        </span>

                                    <?php endif; ?>


                                <?php else: ?>


                                    <?php if ($stock > 0): ?>

                                        <span class="badge bg-success">

                                            <i class="bi bi-check-circle me-1"></i>

                                            In Stock

                                        </span>


                                    <?php else: ?>

                                        <span class="badge bg-danger">

                                            <i class="bi bi-x-circle me-1"></i>

                                            Out of Stock

                                        </span>

                                    <?php endif; ?>


                                <?php endif; ?>


                            </div>


                            <!-- Card Footer -->

                            <div
                                class="card-footer bg-white border-0 pb-4">


                                <?php if ($isAvailable): ?>


                                    <?php if ($itemType === "Pet"): ?>

                                        <a
                                            href="add_to_cart.php?id=<?php echo $itemId; ?>"
                                            class="btn btn-primary w-100">

                                            <i class="bi bi-cart-plus me-1"></i>

                                            Add to Cart

                                        </a>


                                    <?php else: ?>

                                        <a
                                            href="add_to_cart.php?product_id=<?php echo $itemId; ?>"
                                            class="btn btn-primary w-100">

                                            <i class="bi bi-cart-plus me-1"></i>

                                            Add to Cart

                                        </a>

                                    <?php endif; ?>


                                <?php else: ?>

                                    <button
                                        type="button"
                                        class="btn btn-secondary w-100"
                                        disabled>

                                        <i class="bi bi-x-circle me-1"></i>

                                        Not Available

                                    </button>

                                <?php endif; ?>


                            </div>

                        </div>

                    </div>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <!-- Empty Wishlist -->

            <div
                class="text-center py-5"
                data-aos="fade-up">


                <i
                    class="bi bi-heart display-1 text-muted">
                </i>


                <h3 class="mt-4">

                    Your Wishlist is Empty

                </h3>


                <p class="text-muted">

                    Save your favourite pets and products here.

                </p>


                <div
                    class="d-flex justify-content-center gap-2 mt-4">

                    <a
                        href="pets.php"
                        class="btn btn-primary">

                        <i class="bi bi-heart me-1"></i>

                        Browse Pets

                    </a>


                    <a
                        href="product.php"
                        class="btn btn-outline-primary">

                        <i class="bi bi-box-seam me-1"></i>

                        Browse Products

                    </a>

                </div>

            </div>


        <?php endif; ?>


    </div>

</section>


<?php include "footer.php"; ?>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


<!-- AOS -->

<script
    src="https://unpkg.com/aos@2.3.1/dist/aos.js">
</script>


<script>

AOS.init({
    duration: 800,
    once: true
});

</script>

</body>

</html>
