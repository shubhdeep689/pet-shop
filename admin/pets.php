<?php

require_once "auth.php";
require_once "../config/db.php";


/*
|--------------------------------------------------------------------------
| Messages
|--------------------------------------------------------------------------
*/

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";


/*
|--------------------------------------------------------------------------
| Search & Filter
|--------------------------------------------------------------------------
*/

$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");
$availability = trim($_GET["availability"] ?? "");


/*
|--------------------------------------------------------------------------
| Build Query
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT *
    FROM pets
    WHERE 1=1
";

$params = [];
$types = "";


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


if ($category !== "") {

    $sql .= " AND category = ?";

    $params[] = $category;

    $types .= "s";
}


if ($availability !== "") {

    $sql .= " AND availability = ?";

    $params[] = $availability;

    $types .= "s";
}


$sql .= " ORDER BY id DESC";


$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database query failed: " . $conn->error);
}


if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}


$stmt->execute();

$result = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| Dashboard Statistics
|--------------------------------------------------------------------------
*/

$totalPets = 0;
$availablePets = 0;
$reservedPets = 0;
$soldPets = 0;


$resultStats = $conn->query("
    SELECT
        COUNT(*) AS total,
        SUM(availability = 'Available') AS available,
        SUM(availability = 'Reserved') AS reserved,
        SUM(availability = 'Sold') AS sold
    FROM pets
");


if ($resultStats) {

    $stats = $resultStats->fetch_assoc();

    $totalPets = (int)($stats["total"] ?? 0);
    $availablePets = (int)($stats["available"] ?? 0);
    $reservedPets = (int)($stats["reserved"] ?? 0);
    $soldPets = (int)($stats["sold"] ?? 0);
}


function availabilityBadge($availability)
{
    switch ($availability) {

        case "Available":
            return "bg-success";

        case "Reserved":
            return "bg-warning text-dark";

        case "Sold":
            return "bg-danger";

        default:
            return "bg-secondary";
    }
}


function availabilityIcon($availability)
{
    switch ($availability) {

        case "Available":
            return "bi-check-circle-fill";

        case "Reserved":
            return "bi-clock-fill";

        case "Sold":
            return "bi-x-circle-fill";

        default:
            return "bi-question-circle";
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
        Manage Pets | Brar Pet Shop Admin
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <style>

        body {
            background: #f5f7fb;
            font-family: Arial, sans-serif;
        }


        /* ================= SIDEBAR ================= */

        .sidebar {

            width: 250px;

            min-height: 100vh;

            background: #212529;

            position: fixed;

            left: 0;
            top: 0;

            padding: 25px 15px;

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


        /* ================= MAIN ================= */

        .main-content {

            margin-left: 250px;

            padding: 30px;

        }


        .top-title {

            background: white;

            border-radius: 15px;

            padding: 22px 25px;

            margin-bottom: 25px;

            box-shadow:
                0 3px 15px rgba(0,0,0,0.05);

        }


        /* ================= STAT CARDS ================= */

        .stat-card {

            background: white;

            border-radius: 15px;

            padding: 20px;

            box-shadow:
                0 3px 15px rgba(0,0,0,0.05);

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


        /* ================= TABLE ================= */

        .table-card {

            background: white;

            border-radius: 15px;

            padding: 22px;

            box-shadow:
                0 3px 15px rgba(0,0,0,0.05);

        }


        .pet-image {

            width: 65px;

            height: 65px;

            object-fit: cover;

            border-radius: 12px;

            border: 1px solid #eee;

        }


        .pet-name {

            font-weight: 700;

        }


        .filter-box {

            background: #f8f9fa;

            border-radius: 12px;

            padding: 15px;

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

</head>


<body>


<!-- =========================================================
     SIDEBAR
========================================================= -->

<div class="sidebar">

    <a
        href="index.php"
        class="sidebar-brand">

        <i class="bi bi-heart-fill me-2"></i>

        Brar Pet Shop

    </a>


    <a
        href="index.php"
        class="sidebar-link">

        <i class="bi bi-speedometer2"></i>

        Dashboard

    </a>


    <a
        href="pets.php"
        class="sidebar-link active">

        <i class="bi bi-heart"></i>

        Pets

    </a>


    <a
        href="products.php"
        class="sidebar-link">

        <i class="bi bi-bag"></i>

        Products

    </a>


    <a
        href="orders.php"
        class="sidebar-link">

        <i class="bi bi-cart-check"></i>

        Orders

    </a>


    <a
        href="users.php"
        class="sidebar-link">

        <i class="bi bi-people"></i>

        Users

    </a>


    <hr class="border-secondary">


    <a
        href="../index.php"
        class="sidebar-link">

        <i class="bi bi-globe"></i>

        View Website

    </a>


    <a
        href="logout.php"
        class="sidebar-link text-danger">

        <i class="bi bi-box-arrow-right"></i>

        Logout

    </a>

</div>


<!-- =========================================================
     MAIN
========================================================= -->

<div class="main-content">


    <!-- HEADER -->

    <div class="top-title">

        <div
            class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <h3 class="fw-bold mb-1">

                    <i class="bi bi-heart-fill me-2"></i>

                    Manage Pets

                </h3>

                <p class="text-muted mb-0">

                    Manage pet information, categories and availability.

                </p>

            </div>


            <a
                href="pet_add.php"
                class="btn btn-primary">

                <i class="bi bi-plus-lg me-1"></i>

                Add New Pet

            </a>

        </div>

    </div>


    <!-- =====================================================
         ALERTS
    ====================================================== -->

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


    <?php if ($error !== ""): ?>

        <div class="alert alert-danger">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         STATISTICS
    ====================================================== -->

    <div class="row g-4 mb-4">


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


        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon text-danger">

                    <i class="bi bi-x-circle-fill"></i>

                </div>

                <div class="stat-number text-danger">

                    <?php echo $soldPets; ?>

                </div>

                <div class="stat-label">

                    Sold

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         FILTERS
    ====================================================== -->

    <div class="table-card mb-4">

        <div class="filter-box">

            <form
                method="GET"
                action="pets.php">

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
                            value="<?php echo htmlspecialchars($search); ?>">

                    </div>


                    <!-- CATEGORY -->

                    <div class="col-lg-3">

                        <label class="form-label small fw-semibold">

                            Category

                        </label>

                        <select
                            name="category"
                            class="form-select">

                            <option value="">
                                All Categories
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


                    <!-- AVAILABILITY -->

                    <div class="col-lg-2">

                        <label class="form-label small fw-semibold">

                            Availability

                        </label>

                        <select
                            name="availability"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option
                                value="Available"
                                <?php echo $availability === "Available" ? "selected" : ""; ?>>

                                Available

                            </option>

                            <option
                                value="Reserved"
                                <?php echo $availability === "Reserved" ? "selected" : ""; ?>>

                                Reserved

                            </option>

                            <option
                                value="Sold"
                                <?php echo $availability === "Sold" ? "selected" : ""; ?>>

                                Sold

                            </option>

                        </select>

                    </div>


                    <!-- BUTTONS -->

                    <div class="col-lg-2 d-flex align-items-end gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary flex-grow-1">

                            <i class="bi bi-search"></i>

                        </button>


                        <a
                            href="pets.php"
                            class="btn btn-outline-secondary">

                            <i class="bi bi-arrow-counterclockwise"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- =====================================================
         PET TABLE
    ====================================================== -->

    <div class="table-card">

        <div
            class="d-flex justify-content-between align-items-center mb-3">

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

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th>
                            Pet
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Age
                        </th>

                        <th>
                            Price
                        </th>

                        <th>
                            Availability
                        </th>

                        <th class="text-end">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php if ($result->num_rows === 0): ?>

                    <tr>

                        <td
                            colspan="6"
                            class="text-center py-5">

                            <i
                                class="bi bi-search fs-1 text-muted">
                            </i>

                            <p class="text-muted mt-2 mb-0">

                                No pets found.

                            </p>

                        </td>

                    </tr>


                <?php else: ?>


                    <?php while ($pet = $result->fetch_assoc()): ?>

                        <tr>


                            <!-- PET -->

                            <td>

                                <div
                                    class="d-flex align-items-center">

                                    <img
                                        src="../<?php echo htmlspecialchars($pet["image"]); ?>"
                                        alt="<?php echo htmlspecialchars($pet["name"]); ?>"
                                        class="pet-image me-3"
                                        onerror="this.onerror=null; this.src='https://via.placeholder.com/65x65?text=Pet';">


                                    <div>

                                        <div class="pet-name">

                                            <?php
                                            echo htmlspecialchars(
                                                $pet["name"]
                                            );
                                            ?>

                                        </div>

                                        <small class="text-muted">

                                            ID #<?php echo (int)$pet["id"]; ?>

                                        </small>

                                    </div>

                                </div>

                            </td>


                            <!-- CATEGORY -->

                            <td>

                                <span class="badge bg-light text-dark border">

                                    <?php
                                    echo htmlspecialchars(
                                        ucfirst($pet["category"])
                                    );
                                    ?>

                                </span>

                            </td>


                            <!-- AGE -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $pet["age"]
                                );
                                ?>

                            </td>


                            <!-- PRICE -->

                            <td>

                                <strong>

                                    ₹<?php echo number_format(
                                        (float)$pet["price"],
                                        2
                                    ); ?>

                                </strong>

                            </td>


                            <!-- AVAILABILITY -->

                            <td>

                                <span
                                    class="badge rounded-pill px-3 py-2 <?php echo availabilityBadge($pet["availability"]); ?>">

                                    <i
                                        class="bi <?php echo availabilityIcon($pet["availability"]); ?> me-1">
                                    </i>

                                    <?php
                                    echo htmlspecialchars(
                                        $pet["availability"]
                                    );
                                    ?>

                                </span>

                            </td>


                            <!-- ACTIONS -->

                            <td class="text-end">

                                <a
                                    href="pet_edit.php?id=<?php echo (int)$pet["id"]; ?>"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Edit Pet">

                                    <i class="bi bi-pencil"></i>

                                </a>


                                <a
                                    href="pet_delete.php?id=<?php echo (int)$pet["id"]; ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Delete Pet"
                                    onclick="return confirm('Are you sure you want to delete this pet?');">

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


</body>
</html>