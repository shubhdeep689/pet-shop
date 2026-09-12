```php
<?php

session_start();

require_once "config/db.php";


// Get search term
$query = trim($_GET["q"] ?? "");

$petResults = [];
$productResults = [];


// Search only when something is entered
if ($query !== "") {

    $search = "%" . $query . "%";


    // ===============================
    // SEARCH PETS
    // ===============================

    $stmt = $conn->prepare(
        "SELECT *
         FROM pets
         WHERE name LIKE ?
            OR category LIKE ?
            OR description LIKE ?
         ORDER BY id ASC"
    );

    $stmt->bind_param(
        "sss",
        $search,
        $search,
        $search
    );

    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $petResults[] = $row;

    }

    $stmt->close();



    // ===============================
    // SEARCH PRODUCTS
    // ===============================

    $stmt = $conn->prepare(
        "SELECT *
         FROM products
         WHERE name LIKE ?
            OR description LIKE ?
            OR badge LIKE ?
         ORDER BY id ASC"
    );

    $stmt->bind_param(
        "sss",
        $search,
        $search,
        $search
    );

    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $productResults[] = $row;

    }

    $stmt->close();
}


$totalResults =
    count($petResults) + count($productResults);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="Search Brar Pet Shop">

    <title>
        Search<?php echo $query !== "" ? " - " . htmlspecialchars($query) : ""; ?>
        | Brar Pet Shop
    </title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet">


    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">


    <link rel="stylesheet"
          href="style.css">

</head>


<body>


<?php include 'navbar.php'; ?>


<!-- ===============================
     SEARCH HEADER
=============================== -->

<section class="py-5 bg-light">

    <div class="container">

        <div class="text-center">

            <i class="bi bi-search display-4 text-primary"></i>

            <h1 class="mt-3">
                Search Results
            </h1>


            <?php if ($query !== ""): ?>

                <p class="text-muted mb-0">

                    Results for:

                    <strong>
                        "<?php echo htmlspecialchars($query); ?>"
                    </strong>

                </p>

            <?php else: ?>

                <p class="text-muted mb-0">

                    Search for pets and products.

                </p>

            <?php endif; ?>

        </div>

    </div>

</section>



<!-- ===============================
     SEARCH CONTENT
=============================== -->

<section class="py-5">

    <div class="container">


        <?php if ($query === ""): ?>


            <div class="text-center py-5">

                <i class="bi bi-search display-1 text-muted"></i>

                <h4 class="mt-3">

                    What are you looking for?

                </h4>

                <p class="text-muted">

                    Use the search box in the navbar.

                </p>

            </div>


        <?php elseif ($totalResults === 0): ?>


            <!-- NO RESULTS -->

            <div class="text-center py-5">

                <i class="bi bi-emoji-frown display-1 text-muted"></i>

                <h4 class="mt-3">

                    No results found

                </h4>

                <p class="text-muted">

                    Try searching for another pet or product.

                </p>


                <a href="pets.php"
                   class="btn btn-primary me-2">

                    <i class="bi bi-paw me-1"></i>

                    Browse Pets

                </a>


                <a href="product.php"
                   class="btn btn-outline-primary">

                    <i class="bi bi-box-seam me-1"></i>

                    Browse Products

                </a>

            </div>


        <?php else: ?>


            <!-- RESULT COUNT -->

            <div class="mb-4">

                <h5>

                    <i class="bi bi-list-check text-primary me-2"></i>

                    <?php echo $totalResults; ?>

                    result<?php echo $totalResults != 1 ? "s" : ""; ?>

                    found

                </h5>

            </div>



            <!-- ===============================
                 PET RESULTS
            =============================== -->

            <?php if (count($petResults) > 0): ?>


                <h3 class="mb-4">

                    <i class="bi bi-paw-fill text-success me-2"></i>

                    Pets

                </h3>


                <div class="row g-4 mb-5">


                    <?php foreach ($petResults as $pet): ?>


                        <div class="col-lg-3 col-md-6">


                            <div class="card h-100 shadow-sm">


                                <img
                                    src="<?php echo htmlspecialchars($pet["image"]); ?>"
                                    class="card-img-top"
                                    alt="<?php echo htmlspecialchars($pet["name"]); ?>"
                                    style="height:220px; object-fit:cover;"
                                >


                                <div class="card-body">


                                    <span class="badge bg-success mb-2">

                                        Pet

                                    </span>


                                    <h5 class="card-title">

                                        <?php echo htmlspecialchars($pet["name"]); ?>

                                    </h5>


                                    <p class="card-text text-muted">

                                        <?php echo htmlspecialchars($pet["description"]); ?>

                                    </p>


                                    <p class="mb-2">

                                        <strong>Age:</strong>

                                        <?php echo htmlspecialchars($pet["age"]); ?>

                                    </p>


                                    <h5 class="text-danger">

                                        ₹<?php echo number_format($pet["price"]); ?>

                                    </h5>

                                </div>


                                <div class="card-footer bg-white">


                                    <a
                                        href="add_to_cart.php?id=<?php echo $pet["id"]; ?>"
                                        class="btn btn-primary w-100"
                                    >

                                        <i class="bi bi-cart-plus me-1"></i>

                                        Add to Cart

                                    </a>


                                </div>


                            </div>


                        </div>


                    <?php endforeach; ?>


                </div>


            <?php endif; ?>



            <!-- ===============================
                 PRODUCT RESULTS
            =============================== -->

            <?php if (count($productResults) > 0): ?>


                <h3 class="mb-4">

                    <i class="bi bi-box-seam-fill text-primary me-2"></i>

                    Products

                </h3>


                <div class="row g-4">


                    <?php foreach ($productResults as $product): ?>


                        <div class="col-lg-4 col-md-6">


                            <div class="card h-100 shadow-sm">


                                <img
                                    src="<?php echo htmlspecialchars($product["image"]); ?>"
                                    class="card-img-top"
                                    alt="<?php echo htmlspecialchars($product["name"]); ?>"
                                    style="height:220px; object-fit:cover;"
                                >


                                <div class="card-body">


                                    <?php if (!empty($product["badge"])): ?>

                                        <span class="badge bg-danger mb-2">

                                            <?php echo htmlspecialchars($product["badge"]); ?>

                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-primary mb-2">

                                            Product

                                        </span>

                                    <?php endif; ?>


                                    <h5 class="card-title">

                                        <?php echo htmlspecialchars($product["name"]); ?>

                                    </h5>


                                    <p class="card-text text-muted">

                                        <?php echo htmlspecialchars($product["description"]); ?>

                                    </p>


                                    <div class="mb-3">


                                        <?php if (!empty($product["old_price"])): ?>

                                            <del class="text-muted me-2">

                                                ₹<?php echo number_format($product["old_price"]); ?>

                                            </del>

                                        <?php endif; ?>


                                        <strong class="text-danger fs-5">

                                            ₹<?php echo number_format($product["price"]); ?>

                                        </strong>


                                    </div>


                                </div>


                                <div class="card-footer bg-white">


                                    <a
                                        href="add_to_cart.php?product_id=<?php echo $product["id"]; ?>"
                                        class="btn btn-primary w-100"
                                    >

                                        <i class="bi bi-cart-plus me-1"></i>

                                        Add to Cart

                                    </a>


                                </div>


                            </div>


                        </div>


                    <?php endforeach; ?>


                </div>


            <?php endif; ?>


        <?php endif; ?>


    </div>

</section>



<?php include 'footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>


<script src="script.js"></script>


</body>

</html>
```
