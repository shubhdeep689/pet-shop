
<?php

require_once "auth.php";
require_once "../config/db.php";

$message = "";
$messageType = "";

$name = "";
$category = "";
$price = "";
$description = "";
$age = "";
$availability = "Available";
$image = "";


/* ADD PET */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $age = trim($_POST["age"] ?? "");
    $availability = trim($_POST["availability"] ?? "Available");
    $image = trim($_POST["image"] ?? "");


    /* VALIDATION */

    if (
        $name === "" ||
        $category === "" ||
        $price === "" ||
        $description === "" ||
        $age === ""
    ) {

        $message = "Please fill in all required fields.";
        $messageType = "danger";

    } elseif (!is_numeric($price) || $price < 0) {

        $message = "Please enter a valid price.";
        $messageType = "danger";

    } else {


        /* =========================
           IMAGE UPLOAD
        ========================= */

        if (
            isset($_FILES["image_file"]) &&
            $_FILES["image_file"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            $file = $_FILES["image_file"];

            /* Check upload error */

            if ($file["error"] !== UPLOAD_ERR_OK) {

                $message = "There was an error uploading the image.";
                $messageType = "danger";

            } elseif ($file["size"] > 5 * 1024 * 1024) {

                $message = "Image size must be less than 5 MB.";
                $messageType = "danger";

            } else {

                /* Get extension */

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


                /* Check extension */

                if (!in_array($extension, $allowedExtensions, true)) {

                    $message =
                        "Invalid image format. Please use JPG, JPEG, PNG, WEBP or GIF.";

                    $messageType = "danger";

                } else {


                    /* Check actual image */

                    $imageInfo = @getimagesize($file["tmp_name"]);

                    if ($imageInfo === false) {

                        $message = "The uploaded file is not a valid image.";
                        $messageType = "danger";

                    } else {


                        /* Upload directory */

                        $uploadDirectory = "../IMAGES/";


                        /* Create IMAGES folder if it doesn't exist */

                        if (!is_dir($uploadDirectory)) {

                            mkdir(
                                $uploadDirectory,
                                0755,
                                true
                            );
                        }


                        /* Generate unique filename */

                        try {

                            $uniqueName =
                                "pet_" .
                                bin2hex(random_bytes(8)) .
                                "." .
                                $extension;

                        } catch (Exception $e) {

                            $uniqueName =
                                "pet_" .
                                uniqid() .
                                "." .
                                $extension;
                        }


                        $destination =
                            $uploadDirectory .
                            $uniqueName;


                        /* Move uploaded image */

                        if (
                            move_uploaded_file(
                                $file["tmp_name"],
                                $destination
                            )
                        ) {

                            /*
                             * Save path in database
                             *
                             * Example:
                             * IMAGES/pet_a8f32c91.jpg
                             */

                            $image =
                                "IMAGES/" .
                                $uniqueName;

                        } else {

                            $message =
                                "Unable to save the uploaded image.";

                            $messageType = "danger";
                        }

                    }

                }

            }

        }


        /* =========================
           MANUAL IMAGE PATH
        ========================= */

        /*
         * If no image was uploaded,
         * use the manually entered path.
         */

        if (
            $message === "" &&
            $image === "" &&
            trim($_POST["image"] ?? "") !== ""
        ) {

            $image = trim($_POST["image"]);

        }


        /* =========================
           IMAGE REQUIRED
        ========================= */

        if (
            $message === "" &&
            $image === ""
        ) {

            $message =
                "Please upload an image or enter an image path.";

            $messageType = "danger";
        }


        /* =========================
           INSERT PET
        ========================= */

        if ($message === "") {

            $stmt = $conn->prepare(
                "INSERT INTO pets
                (name, category, price, image, description, age, availability)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );


            if (!$stmt) {

                $message =
                    "Database error. Please try again.";

                $messageType = "danger";

            } else {

                $stmt->bind_param(
                    "ssdssss",
                    $name,
                    $category,
                    $price,
                    $image,
                    $description,
                    $age,
                    $availability
                );


                if ($stmt->execute()) {

                    $stmt->close();

                    header("Location: pets.php?success=added");
                    exit();

                } else {

                    $message =
                        "Unable to add pet. Please try again.";

                    $messageType = "danger";

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

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Add Pet - Brar Pet Shop Admin</title>

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
        height: 250px;
        object-fit: contain;
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 10px;
    }

    .required {
        color: #dc3545;
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


<a href="logout.php"
   class="sidebar-link text-danger mt-4">

    <i class="bi bi-box-arrow-right"></i>

    <span>Logout</span>

</a>

</div>


<!-- MAIN -->

<div class="main">


<!-- TOPBAR -->

<div class="topbar d-flex justify-content-between
            align-items-center">

    <div>

        <h3 class="mb-1 fw-bold">

            <i class="bi bi-plus-circle text-primary me-2"></i>

            Add New Pet

        </h3>

        <small class="text-muted">

            Add a new pet to your shop inventory

        </small>

    </div>


    <a href="pets.php"
       class="btn btn-outline-secondary">

        <i class="bi bi-arrow-left me-1"></i>

        Back to Pets

    </a>

</div>


<!-- MESSAGE -->

<?php if ($message !== ""): ?>

<div class="alert alert-<?php echo $messageType; ?>
            alert-dismissible fade show">

    <i class="bi bi-exclamation-circle me-2"></i>

    <?php echo htmlspecialchars($message); ?>

    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
    </button>

</div>

<?php endif; ?>


<!-- FORM -->

<div class="form-card">

<form method="POST"
      action="pet_add.php"
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

    Pet Name
    <span class="required">*</span>

</label>

<input
    type="text"
    name="name"
    class="form-control"
    placeholder="e.g. Golden Retriever"
    value="<?php echo htmlspecialchars($name); ?>"
    required
>

</div>


<!-- CATEGORY + AGE -->

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

    Category
    <span class="required">*</span>

</label>

<select name="category"
        class="form-select"
        required>

    <option value="">
        Select Category
    </option>

    <option value="dog"
        <?php echo $category === "dog" ? "selected" : ""; ?>>
        Dog
    </option>

    <option value="cat"
        <?php echo $category === "cat" ? "selected" : ""; ?>>
        Cat
    </option>

    <option value="bird"
        <?php echo $category === "bird" ? "selected" : ""; ?>>
        Bird
    </option>

    <option value="fish"
        <?php echo $category === "fish" ? "selected" : ""; ?>>
        Fish
    </option>

    <option value="small"
        <?php echo $category === "small" ? "selected" : ""; ?>>
        Small Pet
    </option>

</select>

</div>


<div class="col-md-6 mb-3">

<label class="form-label">

    Age
    <span class="required">*</span>

</label>

<input
    type="text"
    name="age"
    class="form-control"
    placeholder="e.g. 2 years"
    value="<?php echo htmlspecialchars($age); ?>"
    required
>

</div>

</div>


<!-- PRICE -->

<div class="mb-3">

<label class="form-label">

    Price (₹)
    <span class="required">*</span>

</label>

<div class="input-group">

<span class="input-group-text">
    ₹
</span>

<input
    type="number"
    name="price"
    class="form-control"
    placeholder="e.g. 45000"
    min="0"
    step="0.01"
    value="<?php echo htmlspecialchars($price); ?>"
    required
>

</div>

</div>


<!-- DESCRIPTION -->

<div class="mb-3">

<label class="form-label">

    Description
    <span class="required">*</span>

</label>

<textarea
    name="description"
    class="form-control"
    placeholder="Write a short description about this pet..."
    required><?php echo htmlspecialchars($description); ?></textarea>

</div>


<!-- AVAILABILITY -->

<div class="mb-3">

<label class="form-label">

    Availability

</label>

<select name="availability"
        class="form-select">

    <option value="Available"
        <?php echo $availability === "Available" ? "selected" : ""; ?>>
        Available
    </option>

    <option value="Sold"
        <?php echo $availability === "Sold" ? "selected" : ""; ?>>
        Sold
    </option>

    <option value="Reserved"
        <?php echo $availability === "Reserved" ? "selected" : ""; ?>>
        Reserved
    </option>

</select>

</div>


<!-- IMAGE -->

<div class="mb-3">

<label class="form-label">

    Pet Image

</label>


<div class="upload-box">

    <label class="form-label mb-2">

        <i class="bi bi-cloud-arrow-up text-primary me-1"></i>

        Upload Image

    </label>

    <input
        type="file"
        name="image_file"
        id="imageFile"
        class="form-control"
        accept=".jpg,.jpeg,.png,.webp,.gif,image/*"
    >

    <div class="form-text">

        Upload directly from your computer.
        Maximum size: <strong>5 MB</strong>.
        Allowed: JPG, JPEG, PNG, WEBP, GIF.

    </div>

</div>


<div class="or-divider">

    OR

</div>


<label class="form-label">

    <i class="bi bi-link-45deg text-primary me-1"></i>

    Enter Image Path

</label>

<input
    type="text"
    name="image"
    id="imageInput"
    class="form-control"
    placeholder="e.g. IMAGES/dog.jpg"
    value="<?php echo htmlspecialchars($image); ?>"
>


<div class="form-text">

    If you don't want to upload a file,
    enter an existing image path.

    Example:
    <strong>IMAGES/dog.jpg</strong>

</div>


<div class="alert alert-info mt-3 mb-0">

    <i class="bi bi-info-circle me-2"></i>

    <small>

        If you upload a file, the uploaded image will be used
        automatically. The image will be saved inside the
        <strong>IMAGES</strong> folder.

    </small>

</div>

</div>


<!-- BUTTONS -->

<div class="d-flex gap-2 mt-4">

<button
    type="submit"
    class="btn btn-primary px-4">

    <i class="bi bi-check-circle me-1"></i>

    Save Pet

</button>


<a href="pets.php"
   class="btn btn-outline-secondary px-4">

    Cancel

</a>

</div>


</div>


<!-- RIGHT SIDE -->

<div class="col-lg-4">


<div class="sticky-lg-top"
     style="top: 20px;">


<h5 class="fw-bold mb-4">

    <i class="bi bi-image text-primary me-2"></i>

    Image Preview

</h5>


<img
    id="imagePreview"
    src="https://via.placeholder.com/500x350?text=Pet+Image"
    alt="Pet Preview"
    class="image-preview"
>


<div class="alert alert-light border mt-3">

    <i class="bi bi-lightbulb text-warning me-2"></i>

    <small>

        You can either upload a new image
        or enter an existing image path.

        Uploaded images are automatically
        saved inside the <strong>IMAGES</strong> folder.

    </small>

</div>


<div class="card border-0 bg-light">

<div class="card-body">

<h6 class="fw-bold">

    Example

</h6>

<p class="small text-muted mb-2">

    Pet Name:
    <strong>German Shepherd</strong>

</p>

<p class="small text-muted mb-2">

    Category:
    <strong>Dog</strong>

</p>

<p class="small text-muted mb-2">

    Age:
    <strong>3 years</strong>

</p>

<p class="small text-muted mb-0">

    Price:
    <strong>₹50,000</strong>

</p>

</div>

</div>


</div>

</div>


</div>

</form>

</div>


</div>


<!-- BOOTSTRAP -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


<!-- IMAGE PREVIEW -->

<script>

const imageFile =
    document.getElementById("imageFile");

const imageInput =
    document.getElementById("imageInput");

const imagePreview =
    document.getElementById("imagePreview");


/* =========================
   FILE UPLOAD PREVIEW
========================= */

imageFile.addEventListener("change", function () {

    const file = this.files[0];

    if (file) {

        const imageURL =
            URL.createObjectURL(file);

        imagePreview.src = imageURL;

        /*
         * Clear manual path when
         * user selects an upload.
         */

        imageInput.value = "";

    }

});


/* =========================
   MANUAL PATH PREVIEW
========================= */

imageInput.addEventListener("input", function () {

    /*
     * If user enters a path,
     * remove selected upload.
     */

    imageFile.value = "";

    let path = this.value.trim();


    if (path !== "") {

        imagePreview.src =
            "../" + path;

    } else {

        imagePreview.src =
            "https://via.placeholder.com/500x350?text=Pet+Image";

    }

});


/* =========================
   IMAGE ERROR
========================= */

imagePreview.addEventListener("error", function () {

    this.src =
        "https://via.placeholder.com/500x350?text=Image+Not+Found";

});

</script>


</body>

</html>

