
<?php

require_once "auth.php";
require_once "../config/db.php";

// ==========================================
// SEARCH REGISTERED USERS
// ==========================================

$search = trim($_GET["search"] ?? "");

$sql = "SELECT id, name, email, created_at
        FROM users
        WHERE 1=1";

$params = [];
$types = "";

if ($search !== "") {

    $sql .= " AND (
        name LIKE ?
        OR email LIKE ?
    )";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;

    $types = "ss";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database query failed.");
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();


// ==========================================
// TOTAL REGISTERED USERS
// ==========================================

$totalUsers = 0;

$countResult = $conn->query(
    "SELECT COUNT(*) AS total FROM users"
);

if ($countResult) {
    $totalUsers = (int) $countResult->fetch_assoc()["total"];
}


// ==========================================
// NEW USERS IN LAST 30 DAYS
// ==========================================

$newUsers = 0;

$newUsersResult = $conn->query(
    "SELECT COUNT(*) AS total
     FROM users
     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
);

if ($newUsersResult) {
    $newUsers = (int) $newUsersResult->fetch_assoc()["total"];
}


// ==========================================
// USERS WITH CART ITEMS
// ==========================================

$cartUsers = 0;

$cartUsersResult = $conn->query(
    "SELECT COUNT(DISTINCT user_id) AS total
     FROM cart_items"
);

if ($cartUsersResult) {
    $cartUsers = (int) $cartUsersResult->fetch_assoc()["total"];
}


// ==========================================
// NEWSLETTER SUBSCRIBERS
// ==========================================

$newsletterCount = 0;
$newsletterResult = false;
$newsletterError = false;

$newsletterCountResult = $conn->query(
    "SELECT COUNT(*) AS total FROM newsletter"
);

if ($newsletterCountResult) {

    $newsletterCount = (int) $newsletterCountResult
        ->fetch_assoc()["total"];

    $newsletterStmt = $conn->prepare(
        "SELECT id, email, subscribed_at
         FROM newsletter
         ORDER BY id DESC"
    );

    if ($newsletterStmt) {

        $newsletterStmt->execute();

        $newsletterResult = $newsletterStmt->get_result();

    } else {

        $newsletterError = true;

    }

} else {

    // The newsletter table may not exist yet.
    $newsletterError = true;

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

    <title>Users | Brar Pet Shop Admin</title>

    <!-- BOOTSTRAP -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- BOOTSTRAP ICONS -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

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

        /* SIDEBAR */

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

        /* MAIN CONTENT */

        .main {
            margin-left: 250px;
            padding: 28px;
        }

        /* TOPBAR */

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
            flex-shrink: 0;
        }

        /* STATISTICS */

        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 20px;
            height: 100%;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.03);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 15px;
        }

        .icon-blue {
            background: #dbeafe;
            color: #2563eb;
        }

        .icon-green {
            background: #dcfce7;
            color: #16a34a;
        }

        .icon-orange {
            background: #ffedd5;
            color: #ea580c;
        }

        .icon-purple {
            background: #ede9fe;
            color: #7c3aed;
        }

        .stat-number {
            font-size: 27px;
            font-weight: 700;
            margin: 0;
        }

        .stat-label {
            color: #6b7280;
            font-size: 14px;
            margin-top: 3px;
        }

        /* CONTENT CARDS */

        .content-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .content-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .content-header h5 {
            margin: 0;
            font-weight: 700;
        }

        .search-box {
            padding: 18px 20px;
            border-bottom: 1px solid #e5e7eb;
            background: #fafafa;
        }

        /* USER AVATAR */

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 17px;
            flex-shrink: 0;
        }

        .user-name {
            font-weight: 600;
            color: #111827;
        }

        .user-email {
            color: #6b7280;
            font-size: 13px;
        }

        .user-id {
            color: #64748b;
            font-size: 13px;
        }

        .date-text {
            color: #475569;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 18px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .table td {
            padding: 15px 18px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table tbody tr:hover {
            background: #fafafa;
        }

        .subscriber-email {
            overflow-wrap: anywhere;
        }

        /* EMPTY STATE */

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 55px;
            color: #cbd5e1;
            display: block;
            margin-bottom: 15px;
        }

        /* MOBILE */

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

            .content-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .table-responsive {
                overflow-x: auto;
            }

        }

    </style>

</head>

<body>

<!-- ==========================================
     SIDEBAR
========================================== -->

<div class="sidebar">

    <div class="brand">
        <i class="bi bi-heart-pulse-fill"></i>
        <span>Brar Pet Shop</span>
    </div>

    <div class="menu-title">Main Menu</div>

    <a href="index.php" class="sidebar-link">
        <i class="bi bi-speedometer2"></i>
        Dashboard
    </a>

    <a href="pets.php" class="sidebar-link">
        <i class="bi bi-heart-fill"></i>
        Pets
    </a>

    <a href="products.php" class="sidebar-link">
        <i class="bi bi-box-seam-fill"></i>
        Products
    </a>

    <a href="users.php" class="sidebar-link active">
        <i class="bi bi-people-fill"></i>
        Users
    </a>

    <div class="menu-title mt-4">Website</div>

    <a href="../index.php" class="sidebar-link">
        <i class="bi bi-globe2"></i>
        View Website
    </a>

    <a href="logout.php" class="sidebar-link">
        <i class="bi bi-box-arrow-right"></i>
        Logout
    </a>

</div>


<!-- ==========================================
     MAIN CONTENT
========================================== -->

<div class="main">

    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <h1 class="page-title">Users</h1>

            <p class="page-subtitle">
                View registered customers and newsletter subscribers.
            </p>

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
                        $_SESSION["admin_username"] ?? "Admin",
                        ENT_QUOTES,
                        "UTF-8"
                    );
                    ?>
                </strong>

            </div>

        </div>

    </div>


    <!-- ==========================================
         STATISTICS CARDS
    ========================================== -->

    <div class="row g-4 mb-4">

        <!-- TOTAL USERS -->

        <div class="col-md-6 col-xl-3">

            <div class="stat-card">

                <div class="stat-icon icon-blue">
                    <i class="bi bi-people-fill"></i>
                </div>

                <p class="stat-number">
                    <?php echo $totalUsers; ?>
                </p>

                <div class="stat-label">
                    Total Registered Users
                </div>

            </div>

        </div>


        <!-- NEW USERS -->

        <div class="col-md-6 col-xl-3">

            <div class="stat-card">

                <div class="stat-icon icon-green">
                    <i class="bi bi-person-plus-fill"></i>
                </div>

                <p class="stat-number">
                    <?php echo $newUsers; ?>
                </p>

                <div class="stat-label">
                    New Users in Last 30 Days
                </div>

            </div>

        </div>


        <!-- CART USERS -->

        <div class="col-md-6 col-xl-3">

            <div class="stat-card">

                <div class="stat-icon icon-orange">
                    <i class="bi bi-cart-fill"></i>
                </div>

                <p class="stat-number">
                    <?php echo $cartUsers; ?>
                </p>

                <div class="stat-label">
                    Users With Cart Items
                </div>

            </div>

        </div>


        <!-- NEWSLETTER SUBSCRIBERS -->

        <div class="col-md-6 col-xl-3">

            <div class="stat-card">

                <div class="stat-icon icon-purple">
                    <i class="bi bi-envelope-fill"></i>
                </div>

                <p class="stat-number">
                    <?php echo $newsletterCount; ?>
                </p>

                <div class="stat-label">
                    Newsletter Subscribers
                </div>

            </div>

        </div>

    </div>


    <!-- ==========================================
         REGISTERED USERS TABLE
    ========================================== -->

    <div class="content-card">

        <div class="content-header">

            <div>

                <h5>Registered Users</h5>

                <small class="text-muted">
                    Customer accounts registered on Brar Pet Shop.
                </small>

            </div>

        </div>


        <!-- SEARCH USERS -->

        <div class="search-box">

            <form
                action="users.php"
                method="GET"
                class="row g-2">

                <div class="col-md-10">

                    <input
                        type="search"
                        name="search"
                        class="form-control"
                        placeholder="Search by name or email..."
                        value="<?php
                            echo htmlspecialchars(
                                $search,
                                ENT_QUOTES,
                                "UTF-8"
                            );
                        ?>"
                    >

                </div>

                <div class="col-md-2 d-grid">

                    <button
                        type="submit"
                        class="btn btn-dark">

                        <i class="bi bi-search me-1"></i>
                        Search

                    </button>

                </div>

            </form>

        </div>


        <!-- USERS RESULTS -->

        <?php if ($result->num_rows > 0): ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Registered</th>
                            <th>Status</th>
                            <th>User ID</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($user = $result->fetch_assoc()): ?>

                            <?php
                            $initial = strtoupper(
                                substr(
                                    trim((string) $user["name"]),
                                    0,
                                    1
                                )
                            );

                            if ($initial === "") {
                                $initial = "?";
                            }
                            ?>

                            <tr>

                                <!-- USER -->

                                <td>

                                    <div class="d-flex align-items-center gap-3">

                                        <div class="user-avatar">

                                            <?php
                                            echo htmlspecialchars(
                                                $initial,
                                                ENT_QUOTES,
                                                "UTF-8"
                                            );
                                            ?>

                                        </div>

                                        <div>

                                            <div class="user-name">

                                                <?php
                                                echo htmlspecialchars(
                                                    $user["name"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                );
                                                ?>

                                            </div>

                                            <div class="user-email">
                                                Customer Account
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <!-- EMAIL -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $user["email"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );
                                    ?>

                                </td>


                                <!-- REGISTRATION DATE -->

                                <td>

                                    <span class="date-text">

                                        <i class="bi bi-calendar3 me-1"></i>

                                        <?php

                                        if (!empty($user["created_at"])) {

                                            echo htmlspecialchars(
                                                date(
                                                    "d M Y",
                                                    strtotime($user["created_at"])
                                                ),
                                                ENT_QUOTES,
                                                "UTF-8"
                                            );

                                        } else {

                                            echo "Date unavailable";

                                        }

                                        ?>

                                    </span>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <span class="status-badge">

                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Active

                                    </span>

                                </td>


                                <!-- USER ID -->

                                <td>

                                    <span class="user-id">

                                        #<?php echo (int) $user["id"]; ?>

                                    </span>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="empty-state">

                <i class="bi bi-people"></i>

                <h5>No Users Found</h5>

                <p>

                    <?php if ($search !== ""): ?>

                        No users match your search.

                    <?php else: ?>

                        No registered users yet.

                    <?php endif; ?>

                </p>

            </div>

        <?php endif; ?>

    </div>


    <!-- ==========================================
         NEWSLETTER SUBSCRIBERS TABLE
    ========================================== -->

    <div class="content-card mt-4">

        <div class="content-header">

            <div>

                <h5>
                    <i class="bi bi-envelope-paper me-2 text-primary"></i>
                    Newsletter Subscribers
                </h5>

                <small class="text-muted">
                    Email addresses subscribed to Brar Pet Shop updates.
                </small>

            </div>

            <span class="badge bg-primary">
                <?php echo $newsletterCount; ?> Subscribers
            </span>

        </div>


        <?php if ($newsletterError): ?>

            <div class="alert alert-warning m-3">

                <i class="bi bi-exclamation-triangle me-2"></i>

                The newsletter table could not be loaded.
                Please check that your database contains a
                <code>newsletter</code> table with
                <code>id</code>, <code>email</code>, and
                <code>subscribed_at</code> columns.

            </div>

        <?php elseif ($newsletterResult && $newsletterResult->num_rows > 0): ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>
                            <th>Subscriber ID</th>
                            <th>Email Address</th>
                            <th>Subscription Date</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($subscriber = $newsletterResult->fetch_assoc()): ?>

                            <tr>

                                <!-- SUBSCRIBER ID -->

                                <td>
                                    #<?php echo (int) $subscriber["id"]; ?>
                                </td>


                                <!-- EMAIL ADDRESS -->

                                <td>

                                    <div class="d-flex align-items-center gap-2">

                                        <div class="admin-icon">
                                            <i class="bi bi-envelope"></i>
                                        </div>

                                        <span class="subscriber-email">

                                            <?php
                                            echo htmlspecialchars(
                                                $subscriber["email"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            );
                                            ?>

                                        </span>

                                    </div>

                                </td>


                                <!-- SUBSCRIPTION DATE -->

                                <td>

                                    <span class="date-text">

                                        <i class="bi bi-calendar3 me-1"></i>

                                        <?php

                                        if (!empty($subscriber["subscribed_at"])) {

                                            $timestamp = strtotime(
                                                $subscriber["subscribed_at"]
                                            );

                                            if ($timestamp !== false) {

                                                echo htmlspecialchars(
                                                    date(
                                                        "d M Y, h:i A",
                                                        $timestamp
                                                    ),
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                );

                                            } else {

                                                echo "Date unavailable";

                                            }

                                        } else {

                                            echo "Date unavailable";

                                        }

                                        ?>

                                    </span>

                                </td>


                                <!-- SUBSCRIPTION STATUS -->

                                <td>

                                    <span class="status-badge">

                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Subscribed

                                    </span>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="empty-state">

                <i class="bi bi-envelope"></i>

                <h5>No Newsletter Subscribers Yet</h5>

                <p>
                    Subscriber email addresses will appear here
                    when visitors subscribe through your website.
                </p>

            </div>

        <?php endif; ?>

    </div>


</div>


<!-- BOOTSTRAP JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>

<?php

// ==========================================
// CLOSE DATABASE STATEMENTS
// ==========================================

$stmt->close();

if (isset($newsletterStmt) && $newsletterStmt instanceof mysqli_stmt) {
    $newsletterStmt->close();
}

?>

