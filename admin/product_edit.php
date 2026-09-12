```php
<?php

require_once "auth.php";
require_once "../config/db.php";

$error = "";

$name = "";
$price = "";
$old_price = "";
$image = "";
$badge = "";
$description = "";


/* =========================
   CHECK PRODUCT ID
========================= */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: products.php?error=not_found");
    exit();

}

$product_id = (int) $_GET["id"];


/* =========================
   FETCH PRODUCT
========================= */

$stmt = $conn->prepare(
    "SELECT *
     FROM products
     WHERE id = ?"
);

if (!$stmt) {

    die("Database query failed: " . $conn->error);

}

$stmt->bind_param("i", $product_id);
$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: products.php?error=not_found");
    exit();

}


$product = $result->fetch_assoc();

$stmt->close();


/* =========================
   LOAD CURRENT VALUES
========================= */

$name = $product["name"];
$price = $product["price"];
$old_price = $product["old_price"];
$image = $product["image"];
$badge = $product["badge"];
$description = $product["description"];


/*
 * Keep this separately so that
 * the current image can remain unchanged.
 */

$oldImage = $product["image"];


/* =========================
   UPDATE PRODUCT
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $old_price = trim($_POST["old_price"] ?? "");
    $badge = trim($_POST["badge"] ?? "");
    $description = trim($_POST["description"] ?? "");

    /*
     * Keep existing image by default.
     */

    $image = $oldImage;

    /*
     * Manual image path.
     */

    $manualImage = trim($_POST["image"] ?? "");


    /* =========================
       VALIDATION
    ========================= */

    if ($name === "") {

        $error = "Product name is required.";

    } elseif (
        $price === "" ||
        !is_numeric($price) ||
        (float)$price < 0
    ) {

        $error = "Please enter a valid price.";

    } elseif (
        $old_price !== "" &&
        (
            !is_numeric($old_price) ||
            (float)$old_price < 0
        )
    ) {

        $error = "Please enter a valid old price.";

    } else {


        /* =========================
           IMAGE UPLOAD
        ========================= */

        if (
            isset($_FILES["image_file"]) &&
            $_FILES["image_file"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            $file = $_FILES["image_file"];


            /* Upload error */

            if ($file["error"] !== UPLOAD_ERR_OK) {

                $error =
                    "There was an error uploading the image.";

            } elseif ($file["size"] > 5 * 1024 * 1024) {

                $error =
                    "Image size must be less than 5 MB.";

            } else {


                /* Get extension */

                $extension = strtolower(
                    pathinfo(
                        $file["name"],
                        PATHINFO_EXTENSION
                    )
                );


                /* Allowed formats */

                $allowedExtensions = [
                    "jpg",
                    "jpeg",
                    "png",
                    "webp",
                    "gif"
                ];


                if (
                    !in_array(
                        $extension,
                        $allowedExtensions,
                        true
                    )
                ) {

                    $error =
                        "Invalid image format. Please use JPG, JPEG, PNG, WEBP or GIF.";

                } else {


                    /* Check actual image */

                    $imageInfo =
                        @getimagesize($file["tmp_name"]);


                    if ($imageInfo === false) {

                        $error =
                            "The uploaded file is not a valid image.";

                    } else {


                        /* =========================
                           UPLOAD DIRECTORY
                        ========================= */

                        $uploadDirectory = "../IMAGES/";


                        /*
                         * Create IMAGES folder
                         * if it does not exist.
                         */

                        if (!is_dir($uploadDirectory)) {

                            mkdir(
                                $uploadDirectory,
                                0755,
                                true
                            );

                        }


                        /* =========================
                           UNIQUE FILE NAME
                        ========================= */

                        try {

                            $uniqueName =
                                "product_" .
                                bin2hex(
                                    random_bytes(8)
                                ) .
                                "." .
                                $extension;

                        } catch (Exception $e) {

                            $uniqueName =
                                "product_" .
                                uniqid() .
                                "." .
                                $extension;

                        }


                        $destination =
                            $uploadDirectory .
                            $uniqueName;


                        /* =========================
                           MOVE IMAGE
                        ========================= */

                        if (
                            move_uploaded_file(
                                $file["tmp_name"],
                                $destination
                            )
                        ) {

                            /*
                             * Store new image path
                             * in database.
                             */

                            $image =
                                "IMAGES/" .
                                $uniqueName;

                        } else {

                            $error =
                                "Unable to save the uploaded image.";

                        }

                    }

                }

            }

        }


        /* =========================
           MANUAL IMAGE PATH
        ========================= */

        /*
         * If no new image was uploaded,
         * use manual path if provided.
         *
         * Otherwise keep old image.
         */

        $noNewUpload =
            !isset($_FILES["image_file"]) ||
            $_FILES["image_file"]["error"] === UPLOAD_ERR_NO_FILE;


        if (
            $error === "" &&
            $noNewUpload &&
            $manualImage !== ""
        ) {

            $image = $manualImage;

        }


        /* =========================
           UPDATE DATABASE
        ========================= */

        if ($error === "") {

            $priceValue = (float)$price;

            $oldPriceValue = null;

            if ($old_price !== "") {

                $oldPriceValue =
                    (float)$old_price;

            }


            $stmt = $conn->prepare(
                "UPDATE products
                 SET
                    name = ?,
                    price = ?,
                    old_price = ?,
                    image = ?,
                    badge = ?,
                    description = ?
                 WHERE id = ?"
            );


            if (!$stmt) {

                $error =
                    "Database error: " .
                    $conn->error;

            } else {


                $stmt->bind_param(
                    "sddsssi",
                    $name,
                    $priceValue,
                    $oldPriceValue,
                    $image,
                    $badge,
                    $description,
                    $product_id
                );


                if ($stmt->execute()) {

                    $stmt->close();

                    header(
                        "Location: products.php?success=updated"
                    );

                    exit();

                } else {

                    $error =
                        "Unable to update product: " .
                        $stmt->error;

                    $stmt->close();

                }

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

<title>
    Edit Product | Brar Pet Shop
</title>


<!-- BOOTSTRAP -->

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">


<!-- BOOTSTRAP ICONS -->

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    rel="stylesheet">


<style>

* {
    box-sizing: border-box;
}


body {

    margin: 0;

    background: #f5f7fb;

    font-family: Arial, sans-serif;

    color: #1f2937;

}


/* =========================
   SIDEBAR
========================= */

.sidebar {

    width: 250px;

    min-height: 100vh;

    position: fixed;

    left: 0;

    top: 0;

    background: #111827;

    color: white;

    padding: 24px 16px;

    z-index: 1000;

}


.brand {

    display: flex;

    align-items: center;

    gap: 10px;

    font-size: 21px;

    font-weight: 700;

    padding: 8px 10px 28px;

}


.brand i {

    color: #60a5fa;

    font-size: 28px;

}


.menu-title {

    color: #9ca3af;

    font-size: 11px;

    text-transform: uppercase;

    letter-spacing: 1px;

    padding: 0 12px;

    margin-bottom: 10px;

}


.sidebar-link {

    display: flex;

    align-items: center;

    gap: 12px;

    text-decoration: none;

    color: #d1d5db;

    padding: 12px 14px;

    border-radius: 10px;

    margin-bottom: 5px;

    transition: 0.2s;

}


.sidebar-link:hover {

    background: #1f2937;

    color: white;

}


.sidebar-link.active {

    background: #2563eb;

    color: white;

}


.sidebar-link i {

    font-size: 18px;

    width: 22px;

}


/* =========================
   MAIN
========================= */

.main {

    margin-left: 250px;

    padding: 28px;

}


/* =========================
   TOPBAR
========================= */

.topbar {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 25px;

}


.page-title {

    margin: 0;

    font-size: 28px;

    font-weight: 700;

}


.page-subtitle {

    color: #6b7280;

    margin-top: 5px;

    margin-bottom: 0;

}


.admin-badge {

    background: white;

    border: 1px solid #e5e7eb;

    border-radius: 12px;

    padding: 10px 15px;

    display: flex;

    align-items: center;

    gap: 10px;

}


.admin-icon {

    width: 38px;

    height: 38px;

    background: #dbeafe;

    color: #2563eb;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

}


/* =========================
   FORM
========================= */

.form-card {

    background: white;

    border: 1px solid #e5e7eb;

    border-radius: 16px;

    padding: 28px;

    box-shadow:
        0 3px 12px rgba(0, 0, 0, 0.03);

}


.section-title {

    font-size: 18px;

    font-weight: 700;

    margin-bottom: 20px;

}


.form-label {

    font-weight: 600;

    font-size: 14px;

    margin-bottom: 7px;

}


.form-control,
.form-select {

    border-radius: 10px;

    padding: 11px 13px;

    border: 1px solid #d1d5db;

}


.form-control:focus,
.form-select:focus {

    border-color: #2563eb;

    box-shadow:
        0 0 0 3px rgba(37, 99, 235, 0.12);

}


textarea.form-control {

    min-height: 130px;

    resize: vertical;

}


/* =========================
   IMAGE
========================= */

.image-preview-box {

    border: 2px dashed #d1d5db;

    border-radius: 14px;

    min-height: 260px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #f8fafc;

    overflow: hidden;

}


.image-preview-box img {

    max-width: 100%;

    max-height: 250px;

    object-fit: contain;

    display: none;

}


.preview-placeholder {

    text-align: center;

    color: #9ca3af;

}


.preview-placeholder i {

    font-size: 50px;

    display: block;

    margin-bottom: 10px;

}


.upload-box {

    border: 2px dashed #d1d5db;

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


.required {

    color: #dc2626;

}


.help-text {

    color: #6b7280;

    font-size: 12px;

    margin-top: 6px;

}


.product-id {

    display: inline-block;

    background: #f1f5f9;

    color: #64748b;

    border-radius: 20px;

    padding: 5px 10px;

    font-size: 12px;

    margin-top: 8px;

}


.current-image {

    background: #eff6ff;

    border-radius: 10px;

    padding: 10px 12px;

    margin-top: 12px;

    word-break: break-all;

    font-size: 12px;

    color: #475569;

}


/* =========================
   MOBILE
========================= */

@media (max-width: 767px) {

    .sidebar {

        position: relative;

        width: 100%;

        min-height: auto;

    }


    .main {

        margin-left: 0;

        padding: 15px;

    }


    .topbar {

        align-items: flex-start;

        flex-direction: column;

        gap: 15px;

    }


    .admin-badge {

        width: 100%;

    }


    .form-card {

        padding: 20px;

    }

}

</style>

</head>


<body>


<!-- SIDEBAR -->

<div class="sidebar">


<div class="brand">

    <i class="bi bi-heart-pulse-fill"></i>

    <span>
        Brar Pet Shop
    </span>

</div>


<div class="menu-title">
    Main Menu
</div>


<a
    href="index.php"
    class="sidebar-link">

    <i class="bi bi-speedometer2"></i>

    Dashboard

</a>


<a
    href="pets.php"
    class="sidebar-link">

    <i class="bi bi-heart-fill"></i>

    Pets

</a>


<a
    href="products.php"
    class="sidebar-link active">

    <i class="bi bi-box-seam-fill"></i>

    Products

</a>


<a
    href="users.php"
    class="sidebar-link">

    <i class="bi bi-people-fill"></i>

    Users

</a>


<div class="menu-title mt-4">
    Website
</div>


<a
    href="../index.php"
    class="sidebar-link">

    <i class="bi bi-globe2"></i>

    View Website

</a>


<a
    href="logout.php"
    class="sidebar-link">

    <i class="bi bi-box-arrow-right"></i>

    Logout

</a>


</div>


<!-- MAIN -->

<div class="main">


<!-- TOPBAR -->

<div class="topbar">


<div>

    <h1 class="page-title">

        Edit Product

    </h1>


    <p class="page-subtitle">

        Update product information and pricing.

    </p>


    <span class="product-id">

        Product #<?php echo $product_id; ?>

    </span>

</div>


<div class="admin-badge">


<div class="admin-icon">

    <i class="bi bi-person-fill"></i>

</div>


<div>

    <small class="text-muted d-block">

        Logged in as

    </small>


    <strong>

        <?php
        echo htmlspecialchars(
            $_SESSION["admin_username"] ?? "Admin"
        );
        ?>

    </strong>

</div>


</div>


</div>


<!-- ERROR -->

<?php if ($error !== ""): ?>

<div
    class="alert alert-danger alert-dismissible fade show">

    <i
        class="bi bi-exclamation-triangle-fill me-2">
    </i>

    <?php echo htmlspecialchars($error); ?>


    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>

<?php endif; ?>


<!-- FORM -->

<div class="form-card">


<form
    method="POST"
    action="product_edit.php?id=<?php echo $product_id; ?>"
    enctype="multipart/form-data">


<div class="row g-4">


<!-- LEFT -->

<div class="col-lg-8">


<div class="section-title">

    Product Information

</div>


<!-- NAME -->

<div class="mb-3">

<label class="form-label">

    Product Name

    <span class="required">*</span>

</label>


<input
    type="text"
    name="name"
    class="form-control"
    placeholder="e.g. Premium Pet Food"
    value="<?php echo htmlspecialchars($name); ?>"
    required>

</div>


<!-- PRICES -->

<div class="row">


<div class="col-md-6 mb-3">

<label class="form-label">

    Current Price (₹)

    <span class="required">*</span>

</label>


<input
    type="number"
    name="price"
    class="form-control"
    min="0"
    step="0.01"
    value="<?php echo htmlspecialchars($price); ?>"
    required>

</div>


<div class="col-md-6 mb-3">

<label class="form-label">

    Old Price (₹)

</label>


<input
    type="number"
    name="old_price"
    class="form-control"
    min="0"
    step="0.01"
    placeholder="3000"
    value="<?php echo htmlspecialchars($old_price ?? ""); ?>">


<div class="help-text">

    Leave empty if there is no old price.

</div>

</div>


</div>


<!-- BADGE -->

<div class="mb-3">

<label class="form-label">

    Product Badge

</label>


<select
    name="badge"
    class="form-select">


<option
    value=""
    <?php
    echo ($badge === "")
        ? "selected"
        : "";
    ?>>

    No Badge

</option>


<option
    value="NEW"
    <?php
    echo ($badge === "NEW")
        ? "selected"
        : "";
    ?>>

    NEW

</option>


<option
    value="10% OFF"
    <?php
    echo ($badge === "10% OFF")
        ? "selected"
        : "";
    ?>>

    10% OFF

</option>


<option
    value="15% OFF"
    <?php
    echo ($badge === "15% OFF")
        ? "selected"
        : "";
    ?>>

    15% OFF

</option>


<option
    value="20% OFF"
    <?php
    echo ($badge === "20% OFF")
        ? "selected"
        : "";
    ?>>

    20% OFF

</option>


<option
    value="25% OFF"
    <?php
    echo ($badge === "25% OFF")
        ? "selected"
        : "";
    ?>>

    25% OFF

</option>


<option
    value="30% OFF"
    <?php
    echo ($badge === "30% OFF")
        ? "selected"
        : "";
    ?>>

    30% OFF

</option>


</select>

</div>


<!-- IMAGE -->

<div class="mb-3">

<label class="form-label">

    Product Image

</label>


<!-- UPLOAD -->

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
    accept=".jpg,.jpeg,.png,.webp,.gif,image/*">


<div class="help-text">

    Upload directly from your computer.

    Maximum size:
    <strong>5 MB</strong>.

    Allowed:
    JPG, JPEG, PNG, WEBP, GIF.

</div>

</div>


<div class="or-divider">

    OR

</div>


<!-- MANUAL PATH -->

<label class="form-label">

    <i class="bi bi-link-45deg text-primary me-1"></i>

    Image Path

</label>


<input
    type="text"
    name="image"
    id="imageInput"
    class="form-control"
    placeholder="IMAGES/product.jpg"
    value="<?php echo htmlspecialchars($image); ?>">


<div class="help-text">

    Enter an existing image path if you don't
    want to upload a new image.

</div>


<div class="current-image">

    <i class="bi bi-image me-1"></i>

    Current:
    <strong>
        <?php echo htmlspecialchars($oldImage); ?>
    </strong>

</div>


<div class="alert alert-info mt-3 mb-0">

    <i class="bi bi-info-circle me-2"></i>

    <small>

        Leave the image unchanged to keep the
        current image.

        If you upload a new image,
        it will automatically replace the
        current image path.

    </small>

</div>


</div>


<!-- DESCRIPTION -->

<div class="mb-3">

<label class="form-label">

    Description

</label>


<textarea
    name="description"
    class="form-control"
    placeholder="Enter product description..."><?php echo htmlspecialchars($description); ?></textarea>

</div>


<!-- BUTTONS -->

<div class="d-flex gap-2 mt-4">


<button
    type="submit"
    class="btn btn-primary px-4">

    <i class="bi bi-check-lg me-1"></i>

    Update Product

</button>


<a
    href="products.php"
    class="btn btn-outline-secondary px-4">

    <i class="bi bi-x-lg me-1"></i>

    Cancel

</a>


</div>


</div>


<!-- RIGHT -->

<div class="col-lg-4">


<div class="section-title">

    Image Preview

</div>


<div class="image-preview-box">


<img
    id="imagePreview"
    src=""
    alt="Product Preview">


<div
    id="previewPlaceholder"
    class="preview-placeholder">


<i class="bi bi-image"></i>


<div>

    Image Preview

</div>


</div>


</div>


<div class="help-text mt-2">

    Current image is displayed above.
    Upload a new image or enter another path
    to change it.

</div>


</div>


</div>


</form>


</div>


</div>


<!-- IMAGE PREVIEW -->

<script>

const imageFile =
    document.getElementById("imageFile");

const imageInput =
    document.getElementById("imageInput");

const imagePreview =
    document.getElementById("imagePreview");

const previewPlaceholder =
    document.getElementById("previewPlaceholder");


/* =========================
   SHOW PREVIEW
========================= */

function showImagePreview(src) {

    if (!src) {

        imagePreview.style.display = "none";

        previewPlaceholder.style.display = "block";

        return;

    }


    imagePreview.src = src;


    imagePreview.onload = function () {

        imagePreview.style.display = "block";

        previewPlaceholder.style.display = "none";

    };


    imagePreview.onerror = function () {

        imagePreview.style.display = "none";

        previewPlaceholder.style.display = "block";

    };

}


/* =========================
   FILE UPLOAD
========================= */

imageFile.addEventListener(
    "change",
    function () {

        const file = this.files[0];


        if (file) {

            /*
             * Show selected file immediately.
             */

            const imageURL =
                URL.createObjectURL(file);

            showImagePreview(imageURL);


            /*
             * Clear manual path.
             * Uploaded image will take priority.
             */

            imageInput.value = "";

        }

    }
);


/* =========================
   MANUAL IMAGE PATH
========================= */

imageInput.addEventListener(
    "input",
    function () {

        /*
         * Remove selected upload
         * when manual path is entered.
         */

        imageFile.value = "";


        const path =
            this.value.trim();


        if (path !== "") {

            showImagePreview(
                "../" + path
            );

        }

    }
);


/* =========================
   INITIAL PREVIEW
========================= */

showImagePreview(
    "../<?php echo htmlspecialchars($image); ?>"
);

</script>


<!-- BOOTSTRAP JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>
```
