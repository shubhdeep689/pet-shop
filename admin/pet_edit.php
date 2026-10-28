
<?php

require_once "auth.php";
require_once "../config/db.php";

$message = "";
$messageType = "";

/* =========================
   CHECK PET ID
========================= */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: pets.php");
    exit();
}

$pet_id = (int) $_GET["id"];

if ($pet_id <= 0) {
    header("Location: pets.php");
    exit();
}

/* =========================
   GET PET DETAILS
========================= */

$stmt = $conn->prepare(
    "SELECT *
     FROM pets
     WHERE id = ?"
);

if (!$stmt) {
    die("Database error. Please try again.");
}

$stmt->bind_param("i", $pet_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    header("Location: pets.php");
    exit();
}

$pet = $result->fetch_assoc();
$stmt->close();

/* =========================
   DEFAULT VALUES
========================= */

$name = $pet["name"];
$category = $pet["category"];
$price = $pet["price"];
$description = $pet["description"];
$age = $pet["age"];
$quantity = (int) $pet["quantity"];
$availability = $pet["availability"];
$image = $pet["image"];

$oldImage = $pet["image"];

/* =========================
   UPDATE PET
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $age = trim($_POST["age"] ?? "");
    $quantityInput = trim($_POST["quantity"] ?? "");

    $image = $oldImage;

    $manualImage = trim($_POST["image"] ?? "");

    /* =========================
       VALIDATION
    ========================= */

    if (
        $name === "" ||
        $category === "" ||
        $price === "" ||
        $description === "" ||
        $age === "" ||
        $quantityInput === ""
    ) {

        $message = "Please fill in all required fields.";
        $messageType = "danger";

    } elseif (
        !is_numeric($price) ||
        !is_finite((float) $price) ||
        (float) $price < 0
    ) {

        $message = "Please enter a valid price.";
        $messageType = "danger";

    } elseif (
        !ctype_digit($quantityInput) ||
        (float) $quantityInput > 2147483647
    ) {

        $message = "Quantity must be a valid non-negative whole number.";
        $messageType = "danger";

    } elseif (
        !in_array(
            $category,
            ["dog", "cat", "bird", "fish", "small"],
            true
        )
    ) {

        $message = "Please select a valid pet category.";
        $messageType = "danger";

    } else {

        $quantity = (int) $quantityInput;
        $price = (float) $price;

        /*
         * Automatically determine availability.
         *
         * Quantity 0 = Sold
         * Quantity 1 or more = Available
         */

        $availability = ($quantity > 0)
            ? "Available"
            : "Sold";

        /* =========================
           IMAGE UPLOAD
        ========================= */

        $uploadError = false;

        if (
            isset($_FILES["image_file"]) &&
            $_FILES["image_file"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            $file = $_FILES["image_file"];

            if ($file["error"] !== UPLOAD_ERR_OK) {

                $message = "There was an error uploading the image.";
                $messageType = "danger";
                $uploadError = true;

            } elseif ($file["size"] > 5 * 1024 * 1024) {

                $message = "Image size must be less than 5 MB.";
                $messageType = "danger";
                $uploadError = true;

            } else {

                $extension = strtolower(
                    pathinfo($file["name"], PATHINFO_EXTENSION)
                );

                $allowedExtensions = [
                    "jpg",
                    "jpeg",
                    "png",
                    "webp",
                    "gif"
                ];

                $imageInfo = @getimagesize($file["tmp_name"]);

                $allowedMimeTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/webp",
                    "image/gif"
                ];

                $mimeType = $imageInfo["mime"] ?? "";

                if (
                    !in_array($extension, $allowedExtensions, true) ||
                    $imageInfo === false ||
                    !in_array($mimeType, $allowedMimeTypes, true)
                ) {

                    $message = "Invalid image. Use JPG, JPEG, PNG, WEBP or GIF.";
                    $messageType = "danger";
                    $uploadError = true;

                } else {

                    $uploadDirectory = "../IMAGES/";

                    if (
                        !is_dir($uploadDirectory) &&
                        !mkdir($uploadDirectory, 0755, true) &&
                        !is_dir($uploadDirectory)
                    ) {

                        $message = "Unable to create the image upload directory.";
                        $messageType = "danger";
                        $uploadError = true;

                    } elseif (!is_writable($uploadDirectory)) {

                        $message = "The image upload directory is not writable.";
                        $messageType = "danger";
                        $uploadError = true;

                    } else {

                        try {
                            $uniqueName = "pet_" .
                                bin2hex(random_bytes(8)) .
                                "." . $extension;
                        } catch (Exception $e) {
                            $uniqueName = "pet_" .
                                uniqid("", true) .
                                "." . $extension;
                        }

                        $destination = $uploadDirectory . $uniqueName;

                        if (
                            move_uploaded_file(
                                $file["tmp_name"],
                                $destination
                            )
                        ) {

                            $image = "IMAGES/" . $uniqueName;

                        } else {

                            $message = "Unable to save the uploaded image.";
                            $messageType = "danger";
                            $uploadError = true;

                        }
                    }
                }
            }
        }

        /* =========================
           MANUAL IMAGE PATH
        ========================= */

        $noNewUpload =
            !isset($_FILES["image_file"]) ||
            $_FILES["image_file"]["error"] === UPLOAD_ERR_NO_FILE;

        if (
            !$uploadError &&
            $noNewUpload &&
            $manualImage !== ""
        ) {

            /*
             * Prevent absolute paths and directory traversal.
             * Example accepted path: IMAGES/dog.jpg
             */

            $normalizedPath = str_replace("\\", "/", $manualImage);

            if (
                str_starts_with($normalizedPath, "/") ||
                preg_match('/^[a-zA-Z]:/', $normalizedPath) ||
                preg_match('~(^|/)\.\.(/|$)~', $normalizedPath)
            ) {

                $message = "Please enter a valid relative image path.";
                $messageType = "danger";

            } else {

                $image = $normalizedPath;

            }
        }

        /* =========================
           UPDATE DATABASE
        ========================= */

        if ($message === "") {

            $stmt = $conn->prepare(
                "UPDATE pets
                 SET
                    name = ?,
                    category = ?,
                    price = ?,
                    image = ?,
                    description = ?,
                    age = ?,
                    quantity = ?,
                    availability = ?
                 WHERE id = ?"
            );

            if (!$stmt) {

                $message = "Database error. Please try again.";
                $messageType = "danger";

            } else {

                $stmt->bind_param(
                    "ssdsssisi",
                    $name,
                    $category,
                    $price,
                    $image,
                    $description,
                    $age,
                    $quantity,
                    $availability,
                    $pet_id
                );

                if ($stmt->execute()) {

                    $stmt->close();

                    header("Location: pets.php?success=updated");
                    exit();

                } else {

                    $message = "Unable to update pet. Please try again.";
                    $messageType = "danger";

                    $stmt->close();

                }
            }
        }
    }
}

/* =========================
   STOCK STATUS
========================= */

$stockStatus = ($quantity > 0) ? "Available" : "Sold Out";
$stockClass = ($quantity > 0) ? "success" : "danger";

/* =========================
   IMAGE PREVIEW PATH
========================= */

$imagePreviewPath = "../" . ltrim($image, "/");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Pet - Brar Pet Shop Admin</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>

body {
    background: #f5f7fb;
    font-family: Arial, sans-serif;
}

.sidebar {
    min-height: 100vh;
    background: #111827;
    width: 250px;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    padding: 20px 15px;
}

.sidebar-brand {
    color: white;
    font-size: 22px;
    font-weight: bold;
    text-decoration: none;
    display: block;
    padding: 10px 12px 25px;
}

.sidebar-brand i {
    color: #60a5fa;
}

.sidebar-link {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #cbd5e1;
    text-decoration: none;
    padding: 12px 14px;
    border-radius: 10px;
    margin-bottom: 6px;
}

.sidebar-link:hover,
.sidebar-link.active {
    background: #2563eb;
    color: white;
}

.main {
    margin-left: 250px;
    padding: 25px;
}

.topbar {
    background: white;
    border-radius: 15px;
    padding: 18px 22px;
    margin-bottom: 25px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.05);
}

.form-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.05);
}

.form-label {
    font-weight: 600;
}

.form-control,
.form-select {
    border-radius: 10px;
    padding: 11px 13px;
}

textarea.form-control {
    min-height: 130px;
}

.image-preview {
    width: 100%;
    height: 260px;
    object-fit: contain;
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 10px;
}

.required {
    color: #dc3545;
}

.pet-id {
    background: #eff6ff;
    color: #2563eb;
    border-radius: 20px;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
}

.upload-box {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 18px;
    background: #f8fafc;
}

.upload-box:hover {
    border-color: #2563eb;
    background: #f1f5ff;
}

.or-divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 15px 0;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
}

.or-divider::before,
.or-divider::after {
    content: "";
    height: 1px;
    background: #e2e8f0;
    flex: 1;
}

.current-image-text {
    font-size: 13px;
    color: #64748b;
    word-break: break-all;
}

.stock-card {
    border-radius: 12px;
    padding: 18px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}

.stock-number {
    font-size: 30px;
    font-weight: bold;
}

@media (max-width: 900px) {

    .sidebar {
        width: 70px;
        padding: 15px 8px;
    }

    .sidebar-brand span,
    .sidebar-link span {
        display: none;
    }

    .sidebar-link {
        justify-content: center;
    }

    .main {
        margin-left: 70px;
    }
}

@media (max-width: 576px) {

    .main {
        padding: 15px;
    }

    .form-card {
        padding: 20px;
    }

    .topbar {
        padding: 15px;
        gap: 12px;
        flex-wrap: wrap;
    }
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <a href="index.php" class="sidebar-brand">
        <i class="bi bi-shop"></i>
        <span>Brar Admin</span>
    </a>

    <a href="index.php" class="sidebar-link">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    <a href="pets.php" class="sidebar-link active">
        <i class="bi bi-heart-fill"></i>
        <span>Pets</span>
    </a>

    <a href="products.php" class="sidebar-link">
        <i class="bi bi-box-seam"></i>
        <span>Products</span>
    </a>

    <a href="users.php" class="sidebar-link">
        <i class="bi bi-people-fill"></i>
        <span>Users</span>
    </a>

    <a href="../index.php" class="sidebar-link">
        <i class="bi bi-globe"></i>
        <span>View Website</span>
    </a>

    <a href="logout.php" class="sidebar-link text-danger mt-4">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </a>

</div>

<!-- MAIN -->

<div class="main">

    <!-- TOPBAR -->

    <div class="topbar d-flex justify-content-between align-items-center">

        <div>
            <h3 class="mb-1 fw-bold">
                <i class="bi bi-pencil-square text-primary me-2"></i>
                Edit Pet
            </h3>

            <small class="text-muted">
                Update pet details, stock quantity and image
            </small>
        </div>

        <div class="d-flex align-items-center gap-2">

            <span class="pet-id">
                Pet #<?php echo $pet_id; ?>
            </span>

            <a href="pets.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>

        </div>

    </div>

    <!-- MESSAGE -->

    <?php if ($message !== ""): ?>

        <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show">

            <i class="bi bi-exclamation-circle me-2"></i>

            <?php echo htmlspecialchars($message); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    <?php endif; ?>

    <!-- FORM -->

    <div class="form-card">

        <form
            method="POST"
            action="pet_edit.php?id=<?php echo $pet_id; ?>"
            enctype="multipart/form-data">

            <div class="row g-4">

                <!-- LEFT SIDE -->

                <div class="col-lg-8">

                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Pet Information
                    </h5>

                    <!-- NAME -->

                    <div class="mb-3">

                        <label class="form-label">
                            Pet Name <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            maxlength="150"
                            value="<?php echo htmlspecialchars($name); ?>"
                            required>

                    </div>

                    <!-- CATEGORY AND AGE -->

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Category <span class="required">*</span>
                            </label>

                            <select name="category" class="form-select" required>

                                <option value="">Select Category</option>

                                <?php
                                $categories = [
                                    "dog" => "Dog",
                                    "cat" => "Cat",
                                    "bird" => "Bird",
                                    "fish" => "Fish",
                                    "small" => "Small Pet"
                                ];

                                foreach ($categories as $value => $label):
                                ?>

                                    <option
                                        value="<?php echo htmlspecialchars($value); ?>"
                                        <?php echo $category === $value ? "selected" : ""; ?>>

                                        <?php echo htmlspecialchars($label); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Age <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                name="age"
                                class="form-control"
                                maxlength="50"
                                value="<?php echo htmlspecialchars($age); ?>"
                                placeholder="e.g. 2 years"
                                required>

                        </div>

                    </div>

                    <!-- PRICE -->

                    <div class="mb-3">

                        <label class="form-label">
                            Price (₹) <span class="required">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">₹</span>

                            <input
                                type="number"
                                name="price"
                                class="form-control"
                                min="0"
                                step="0.01"
                                value="<?php echo htmlspecialchars((string)$price); ?>"
                                required>

                        </div>

                    </div>

                    <!-- QUANTITY -->

                    <div class="mb-4">

                        <label class="form-label">
                            Available Quantity <span class="required">*</span>
                        </label>

                        <input
                            type="number"
                            name="quantity"
                            id="quantity"
                            class="form-control"
                            min="0"
                            max="2147483647"
                            step="1"
                            value="<?php echo htmlspecialchars((string)$quantity); ?>"
                            required>

                        <div class="form-text">
                            Enter the number of pets currently available.
                            Use 0 when no pets are available.
                        </div>

                    </div>

                    <!-- STOCK STATUS -->

                    <div class="stock-card mb-4">

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                            <div>

                                <div class="text-muted small mb-1">
                                    Current Stock
                                </div>

                                <div class="stock-number" id="stockNumber">
                                    <?php echo $quantity; ?>
                                </div>

                            </div>

                            <div class="text-end">

                                <span
                                    id="stockBadge"
                                    class="badge bg-<?php echo $stockClass; ?> fs-6">

                                    <?php echo htmlspecialchars($stockStatus); ?>

                                </span>

                                <div class="small text-muted mt-2">
                                    Availability updates automatically
                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- DESCRIPTION -->

                    <div class="mb-3">

                        <label class="form-label">
                            Description <span class="required">*</span>
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            required><?php echo htmlspecialchars($description); ?></textarea>

                    </div>

                    <!-- IMAGE MANAGEMENT -->

                    <h5 class="fw-bold mt-4 mb-3">
                        <i class="bi bi-image text-primary me-2"></i>
                        Pet Image
                    </h5>

                    <div class="upload-box">

                        <label class="form-label mb-2">
                            <i class="bi bi-cloud-arrow-up text-primary me-1"></i>
                            Upload New Image
                        </label>

                        <input
                            type="file"
                            name="image_file"
                            id="imageFile"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">

                        <div class="form-text">
                            Maximum size: 5 MB. Formats: JPG, JPEG, PNG, WEBP and GIF.
                        </div>

                    </div>

                    <div class="or-divider">OR</div>

                    <label class="form-label">
                        <i class="bi bi-link-45deg text-primary me-1"></i>
                        Image Path
                    </label>

                    <input
                        type="text"
                        name="image"
                        id="imageInput"
                        class="form-control"
                        value="<?php echo htmlspecialchars($image); ?>"
                        placeholder="e.g. IMAGES/dog.jpg">

                    <div class="form-text">
                        Enter a relative path to an existing image.
                        Leave unchanged to keep the current image.
                    </div>

                    <div class="alert alert-info mt-3 mb-0">

                        <i class="bi bi-info-circle me-2"></i>

                        Uploading a new image takes priority over the image path.

                    </div>

                    <!-- BUTTONS -->

                    <div class="d-flex gap-2 mt-4 flex-wrap">

                        <button type="submit" class="btn btn-primary px-4">

                            <i class="bi bi-check-circle me-1"></i>
                            Update Pet

                        </button>

                        <a href="pets.php" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>

                    </div>

                </div>

                <!-- RIGHT SIDE -->

                <div class="col-lg-4">

                    <div class="sticky-lg-top" style="top: 20px;">

                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-image text-primary me-2"></i>
                            Image Preview
                        </h5>

                        <img
                            id="imagePreview"
                            src="<?php echo htmlspecialchars($imagePreviewPath); ?>"
                            alt="<?php echo htmlspecialchars($name); ?>"
                            class="image-preview">

                        <div class="alert alert-light border mt-3">

                            <i class="bi bi-lightbulb text-warning me-2"></i>

                            <small>
                                Select a new image or enter another image path
                                to replace the current image.
                            </small>

                        </div>

                        <div class="card border-0 bg-light">

                            <div class="card-body">

                                <h6 class="fw-bold mb-3">
                                    Pet Summary
                                </h6>

                                <p class="small text-muted mb-2">
                                    <strong>Name:</strong>
                                    <?php echo htmlspecialchars($name); ?>
                                </p>

                                <p class="small text-muted mb-2">
                                    <strong>Category:</strong>
                                    <?php echo htmlspecialchars(ucfirst($category)); ?>
                                </p>

                                <p class="small text-muted mb-2">
                                    <strong>Age:</strong>
                                    <?php echo htmlspecialchars($age); ?>
                                </p>

                                <p class="small text-muted mb-2">
                                    <strong>Price:</strong>
                                    ₹<?php echo number_format((float)$price, 2); ?>
                                </p>

                                <p class="small text-muted mb-2">
                                    <strong>Quantity:</strong>
                                    <?php echo $quantity; ?>
                                </p>

                                <p class="small text-muted mb-0">
                                    <strong>Availability:</strong>
                                    <?php echo htmlspecialchars($stockStatus); ?>
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/* =========================
   QUANTITY PREVIEW
========================= */

const quantityInput = document.getElementById("quantity");
const stockNumber = document.getElementById("stockNumber");
const stockBadge = document.getElementById("stockBadge");

function updateStockPreview() {

    const quantity = Math.max(
        0,
        parseInt(quantityInput.value, 10) || 0
    );

    stockNumber.textContent = quantity;

    if (quantity > 0) {

        stockBadge.textContent = "Available";
        stockBadge.className = "badge bg-success fs-6";

    } else {

        stockBadge.textContent = "Sold Out";
        stockBadge.className = "badge bg-danger fs-6";

    }
}

quantityInput.addEventListener("input", updateStockPreview);


/* =========================
   IMAGE PREVIEW
========================= */

const imageFile = document.getElementById("imageFile");
const imageInput = document.getElementById("imageInput");
const imagePreview = document.getElementById("imagePreview");

imageFile.addEventListener("change", function () {

    const file = this.files[0];

    if (file) {

        const allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/webp",
            "image/gif"
        ];

        if (!allowedTypes.includes(file.type)) {
            alert("Please select a valid JPG, PNG, WEBP or GIF image.");
            this.value = "";
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert("Image size must be less than 5 MB.");
            this.value = "";
            return;
        }

        imageInput.value = "";

        imagePreview.src = URL.createObjectURL(file);
    }
});


/* =========================
   MANUAL IMAGE PATH PREVIEW
========================= */

imageInput.addEventListener("input", function () {

    const path = this.value.trim();

    if (path !== "") {

        imageFile.value = "";

        imagePreview.src = "../" + path.replace(/^\/+/, "");

    }
});


/* =========================
   IMAGE ERROR
========================= */

imagePreview.addEventListener("error", function () {

    this.alt = "Image not found";

});

</script>

</body>
</html>

