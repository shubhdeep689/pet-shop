<?php

require_once "auth.php";
require_once "../config/db.php";


// =====================================================
// MESSAGES
// =====================================================

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";


// =====================================================
// SEARCH & FILTER
// =====================================================

$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");
$availability = trim($_GET["availability"] ?? "");


// =====================================================
// CHECK DATABASE CONNECTION
// =====================================================

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed. Check config/db.php.");
}


// =====================================================
// SYNCHRONIZE AVAILABILITY WITH QUANTITY
// quantity > 0 = Available
// quantity = 0 = Sold
// =====================================================

$syncSql = "
    UPDATE pets
    SET availability = CASE
        WHEN quantity > 0 THEN 'Available'
        ELSE 'Sold'
    END
    WHERE availability IS NULL
       OR availability != CASE
            WHEN quantity > 0 THEN 'Available'
            ELSE 'Sold'
       END
";

if (!$conn->query($syncSql)) {
    die("Availability synchronization failed: " .
        htmlspecialchars($conn->error, ENT_QUOTES, "UTF-8"));
}


// =====================================================
// DASHBOARD STATISTICS
// =====================================================

$statsSql = "
    SELECT
        COUNT(*) AS total,
        COALESCE(SUM(quantity > 0), 0) AS available,
        COALESCE(SUM(quantity = 0), 0) AS sold
    FROM pets
";

$resultStats = $conn->query($statsSql);

if (!$resultStats) {
    die("Unable to load statistics: " .
        htmlspecialchars($conn->error, ENT_QUOTES, "UTF-8"));
}

$stats = $resultStats->fetch_assoc();

$totalPets = (int) ($stats["total"] ?? 0);
$availablePets = (int) ($stats["available"] ?? 0);
$soldPets = (int) ($stats["sold"] ?? 0);

// Quantity-based inventory does not use Reserved as a stock status.
$reservedPets = 0;


// =====================================================
// GET CATEGORIES FROM DATABASE
// =====================================================

$categories = [];

$categoryResult = $conn->query("
    SELECT DISTINCT category
    FROM pets
    WHERE category IS NOT NULL
      AND category != ''
    ORDER BY category ASC
");

if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row["category"];
    }
}


// =====================================================
// BUILD SEARCH QUERY
// =====================================================

$sql = "
    SELECT
        id,
        name,
        category,
        age,
        price,
        quantity,
        availability,
        image,
        description
    FROM pets
    WHERE 1 = 1
";

$params = [];
$types = "";


// Search pet details

if ($search !== "") {

    $sql .= "
        AND (
            name LIKE ?
            OR description LIKE ?
            OR category LIKE ?
            OR age LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "ssss";
}


// Filter by category

if ($category !== "") {

    $sql .= " AND category = ? ";

    $params[] = $category;
    $types .= "s";
}


// Filter by quantity-based availability

if ($availability === "Available") {

    $sql .= " AND quantity > 0 ";

} elseif ($availability === "Sold") {

    $sql .= " AND quantity = 0 ";
}


$sql .= " ORDER BY id DESC";


// =====================================================
// EXECUTE QUERY
// =====================================================

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database query failed: " .
        htmlspecialchars($conn->error, ENT_QUOTES, "UTF-8"));
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    die("Unable to fetch pets: " .
        htmlspecialchars($stmt->error, ENT_QUOTES, "UTF-8"));
}

$result = $stmt->get_result();


// =====================================================
// HELPER FUNCTIONS
// =====================================================

function availabilityBadge($availability)
{
    if ($availability === "Available") {
        return "bg-success";
    }

    if ($availability === "Sold") {
        return "bg-danger";
    }

    return "bg-secondary";
}


function availabilityIcon($availability)
{
    if ($availability === "Available") {
        return "bi-check-circle-fill";
    }

    if ($availability === "Sold") {
        return "bi-x-circle-fill";
    }

    return "bi-question-circle";
}


function e($value)
{
    return htmlspecialchars(
        (string) ($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Pets | Brar Pet Shop Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    >

    <style>

        body {
            background: #f5f7fb;
            font-family: Arial, sans-serif;
        }

        /* SIDEBAR */

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #212529;
            position: fixed;
            left: 0;
            top: 0;
            padding: 25px 15px;
            z-index: 1000;
        }

        .sidebar-brand {
            color: white;
            font-size: 22px;
            font-weight: 700;
            text-decoration: none;
            display: block;
            padding: 0 10px 25px;
        }

        .sidebar-brand i {
            color: #ffc107;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #adb5bd;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 6px;
            transition: 0.2s;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: #343a40;
            color: white;
        }

        .sidebar-link.active {
            border-left: 4px solid #ffc107;
        }

        /* MAIN */

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .top-title {
            background: white;
            border-radius: 15px;
            padding: 22px 25px;
            margin-bottom: 25px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        }

        /* STATISTICS */

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f3f5;
            font-size: 21px;
        }

        .stat-number {
            font-size: 27px;
            font-weight: 700;
            margin-top: 12px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 14px;
        }

        /* TABLE */

        .table-card {
            background: white;
            border-radius: 15px;
            padding: 22px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        }

        .pet-image {
            width: 65px;
            height: 65px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #eee;
            background: #f8f9fa;
        }

        .pet-name {
            font-weight: 700;
        }

        .filter-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
        }

        .quantity-badge {
            display: inline-block;
            min-width: 42px;
            padding: 7px 12px;
            border-radius: 20px;
            font-weight: bold;
            text-align: center;
        }

        .quantity-in-stock {
            color: #146c43;
            background: #d1e7dd;
        }

        .quantity-out-of-stock {
            color: #842029;
            background: #f8d7da;
        }

        .table th {
            white-space: nowrap;
        }

        @media (max-width: 768px) {

            .sidebar {
                width: 100%;
                min-height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

        }

    </style>

    <link rel="stylesheet" href="sidebar.css">

</head>

<body>


<?php require __DIR__ . "/sidebar.php"; ?>


<!-- =====================================================
     MAIN CONTENT
====================================================== -->

<div class="main-content">


    <!-- HEADER -->

    <div class="top-title">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <h3 class="fw-bold mb-1">
                    <i class="bi bi-heart-fill me-2"></i>
                    Manage Pets
                </h3>

                <p class="text-muted mb-0">
                    Manage pet information, stock quantities and availability.
                </p>

            </div>

            <a href="pet_add.php" class="btn btn-primary">

                <i class="bi bi-plus-lg me-1"></i>
                Add New Pet

            </a>

        </div>

    </div>


    <!-- SUCCESS MESSAGES -->

    <?php if ($success === "added"): ?>

        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-2"></i>
            Pet added successfully.
        </div>

    <?php elseif ($success === "updated"): ?>

        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-2"></i>
            Pet updated successfully.
        </div>

    <?php elseif ($success === "deleted"): ?>

        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-2"></i>
            Pet deleted successfully.
        </div>

    <?php endif; ?>


    <!-- ERROR MESSAGE -->

    <?php if ($error !== ""): ?>

        <div class="alert alert-danger">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <?php echo e($error); ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         STATISTICS
    ====================================================== -->

    <div class="row g-4 mb-4">


        <!-- TOTAL -->

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon">
                    <i class="bi bi-heart"></i>
                </div>

                <div class="stat-number">
                    <?php echo $totalPets; ?>
                </div>

                <div class="stat-label">
                    Total Pets
                </div>

            </div>

        </div>


        <!-- AVAILABLE -->

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <div class="stat-number text-success">
                    <?php echo $availablePets; ?>
                </div>

                <div class="stat-label">
                    Available
                </div>

            </div>

        </div>


        <!-- RESERVED -->

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon text-warning">
                    <i class="bi bi-clock-fill"></i>
                </div>

                <div class="stat-number text-warning">
                    <?php echo $reservedPets; ?>
                </div>

                <div class="stat-label">
                    Reserved
                </div>

            </div>

        </div>


        <!-- SOLD -->

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon text-danger">
                    <i class="bi bi-x-circle-fill"></i>
                </div>

                <div class="stat-number text-danger">
                    <?php echo $soldPets; ?>
                </div>

                <div class="stat-label">
                    Sold Out
                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         SEARCH AND FILTERS
    ====================================================== -->

    <div class="table-card mb-4">

        <div class="filter-box">

            <form method="GET" action="pets.php">

                <div class="row g-2">


                    <!-- SEARCH -->

                    <div class="col-lg-5">

                        <label class="form-label small fw-semibold">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search pet name, category, age..."
                            value="<?php echo e($search); ?>"
                        >

                    </div>


                    <!-- CATEGORY -->

                    <div class="col-lg-3">

                        <label class="form-label small fw-semibold">
                            Category
                        </label>

                        <select name="category" class="form-select">

                            <option value="">
                                All Categories
                            </option>

                            <?php foreach ($categories as $cat): ?>

                                <option
                                    value="<?php echo e($cat); ?>"
                                    <?php echo ($category === $cat) ? "selected" : ""; ?>
                                >
                                    <?php echo e(ucfirst($cat)); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- AVAILABILITY -->

                    <div class="col-lg-2">

                        <label class="form-label small fw-semibold">
                            Availability
                        </label>

                        <select name="availability" class="form-select">

                            <option value="">
                                All
                            </option>

                            <option
                                value="Available"
                                <?php echo ($availability === "Available") ? "selected" : ""; ?>
                            >
                                Available
                            </option>

                            <option
                                value="Sold"
                                <?php echo ($availability === "Sold") ? "selected" : ""; ?>
                            >
                                Sold Out
                            </option>

                        </select>

                    </div>


                    <!-- BUTTONS -->

                    <div class="col-lg-2 d-flex align-items-end gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary flex-grow-1"
                            title="Search"
                        >
                            <i class="bi bi-search"></i>
                        </button>

                        <a
                            href="pets.php"
                            class="btn btn-outline-secondary"
                            title="Reset filters"
                        >
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- =====================================================
         PET INVENTORY TABLE
    ====================================================== -->

    <div class="table-card">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

            <div>

                <h5 class="fw-bold mb-1">
                    Pet Inventory
                </h5>

                <small class="text-muted">

                    <?php echo $result->num_rows; ?>

                    pet(s) found

                </small>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table align-middle table-hover">

                <thead class="table-light">

                    <tr>

                        <th>Pet</th>

                        <th>Category</th>

                        <th>Age</th>

                        <th>Price</th>

                        <th>Quantity</th>

                        <th>Availability</th>

                        <th class="text-end">Actions</th>

                    </tr>

                </thead>


                <tbody>


                <?php if ($result->num_rows === 0): ?>

                    <tr>

                        <td colspan="7" class="text-center py-5">

                            <i class="bi bi-search fs-1 text-muted"></i>

                            <p class="text-muted mt-2 mb-0">
                                No pets found.
                            </p>

                        </td>

                    </tr>


                <?php else: ?>


                    <?php while ($pet = $result->fetch_assoc()): ?>

                        <?php

                            $petId = (int) $pet["id"];

                            $petQuantity = (int) ($pet["quantity"] ?? 0);

                            // Derive status directly from quantity.
                            $petStatus = ($petQuantity > 0)
                                ? "Available"
                                : "Sold";

                            $petImage = trim((string) ($pet["image"] ?? ""));

                            // Image paths in the database are relative to the project root.
                            $imagePath = "../" . ltrim($petImage, "/");

                        ?>


                        <tr>


                            <!-- PET IMAGE AND NAME -->

                            <td>

                                <div class="d-flex align-items-center">

                                    <?php if ($petImage !== ""): ?>

                                        <img
                                            src="<?php echo e($imagePath); ?>"
                                            alt="<?php echo e($pet["name"]); ?>"
                                            class="pet-image me-3"
                                            onerror="this.onerror=null;this.src='https://placehold.co/65x65?text=Pet';"
                                        >

                                    <?php else: ?>

                                        <img
                                            src="https://placehold.co/65x65?text=Pet"
                                            alt="Pet image unavailable"
                                            class="pet-image me-3"
                                        >

                                    <?php endif; ?>


                                    <div>

                                        <div class="pet-name">

                                            <?php echo e($pet["name"]); ?>

                                        </div>

                                        <small class="text-muted">

                                            ID #<?php echo $petId; ?>

                                        </small>

                                    </div>

                                </div>

                            </td>


                            <!-- CATEGORY -->

                            <td>

                                <span class="badge bg-light text-dark border">

                                    <?php echo e(ucfirst($pet["category"])); ?>

                                </span>

                            </td>


                            <!-- AGE -->

                            <td>

                                <?php echo e($pet["age"]); ?>

                            </td>


                            <!-- PRICE -->

                            <td>

                                <strong>

                                    ₹<?php echo number_format(
                                        (float) $pet["price"],
                                        2
                                    ); ?>

                                </strong>

                            </td>


                            <!-- QUANTITY -->

                            <td>

                                <span class="quantity-badge <?php
                                    echo ($petQuantity > 0)
                                        ? "quantity-in-stock"
                                        : "quantity-out-of-stock";
                                ?>">

                                    <?php echo $petQuantity; ?>

                                </span>

                            </td>


                            <!-- AVAILABILITY -->

                            <td>

                                <span class="badge rounded-pill px-3 py-2 <?php
                                    echo availabilityBadge($petStatus);
                                ?>">

                                    <i class="bi <?php
                                        echo availabilityIcon($petStatus);
                                    ?> me-1"></i>

                                    <?php echo e($petStatus); ?>

                                </span>

                            </td>


                            <!-- ACTIONS -->

                            <td class="text-end">

                                <a
                                    href="pet_edit.php?id=<?php echo $petId; ?>"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Edit Pet"
                                >

                                    <i class="bi bi-pencil"></i>

                                </a>


                                <a
                                    href="pet_delete.php?id=<?php echo $petId; ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Delete Pet"
                                    onclick="return confirm('Are you sure you want to delete this pet?');"
                                >

                                    <i class="bi bi-trash"></i>

                                </a>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php endif; ?>


                </tbody>

            </table>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php

$stmt->close();
$conn->close();

?>