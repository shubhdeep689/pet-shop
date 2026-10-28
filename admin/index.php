<?php

require_once "auth.php";
require_once "../config/db.php";


/*
|--------------------------------------------------------------------------
| Dashboard Statistics
|--------------------------------------------------------------------------
*/

function getCount($conn, $table)
{
    $result = $conn->query("SELECT COUNT(*) AS total FROM $table");

    if ($result) {
        $row = $result->fetch_assoc();
        return (int)$row["total"];
    }

    return 0;
}

$totalPets = getCount($conn, "pets");
$totalProducts = getCount($conn, "products");
$totalUsers = getCount($conn, "users");
$totalCartItems = getCount($conn, "cart_items");
$totalOrders = getCount($conn, "orders");


/*
|--------------------------------------------------------------------------
| Revenue
|--------------------------------------------------------------------------
*/

$totalRevenue = 0;

$result = $conn->query("
    SELECT COALESCE(SUM(total_amount), 0) AS revenue
    FROM orders
    WHERE status != 'Cancelled'
");

if ($result) {
    $row = $result->fetch_assoc();
    $totalRevenue = (float)$row["revenue"];
}


/*
|--------------------------------------------------------------------------
| Order Status Counts
|--------------------------------------------------------------------------
*/

$statusCounts = [
    "Pending" => 0,
    "Confirmed" => 0,
    "Preparing" => 0,
    "Completed" => 0,
    "Cancelled" => 0
];

$result = $conn->query("
    SELECT status, COUNT(*) AS total
    FROM orders
    GROUP BY status
");

if ($result) {

    while ($row = $result->fetch_assoc()) {

        if (isset($statusCounts[$row["status"]])) {
            $statusCounts[$row["status"]] = (int)$row["total"];
        }

    }
}


/*
|--------------------------------------------------------------------------
| Pets by Category
|--------------------------------------------------------------------------
*/

$petCategories = [];
$petCategoryCounts = [];

$result = $conn->query("
    SELECT category, COUNT(*) AS total
    FROM pets
    GROUP BY category
    ORDER BY total DESC
");

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $petCategories[] = ucfirst($row["category"]);
        $petCategoryCounts[] = (int)$row["total"];

    }
}


/*
|--------------------------------------------------------------------------
| Monthly Orders & Revenue — Last 6 Months
|--------------------------------------------------------------------------
*/

$monthlyLabels = [];
$monthlyOrders = [];
$monthlyRevenue = [];

for ($i = 5; $i >= 0; $i--) {

    $monthStart = date(
        "Y-m-01",
        strtotime("-" . $i . " months")
    );

    $monthEnd = date(
        "Y-m-01",
        strtotime("-" . ($i - 1) . " months")
    );

    $label = date(
        "M Y",
        strtotime($monthStart)
    );

    $monthlyLabels[] = $label;


    /*
    | Orders
    */

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM orders
        WHERE created_at >= ?
          AND created_at < ?
          AND status != 'Cancelled'
    ");

    $monthOrders = 0;

    if ($stmt) {

        $stmt->bind_param(
            "ss",
            $monthStart,
            $monthEnd
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $monthOrders = (int)$row["total"];
        }

        $stmt->close();
    }

    $monthlyOrders[] = $monthOrders;


    /*
    | Revenue
    */

    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(total_amount), 0) AS revenue
        FROM orders
        WHERE created_at >= ?
          AND created_at < ?
          AND status != 'Cancelled'
    ");

    $monthRevenue = 0;

    if ($stmt) {

        $stmt->bind_param(
            "ss",
            $monthStart,
            $monthEnd
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $monthRevenue = (float)$row["revenue"];
        }

        $stmt->close();
    }

    $monthlyRevenue[] = $monthRevenue;
}


/*
|--------------------------------------------------------------------------
| Recent Orders
|--------------------------------------------------------------------------
*/

$recentOrders = [];

$result = $conn->query("
    SELECT
        id,
        customer_name,
        total_amount,
        status,
        created_at
    FROM orders
    ORDER BY id DESC
    LIMIT 5
");

if ($result) {

    while ($row = $result->fetch_assoc()) {
        $recentOrders[] = $row;
    }

}


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function statusBadge($status)
{
    switch ($status) {

        case "Confirmed":
            return "bg-primary";

        case "Preparing":
            return "bg-warning text-dark";

        case "Completed":
            return "bg-success";

        case "Cancelled":
            return "bg-danger";

        default:
            return "bg-secondary";
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
        Admin Dashboard | Brar Pet Shop
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <!-- Chart.js -->

    <script
        src="https://cdn.jsdelivr.net/npm/chart.js">
    </script>


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

            padding: 22px;

            box-shadow:
                0 3px 15px rgba(0,0,0,0.05);

            height: 100%;

            transition: 0.2s;

        }


        .stat-card:hover {

            transform: translateY(-3px);

        }


        .stat-icon {

            width: 50px;

            height: 50px;

            border-radius: 12px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 22px;

            background: #f0f2f5;

        }


        .stat-number {

            font-size: 28px;

            font-weight: 700;

            margin-top: 15px;

        }


        .stat-label {

            color: #6c757d;

            font-size: 14px;

        }


        /* ================= CHART CARDS ================= */

        .chart-card {

            background: white;

            border-radius: 15px;

            padding: 22px;

            box-shadow:
                0 3px 15px rgba(0,0,0,0.05);

            height: 100%;

        }


        .chart-container {

            position: relative;

            height: 300px;

        }


        /* ================= TABLE ================= */

        .table-card {

            background: white;

            border-radius: 15px;

            padding: 22px;

            box-shadow:
                0 3px 15px rgba(0,0,0,0.05);

        }


        .customer-avatar {

            width: 38px;

            height: 38px;

            border-radius: 50%;

            background: #e9ecef;

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: 700;

        }


        /* ================= MOBILE ================= */

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


<!-- =========================================================
     MAIN CONTENT
========================================================= -->

<div class="main-content">


    <!-- HEADER -->

    <div class="top-title">

        <div
            class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <h3 class="fw-bold mb-1">

                    <i class="bi bi-speedometer2 me-2"></i>

                    Admin Dashboard

                </h3>

                <p class="text-muted mb-0">

                    Welcome back,
                    <?php
                    echo htmlspecialchars(
                        $_SESSION["admin_username"] ?? "Admin"
                    );
                    ?>.

                    Here's what's happening at your pet shop.

                </p>

            </div>


            <a
                href="../index.php"
                class="btn btn-outline-primary">

                <i class="bi bi-globe me-1"></i>

                View Website

            </a>

        </div>

    </div>


    <!-- =====================================================
         STAT CARDS
    ====================================================== -->

    <div class="row g-4 mb-4">


        <!-- PETS -->

        <div class="col-xl-2 col-md-4 col-sm-6">

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


        <!-- PRODUCTS -->

        <div class="col-xl-2 col-md-4 col-sm-6">

            <div class="stat-card">

                <div class="stat-icon">

                    <i class="bi bi-bag"></i>

                </div>

                <div class="stat-number">

                    <?php echo $totalProducts; ?>

                </div>

                <div class="stat-label">

                    Products

                </div>

            </div>

        </div>


        <!-- USERS -->

        <div class="col-xl-2 col-md-4 col-sm-6">

            <div class="stat-card">

                <div class="stat-icon">

                    <i class="bi bi-people"></i>

                </div>

                <div class="stat-number">

                    <?php echo $totalUsers; ?>

                </div>

                <div class="stat-label">

                    Users

                </div>

            </div>

        </div>


        <!-- ORDERS -->

        <div class="col-xl-2 col-md-4 col-sm-6">

            <div class="stat-card">

                <div class="stat-icon">

                    <i class="bi bi-cart-check"></i>

                </div>

                <div class="stat-number">

                    <?php echo $totalOrders; ?>

                </div>

                <div class="stat-label">

                    Orders

                </div>

            </div>

        </div>


        <!-- CART -->

        <div class="col-xl-2 col-md-4 col-sm-6">

            <div class="stat-card">

                <div class="stat-icon">

                    <i class="bi bi-cart"></i>

                </div>

                <div class="stat-number">

                    <?php echo $totalCartItems; ?>

                </div>

                <div class="stat-label">

                    Cart Items

                </div>

            </div>

        </div>


        <!-- REVENUE -->

        <div class="col-xl-2 col-md-4 col-sm-6">

            <div class="stat-card">

                <div class="stat-icon">

                    <i class="bi bi-currency-rupee"></i>

                </div>

                <div class="stat-number"
                     style="font-size:22px;">

                    ₹<?php echo number_format($totalRevenue, 0); ?>

                </div>

                <div class="stat-label">

                    Revenue

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         CHART ROW 1
    ====================================================== -->

    <div class="row g-4 mb-4">


        <!-- ORDERS + REVENUE -->

        <div class="col-lg-8">

            <div class="chart-card">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>

                        <h5 class="fw-bold mb-1">

                            Orders & Revenue

                        </h5>

                        <small class="text-muted">

                            Last 6 months

                        </small>

                    </div>

                </div>


                <div class="chart-container">

                    <canvas id="ordersRevenueChart"></canvas>

                </div>

            </div>

        </div>


        <!-- ORDER STATUS -->

        <div class="col-lg-4">

            <div class="chart-card">

                <h5 class="fw-bold mb-1">

                    Order Status

                </h5>

                <small class="text-muted">

                    Current order distribution

                </small>


                <div class="chart-container">

                    <canvas id="statusChart"></canvas>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         CHART ROW 2
    ====================================================== -->

    <div class="row g-4 mb-4">


        <!-- PET CATEGORIES -->

        <div class="col-lg-6">

            <div class="chart-card">

                <h5 class="fw-bold mb-1">

                    Pets by Category

                </h5>

                <small class="text-muted">

                    Number of pets in each category

                </small>


                <div class="chart-container">

                    <canvas id="petCategoryChart"></canvas>

                </div>

            </div>

        </div>


        <!-- PRODUCTS / PETS -->

        <div class="col-lg-6">

            <div class="chart-card">

                <h5 class="fw-bold mb-1">

                    Shop Overview

                </h5>

                <small class="text-muted">

                    Main shop data

                </small>


                <div class="chart-container">

                    <canvas id="shopOverviewChart"></canvas>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         RECENT ORDERS
    ====================================================== -->

    <div class="table-card mb-4">

        <div
            class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h5 class="fw-bold mb-1">

                    Recent Orders

                </h5>

                <small class="text-muted">

                    Latest customer orders

                </small>

            </div>


            <a
                href="orders.php"
                class="btn btn-outline-primary btn-sm">

                View All

                <i class="bi bi-arrow-right ms-1"></i>

            </a>

        </div>


        <?php if (empty($recentOrders)): ?>

            <div class="text-center py-5">

                <i
                    class="bi bi-cart-x fs-1 text-muted">
                </i>

                <p class="text-muted mt-2 mb-0">

                    No orders yet.

                </p>

            </div>

        <?php else: ?>


            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>
                                Order
                            </th>

                            <th>
                                Customer
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

                    <?php foreach ($recentOrders as $order): ?>

                        <tr>


                            <td>

                                <strong>

                                    #<?php echo (int)$order["id"]; ?>

                                </strong>

                            </td>


                            <td>

                                <div
                                    class="d-flex align-items-center gap-2">

                                    <div class="customer-avatar">

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

                                    <span>

                                        <?php
                                        echo htmlspecialchars(
                                            $order["customer_name"]
                                        );
                                        ?>

                                    </span>

                                </div>

                            </td>


                            <td>

                                <strong>

                                    ₹<?php echo number_format(
                                        (float)$order["total_amount"],
                                        2
                                    ); ?>

                                </strong>

                            </td>


                            <td>

                                <span
                                    class="badge <?php echo statusBadge($order["status"]); ?>">

                                    <?php
                                    echo htmlspecialchars(
                                        $order["status"]
                                    );
                                    ?>

                                </span>

                            </td>


                            <td>

                                <small>

                                    <?php
                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $order["created_at"]
                                        )
                                    );
                                    ?>

                                </small>

                            </td>


                            <td>

                                <a
                                    href="order_view.php?id=<?php echo (int)$order["id"]; ?>"
                                    class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-eye"></i>

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>


</div>


<!-- =========================================================
     CHART.JS
========================================================= -->

<script>

const monthlyLabels =
    <?php echo json_encode($monthlyLabels); ?>;

const monthlyOrders =
    <?php echo json_encode($monthlyOrders); ?>;

const monthlyRevenue =
    <?php echo json_encode($monthlyRevenue); ?>;

const statusLabels =
    <?php echo json_encode(array_keys($statusCounts)); ?>;

const statusValues =
    <?php echo json_encode(array_values($statusCounts)); ?>;

const petCategories =
    <?php echo json_encode($petCategories); ?>;

const petCategoryCounts =
    <?php echo json_encode($petCategoryCounts); ?>;


/*
|--------------------------------------------------------------------------
| Orders & Revenue Chart
|--------------------------------------------------------------------------
*/

new Chart(
    document.getElementById("ordersRevenueChart"),
    {
        type: "line",

        data: {

            labels: monthlyLabels,

            datasets: [

                {
                    label: "Orders",

                    data: monthlyOrders,

                    tension: 0.35,

                    borderWidth: 3,

                    yAxisID: "ordersAxis"
                },

                {
                    label: "Revenue (₹)",

                    data: monthlyRevenue,

                    tension: 0.35,

                    borderWidth: 3,

                    yAxisID: "revenueAxis"
                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            interaction: {
                mode: "index",
                intersect: false
            },

            scales: {

                ordersAxis: {

                    type: "linear",

                    position: "left",

                    beginAtZero: true,

                    ticks: {
                        precision: 0
                    }

                },

                revenueAxis: {

                    type: "linear",

                    position: "right",

                    beginAtZero: true,

                    grid: {
                        drawOnChartArea: false
                    }

                }

            }

        }

    }
);


/*
|--------------------------------------------------------------------------
| Order Status Chart
|--------------------------------------------------------------------------
*/

new Chart(
    document.getElementById("statusChart"),
    {
        type: "doughnut",

        data: {

            labels: statusLabels,

            datasets: [

                {
                    data: statusValues,

                    borderWidth: 2
                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    position: "bottom"
                }

            }

        }

    }
);


/*
|--------------------------------------------------------------------------
| Pet Category Chart
|--------------------------------------------------------------------------
*/

new Chart(
    document.getElementById("petCategoryChart"),
    {
        type: "bar",

        data: {

            labels: petCategories,

            datasets: [

                {
                    label: "Pets",

                    data: petCategoryCounts,

                    borderWidth: 1
                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {
                        precision: 0
                    }

                }

            },

            plugins: {

                legend: {
                    display: false
                }

            }

        }

    }
);


/*
|--------------------------------------------------------------------------
| Shop Overview
|--------------------------------------------------------------------------
*/

new Chart(
    document.getElementById("shopOverviewChart"),
    {
        type: "bar",

        data: {

            labels: [
                "Pets",
                "Products",
                "Users",
                "Orders"
            ],

            datasets: [

                {
                    label: "Total",

                    data: [
                        <?php echo $totalPets; ?>,
                        <?php echo $totalProducts; ?>,
                        <?php echo $totalUsers; ?>,
                        <?php echo $totalOrders; ?>
                    ],

                    borderWidth: 1
                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {
                        precision: 0
                    }

                }

            },

            plugins: {

                legend: {
                    display: false
                }

            }

        }

    }
);

</script>


</body>
</html>