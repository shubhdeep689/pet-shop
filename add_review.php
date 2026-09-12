<?php

session_start();

require_once "config/db.php";

// Login check
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = (int)$_SESSION["user_id"];

$petId = isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;

$productId = isset($_GET["product_id"])
    ? (int)$_GET["product_id"]
    : 0;

if ($petId > 0 && $productId > 0) {
    header("Location: index.php");
    exit();
}

if ($petId <= 0 && $productId <= 0) {
    header("Location: index.php");
    exit();
}

$itemName = "";
$itemType = "";
$itemId = 0;
$error = "";
$success = "";

/* =========================
   GET PET
========================= */

if ($petId > 0) {

    $stmt = $conn->prepare("
        SELECT id, name
        FROM pets
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $petId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: pets.php?error=not_found");
        exit();
    }

    $item = $result->fetch_assoc();

    $itemName = $item["name"];
    $itemType = "Pet";
    $itemId = $petId;
}

/* =========================
   GET PRODUCT
========================= */

if ($productId > 0) {

    $stmt = $conn->prepare("
        SELECT id, name
        FROM products
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $productId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: product.php?error=not_found");
        exit();
    }

    $item = $result->fetch_assoc();

    $itemName = $item["name"];
    $itemType = "Product";
    $itemId = $productId;
}

/* =========================
   SUBMIT REVIEW
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $rating = isset($_POST["rating"])
        ? (int)$_POST["rating"]
        : 0;

    $review = trim($_POST["review"] ?? "");

    if ($rating < 1 || $rating > 5) {

        $error = "Please select a rating between 1 and 5 stars.";

    } else {

        /* Check if user already reviewed this item */

        if ($petId > 0) {

            $stmt = $conn->prepare("
                SELECT id
                FROM reviews
                WHERE user_id = ?
                AND pet_id = ?
                LIMIT 1
            ");

            $stmt->bind_param("ii", $userId, $petId);

        } else {

            $stmt = $conn->prepare("
                SELECT id
                FROM reviews
                WHERE user_id = ?
                AND product_id = ?
                LIMIT 1
            ");

            $stmt->bind_param("ii", $userId, $productId);
        }

        $stmt->execute();

        $existing = $stmt->get_result();

        if ($existing->num_rows > 0) {

            $error = "You have already reviewed this item.";

        } else {

            /* Insert review */

            if ($petId > 0) {

                $stmt = $conn->prepare("
                    INSERT INTO reviews
                    (user_id, pet_id, product_id, rating, review)
                    VALUES (?, ?, NULL, ?, ?)
                ");

                $stmt->bind_param(
                    "iiis",
                    $userId,
                    $petId,
                    $rating,
                    $review
                );

            } else {

                $stmt = $conn->prepare("
                    INSERT INTO reviews
                    (user_id, pet_id, product_id, rating, review)
                    VALUES (?, NULL, ?, ?, ?)
                ");

                $stmt->bind_param(
                    "iiis",
                    $userId,
                    $productId,
                    $rating,
                    $review
                );
            }

            if ($stmt->execute()) {

                if ($petId > 0) {
                    header(
                        "Location: pets.php?success=review_added"
                    );
                } else {
                    header(
                        "Location: product.php?success=review_added"
                    );
                }

                exit();

            } else {

                $error = "Review could not be submitted.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Add Review - Brar Pet Shop</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="style.css">

</head>

<body>

<?php include "navbar.php"; ?>


<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card shadow-sm border-0">

                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">

                        <i
                            class="bi bi-star-fill text-warning"
                            style="font-size: 3rem;">
                        </i>

                        <h2 class="fw-bold mt-3">
                            Write a Review
                        </h2>

                        <p class="text-muted mb-0">
                            Share your experience with
                            <strong>
                                <?php echo htmlspecialchars($itemName); ?>
                            </strong>
                        </p>

                    </div>


                    <?php if ($error !== ""): ?>

                        <div class="alert alert-danger">

                            <i class="bi bi-exclamation-circle me-2"></i>

                            <?php echo htmlspecialchars($error); ?>

                        </div>

                    <?php endif; ?>


                    <form method="POST">


                        <!-- Rating -->

                        <div class="mb-4">

                            <label class="form-label fw-bold">
                                Your Rating
                            </label>

                            <div
                                class="d-flex gap-2"
                                id="ratingStars">

                                <?php for ($i = 1; $i <= 5; $i++): ?>

                                    <button
                                        type="button"
                                        class="btn btn-outline-warning rating-star"
                                        data-rating="<?php echo $i; ?>"
                                        style="font-size: 1.4rem;">

                                        <i class="bi bi-star"></i>

                                    </button>

                                <?php endfor; ?>

                            </div>

                            <input
                                type="hidden"
                                name="rating"
                                id="rating"
                                value="">

                            <small class="text-muted">
                                Select 1 to 5 stars
                            </small>

                        </div>


                        <!-- Review -->

                        <div class="mb-4">

                            <label
                                for="review"
                                class="form-label fw-bold">

                                Your Review

                            </label>

                            <textarea
                                name="review"
                                id="review"
                                class="form-control"
                                rows="5"
                                maxlength="1000"
                                placeholder="Write your experience..."></textarea>

                            <small class="text-muted">
                                Maximum 1000 characters
                            </small>

                        </div>


                        <!-- Buttons -->

                        <div class="d-flex gap-2">

                            <?php if ($petId > 0): ?>

                                <a
                                    href="pets.php"
                                    class="btn btn-secondary">

                                    <i class="bi bi-arrow-left me-1"></i>

                                    Back

                                </a>

                            <?php else: ?>

                                <a
                                    href="product.php"
                                    class="btn btn-secondary">

                                    <i class="bi bi-arrow-left me-1"></i>

                                    Back

                                </a>

                            <?php endif; ?>


                            <button
                                type="submit"
                                class="btn btn-warning flex-grow-1">

                                <i class="bi bi-star-fill me-1"></i>

                                Submit Review

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<?php include "footer.php"; ?>


<script>

const stars = document.querySelectorAll(".rating-star");
const ratingInput = document.getElementById("rating");

stars.forEach((star) => {

    star.addEventListener("click", function () {

        const rating = parseInt(
            this.getAttribute("data-rating")
        );

        ratingInput.value = rating;

        stars.forEach((item) => {

            const itemRating = parseInt(
                item.getAttribute("data-rating")
            );

            const icon = item.querySelector("i");

            if (itemRating <= rating) {

                icon.classList.remove("bi-star");

                icon.classList.add("bi-star-fill");

            } else {

                icon.classList.remove("bi-star-fill");

                icon.classList.add("bi-star");
            }

        });

    });

});

</script>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>