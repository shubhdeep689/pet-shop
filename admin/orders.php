<?php

require_once "auth.php";
require_once "../config/db.php";

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

$search = trim($_GET["search"] ?? "");
$status = trim($_GET["status"] ?? "");

/*
|--------------------------------------------------------------------------
| Build Query
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        o.id,
        o.customer_name,
        o.customer_email,
        o.total_amount,
        o.status,
        o.whatsapp_sent,
        o.created_at,
        COUNT(oi.id) AS total_lines
    FROM orders o
    LEFT JOIN order_items oi
        ON o.id = oi.order_id
    WHERE 1=1
";

$params = [];
$types = "";

if ($search !== "") {

    $sql .= "
        AND (
            o.customer_name LIKE ?
            OR o.customer_email LIKE ?
            OR CAST(o.id AS CHAR) LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "sss";
}

if ($status !== "") {

    $sql .= " AND o.status = ?";

    $params[] = $status;
    $types .= "s";
}

$sql .= "
    GROUP BY
        o.id,
        o.customer_name,
        o.customer_email,
        o.total_amount,
        o.status,
        o.whatsapp_sent,
        o.created_at
    ORDER BY o.id DESC
";

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

$totalOrders = 0;
$pendingOrders = 0;
$confirmedOrders = 0;
$completedOrders = 0;
$totalSales = 0;

$statsQuery = "
    SELECT
        COUNT(*) AS total_orders,

        SUM(CASE
            WHEN status = 'Pending'
            THEN 1 ELSE 0
        END) AS pending_orders,

        SUM(CASE
            WHEN status = 'Confirmed'
            THEN 1 ELSE 0
        END) AS confirmed_orders,

        SUM(CASE
            WHEN status = 'Completed'
            THEN 1 ELSE 0
        END) AS completed_orders,

        COALESCE(SUM(total_amount), 0) AS total_sales

    FROM orders
";

$statsResult = $conn->query($statsQuery);

if ($statsResult && $statsRow = $statsResult->fetch_assoc()) {

    $totalOrders = (int) $statsRow["total_orders"];
    $pendingOrders = (int) $statsRow["pending_orders"];
    $confirmedOrders = (int) $statsRow["confirmed_orders"];
    $completedOrders = (int) $statsRow["completed_orders"];
    $totalSales = (float) $statsRow["total_sales"];
}


/*
|--------------------------------------------------------------------------
| Status Badge Helper
|--------------------------------------------------------------------------
*/

function getStatusBadge($status)
{
    switch ($status) {

        case "Pending":
            return '<span class="badge bg-warning text-dark">
                        <i class="bi bi-clock me-1"></i>Pending
                    </span>';

        case "Confirmed":
            return '<span class="badge bg-primary">
                        <i class="bi bi-check-circle me-1"></i>Confirmed
                    </span>';

        case "Preparing":
            return '<span class="badge bg-info text-dark">
                        <i class="bi bi-box-seam me-1"></i>Preparing
                    </span>';

        case "Completed":
            return '<span class="badge bg-success">
                        <i class="bi bi-check-circle-fill me-1"></i>Completed
                    </span>';

        case "Cancelled":
            return '<span class="badge bg-danger">
                        <i class="bi bi-x-circle-fill me-1"></i>Cancelled
                    </span>';

        default:
            return '<span class="badge bg-secondary">'
                    . htmlspecialchars($status)
                    . '</span>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Orders - Brar Pet Shop Admin</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
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
            background: #212529;
            color: white;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }

        .sidebar-brand h4 {
            margin: 0;
            font-weight: 700;
        }

        .sidebar-menu {
            padding: 20px 12px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,.75);
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: .2s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #0d6efd;
            color: white;
        }

        .sidebar-menu a i {
            font-size: 18px;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .topbar {
            background: white;
            border-radius: 12px;
            padding: 18px 22px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }

        .stat-card {
            background: white;
            border: none;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
            height: 100%;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .orders-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
            border: none;
        }

        .orders-card .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 20px;
            border-radius: 14px 14px 0 0;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            font-size: 13px;
            text-transform: uppercase;
            color: #6c757d;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .customer-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e7f1ff;
            color: #0d6efd;
            font-weight: 700;
        }

        .empty-state {
            padding: 70px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 55px;
            color: #adb5bd;
        }

        @media (max-width: 991px) {

            .sidebar {
                width: 220px;
            }

            .main-content {
                margin-left: 220px;
                padding: 20px;
            }

        }

        @media (max-width: 767px) {

            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .table-responsive {
                overflow-x: auto;
            }

        }

    </style>

</head>

<body>


<!-- =========================================================
     SIDEBAR
========================================================= -->

<div class="sidebar">

    <div class="sidebar-brand">

        <h4>
            <i class="bi bi-shield-check me-2"></i>
            Brar Pet Shop
        </h4>

        <small class="text-white-50">
            Admin Panel
        </small>

    </div>


    <div class="sidebar-menu">

        <a href="index.php">

            <i class="bi bi-speedometer2"></i>

            Dashboard

        </a>


        <a href="pets.php">

            <i class="bi bi-heart-fill"></i>

            Pets

        </a>


        <a href="products.php">

            <i class="bi bi-bag-fill"></i>

            Products

        </a>


        <a href="orders.php" class="active">

            <i class="bi bi-cart-check-fill"></i>

            Orders

        </a>


        <a href="users.php">

            <i class="bi bi-people-fill"></i>

            Users

        </a>


        <hr class="border-secondary">


        <a href="../index.php" target="_blank">

            <i class="bi bi-globe"></i>

            View Website

        </a>


        <a href="logout.php"
           class="text-danger">

            <i class="bi bi-box-arrow-right"></i>

            Logout

        </a>

    </div>

</div>


<!-- =========================================================
     MAIN CONTENT
========================================================= -->

<div class="main-content">


    <!-- Top Bar -->

    <div class="topbar d-flex justify-content-between align-items-center">

        <div>

            <h4 class="mb-1 fw-bold">
                Orders
            </h4>

            <small class="text-muted">
                Manage customer orders
            </small>

        </div>


        <div class="text-end">

            <small class="text-muted">
                Admin
            </small>

            <div class="fw-semibold">

                <?php
                echo htmlspecialchars(
                    $_SESSION["admin_username"] ?? "Administrator"
                );
                ?>

            </div>

        </div>

    </div>


    <!-- Alerts -->

    <?php if ($success === "updated"): ?>

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill me-2"></i>

            Order status updated successfully.

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    <?php if ($error !== ""): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            Something went wrong.

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         STATISTICS
    ====================================================== -->

    <div class="row g-4 mb-4">


        <!-- Total Orders -->

        <div class="col-xl-3 col-md-6">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted">
                            Total Orders
                        </small>

                        <h3 class="fw-bold mb-0">
                            <?php echo $totalOrders; ?>
                        </h3>

                    </div>

                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">

                        <i class="bi bi-cart-check"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- Pending -->

        <div class="col-xl-3 col-md-6">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted">
                            Pending
                        </small>

                        <h3 class="fw-bold mb-0">
                            <?php echo $pendingOrders; ?>
                        </h3>

                    </div>

                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">

                        <i class="bi bi-clock-history"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- Confirmed -->

        <div class="col-xl-3 col-md-6">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted">
                            Confirmed
                        </small>

                        <h3 class="fw-bold mb-0">
                            <?php echo $confirmedOrders; ?>
                        </h3>

                    </div>

                    <div class="stat-icon bg-info bg-opacity-10 text-info">

                        <i class="bi bi-check-circle"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- Completed -->

        <div class="col-xl-3 col-md-6">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted">
                            Completed
                        </small>

                        <h3 class="fw-bold mb-0">
                            <?php echo $completedOrders; ?>
                        </h3>

                    </div>

                    <div class="stat-icon bg-success bg-opacity-10 text-success">

                        <i class="bi bi-check-circle-fill"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Total Sales -->

    <div class="row mb-4">

        <div class="col-md-6">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted">
                            Total Order Value
                        </small>

                        <h3 class="fw-bold mb-0">

                            ₹<?php
                            echo number_format(
                                $totalSales,
                                2
                            );
                            ?>

                        </h3>

                    </div>

                    <div class="stat-icon bg-success bg-opacity-10 text-success">

                        <i class="bi bi-currency-rupee"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         ORDERS TABLE
    ====================================================== -->

    <div class="card orders-card">


        <!-- Header -->

        <div class="card-header">

            <div class="row g-3 align-items-center">

                <div class="col-lg-5">

                    <h5 class="mb-0 fw-bold">

                        <i class="bi bi-list-check me-2"></i>

                        All Orders

                    </h5>

                </div>


                <div class="col-lg-7">

                    <form
                        method="GET"
                        action="orders.php">

                        <div class="row g-2">


                            <!-- Search -->

                            <div class="col-md-6">

                                <input
                                    type="search"
                                    name="search"
                                    class="form-control"
                                    placeholder="Search customer or order ID..."
                                    value="<?php echo htmlspecialchars($search); ?>">

                            </div>


                            <!-- Status -->

                            <div class="col-md-4">

                                <select
                                    name="status"
                                    class="form-select">

                                    <option value="">
                                        All Status
                                    </option>

                                    <option value="Pending"
                                        <?php echo $status === "Pending" ? "selected" : ""; ?>>
                                        Pending
                                    </option>

                                    <option value="Confirmed"
                                        <?php echo $status === "Confirmed" ? "selected" : ""; ?>>
                                        Confirmed
                                    </option>

                                    <option value="Preparing"
                                        <?php echo $status === "Preparing" ? "selected" : ""; ?>>
                                        Preparing
                                    </option>

                                    <option value="Completed"
                                        <?php echo $status === "Completed" ? "selected" : ""; ?>>
                                        Completed
                                    </option>

                                    <option value="Cancelled"
                                        <?php echo $status === "Cancelled" ? "selected" : ""; ?>>
                                        Cancelled
                                    </option>

                                </select>

                            </div>


                            <!-- Filter -->

                            <div class="col-md-2">

                                <button
                                    type="submit"
                                    class="btn btn-primary w-100">

                                    <i class="bi bi-search"></i>

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- Table -->

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th class="px-4">
                                Order
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                Items
                            </th>

                            <th>
                                Total
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if ($result->num_rows > 0): ?>

                        <?php while ($order = $result->fetch_assoc()): ?>

                            <tr>


                                <!-- Order ID -->

                                <td class="px-4">

                                    <strong>
                                        #<?php
                                        echo $order["id"];
                                        ?>
                                    </strong>

                                </td>


                                <!-- Customer -->

                                <td>

                                    <div class="d-flex align-items-center">

                                        <div class="customer-avatar me-2">

                                            <?php
                                            echo strtoupper(
                                                substr(
                                                    $order["customer_name"],
                                                    0,
                                                    1
                                                )
                                            );
                                            ?>

                                        </div>

                                        <div>

                                            <div class="fw-semibold">

                                                <?php
                                                echo htmlspecialchars(
                                                    $order["customer_name"]
                                                );
                                                ?>

                                            </div>

                                            <small class="text-muted">

                                                <?php
                                                echo htmlspecialchars(
                                                    $order["customer_email"]
                                                );
                                                ?>

                                            </small>

                                        </div>

                                    </div>

                                </td>


                                <!-- Items -->

                                <td>

                                    <span class="badge bg-light text-dark">

                                        <?php
                                        echo (int) $order["total_lines"];
                                        ?>

                                        item(s)

                                    </span>

                                </td>


                                <!-- Total -->

                                <td>

                                    <strong>

                                        ₹<?php
                                        echo number_format(
                                            (float) $order["total_amount"],
                                            2
                                        );
                                        ?>

                                    </strong>

                                </td>


                                <!-- Status -->

                                <td>

                                    <?php
                                    echo getStatusBadge(
                                        $order["status"]
                                    );
                                    ?>

                                </td>


                                <!-- Date -->

                                <td>

                                    <div>

                                        <?php
                                        echo date(
                                            "d M Y",
                                            strtotime(
                                                $order["created_at"]
                                            )
                                        );
                                        ?>

                                    </div>

                                    <small class="text-muted">

                                        <?php
                                        echo date(
                                            "h:i A",
                                            strtotime(
                                                $order["created_at"]
                                            )
                                        );
                                        ?>

                                    </small>

                                </td>


                                <!-- Action -->

                                <td>

                                    <a
                                        href="order_view.php?id=<?php echo $order["id"]; ?>"
                                        class="btn btn-outline-primary btn-sm">

                                        <i class="bi bi-eye me-1"></i>

                                        View

                                    </a>

                                </td>


                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="7">

                                <div class="empty-state">

                                    <i class="bi bi-cart-x"></i>

                                    <h5 class="mt-3">
                                        No Orders Found
                                    </h5>

                                    <p class="text-muted mb-0">

                                        There are no orders matching your search.

                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


</div>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>