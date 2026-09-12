<?php

require_once "auth.php";
require_once "../config/db.php";

/*
|--------------------------------------------------------------------------
| Validate Order ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: orders.php");
    exit();
}

$order_id = (int) $_GET["id"];


/*
|--------------------------------------------------------------------------
| Get Order
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        user_id,
        customer_name,
        customer_email,
        total_amount,
        status,
        whatsapp_sent,
        created_at
    FROM orders
    WHERE id = ?
");

if (!$stmt) {
    die("Database query failed: " . $conn->error);
}

$stmt->bind_param("i", $order_id);
$stmt->execute();

$orderResult = $stmt->get_result();

if ($orderResult->num_rows !== 1) {
    $stmt->close();

    header("Location: orders.php?error=not_found");
    exit();
}

$order = $orderResult->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Get Order Items
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        pet_id,
        product_id,
        item_name,
        item_type,
        quantity,
        price,
        subtotal
    FROM order_items
    WHERE order_id = ?
    ORDER BY id ASC
");

if (!$stmt) {
    die("Database query failed: " . $conn->error);
}

$stmt->bind_param("i", $order_id);
$stmt->execute();

$itemsResult = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| Status Badge
|--------------------------------------------------------------------------
*/

function getStatusBadge($status)
{
    switch ($status) {

        case "Pending":
            return '<span class="badge bg-warning text-dark px-3 py-2">
                        <i class="bi bi-clock me-1"></i>
                        Pending
                    </span>';

        case "Confirmed":
            return '<span class="badge bg-primary px-3 py-2">
                        <i class="bi bi-check-circle me-1"></i>
                        Confirmed
                    </span>';

        case "Preparing":
            return '<span class="badge bg-info text-dark px-3 py-2">
                        <i class="bi bi-box-seam me-1"></i>
                        Preparing
                    </span>';

        case "Completed":
            return '<span class="badge bg-success px-3 py-2">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Completed
                    </span>';

        case "Cancelled":
            return '<span class="badge bg-danger px-3 py-2">
                        <i class="bi bi-x-circle-fill me-1"></i>
                        Cancelled
                    </span>';

        default:
            return '<span class="badge bg-secondary px-3 py-2">'
                    . htmlspecialchars($status) .
                   '</span>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Order #<?php echo $order["id"]; ?> -
        Brar Pet Shop Admin
    </title>


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


        /* =========================
           SIDEBAR
        ========================= */

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


        /* =========================
           MAIN CONTENT
        ========================= */

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


        /* =========================
           CARDS
        ========================= */

        .content-card {
            background: white;
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }


        .content-card .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 20px;
            border-radius: 14px 14px 0 0;
        }


        /* =========================
           CUSTOMER
        ========================= */

        .customer-avatar {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: #e7f1ff;
            color: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            font-weight: 700;
        }


        .customer-info-row {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }


        .customer-info-row:last-child {
            border-bottom: none;
        }


        /* =========================
           ORDER ITEMS
        ========================= */

        .item-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background: #f1f3f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }


        .total-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }


        /* =========================
           STATUS
        ========================= */

        .status-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
        }


        /* =========================
           RESPONSIVE
        ========================= */

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


        <a
            href="../index.php"
            target="_blank">

            <i class="bi bi-globe"></i>

            View Website

        </a>


        <a
            href="logout.php"
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


    <!-- TOP BAR -->

    <div class="topbar">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h4 class="mb-1 fw-bold">

                    <i class="bi bi-receipt me-2"></i>

                    Order #<?php echo $order["id"]; ?>

                </h4>

                <small class="text-muted">

                    Order details and customer information

                </small>

            </div>


            <a
                href="orders.php"
                class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left me-1"></i>

                Back to Orders

            </a>

        </div>

    </div>


    <!-- =====================================================
         ORDER SUMMARY
    ====================================================== -->

    <div class="row g-4">


        <!-- =================================================
             LEFT SIDE
        ================================================== -->

        <div class="col-lg-8">


            <!-- CUSTOMER CARD -->

            <div class="card content-card mb-4">

                <div class="card-header">

                    <h5 class="mb-0 fw-bold">

                        <i class="bi bi-person-circle me-2"></i>

                        Customer Information

                    </h5>

                </div>


                <div class="card-body">

                    <div class="d-flex align-items-center mb-3">

                        <div class="customer-avatar me-3">

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

                            <h5 class="mb-1">

                                <?php

                                echo htmlspecialchars(
                                    $order["customer_name"]
                                );

                                ?>

                            </h5>

                            <small class="text-muted">

                                Customer ID:
                                #<?php echo $order["user_id"]; ?>

                            </small>

                        </div>

                    </div>


                    <div class="customer-info-row">

                        <small class="text-muted">
                            Email
                        </small>

                        <div class="fw-semibold">

                            <i class="bi bi-envelope me-2"></i>

                            <?php

                            echo htmlspecialchars(
                                $order["customer_email"]
                            );

                            ?>

                        </div>

                    </div>


                    <div class="customer-info-row">

                        <small class="text-muted">
                            Order Date
                        </small>

                        <div class="fw-semibold">

                            <i class="bi bi-calendar3 me-2"></i>

                            <?php

                            echo date(
                                "d M Y, h:i A",
                                strtotime(
                                    $order["created_at"]
                                )
                            );

                            ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ORDER ITEMS -->

            <div class="card content-card">

                <div class="card-header">

                    <h5 class="mb-0 fw-bold">

                        <i class="bi bi-bag-check me-2"></i>

                        Order Items

                    </h5>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th class="px-4">
                                        Item
                                    </th>

                                    <th>
                                        Type
                                    </th>

                                    <th>
                                        Price
                                    </th>

                                    <th>
                                        Quantity
                                    </th>

                                    <th>
                                        Subtotal
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                            <?php while ($item = $itemsResult->fetch_assoc()): ?>

                                <tr>

                                    <!-- ITEM -->

                                    <td class="px-4">

                                        <div class="d-flex align-items-center">

                                            <div class="item-icon me-3">

                                                <?php if ($item["item_type"] === "Pet"): ?>

                                                    <i class="bi bi-heart-fill text-danger"></i>

                                                <?php else: ?>

                                                    <i class="bi bi-bag-fill text-primary"></i>

                                                <?php endif; ?>

                                            </div>


                                            <div>

                                                <div class="fw-semibold">

                                                    <?php

                                                    echo htmlspecialchars(
                                                        $item["item_name"]
                                                    );

                                                    ?>

                                                </div>


                                                <?php if (!empty($item["pet_id"])): ?>

                                                    <small class="text-muted">

                                                        Pet ID:
                                                        #<?php echo $item["pet_id"]; ?>

                                                    </small>

                                                <?php elseif (!empty($item["product_id"])): ?>

                                                    <small class="text-muted">

                                                        Product ID:
                                                        #<?php echo $item["product_id"]; ?>

                                                    </small>

                                                <?php endif; ?>

                                            </div>

                                        </div>

                                    </td>


                                    <!-- TYPE -->

                                    <td>

                                        <?php if ($item["item_type"] === "Pet"): ?>

                                            <span class="badge bg-danger-subtle text-danger">

                                                Pet

                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-primary-subtle text-primary">

                                                Product

                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- PRICE -->

                                    <td>

                                        ₹<?php

                                        echo number_format(
                                            (float) $item["price"],
                                            2
                                        );

                                        ?>

                                    </td>


                                    <!-- QUANTITY -->

                                    <td>

                                        <?php
                                        echo (int) $item["quantity"];
                                        ?>

                                    </td>


                                    <!-- SUBTOTAL -->

                                    <td>

                                        <strong>

                                            ₹<?php

                                            echo number_format(
                                                (float) $item["subtotal"],
                                                2
                                            );

                                            ?>

                                        </strong>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>

                </div>


                <!-- TOTAL -->

                <div class="card-footer bg-white border-0 p-4">

                    <div class="d-flex justify-content-between align-items-center total-box">

                        <span class="fw-semibold">
                            Order Total
                        </span>

                        <span class="fs-4 fw-bold text-success">

                            ₹<?php

                            echo number_format(
                                (float) $order["total_amount"],
                                2
                            );

                            ?>

                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- =================================================
             RIGHT SIDE
        ================================================== -->

        <div class="col-lg-4">


            <!-- STATUS CARD -->

            <div class="card content-card mb-4">

                <div class="card-header">

                    <h5 class="mb-0 fw-bold">

                        <i class="bi bi-arrow-repeat me-2"></i>

                        Order Status

                    </h5>

                </div>


                <div class="card-body">

                    <div class="status-box text-center">

                        <small class="text-muted d-block mb-2">

                            Current Status

                        </small>


                        <?php

                        echo getStatusBadge(
                            $order["status"]
                        );

                        ?>


                        <div class="mt-4">

                            <a
                                href="order_status.php?id=<?php echo $order["id"]; ?>"
                                class="btn btn-primary w-100">

                                <i class="bi bi-pencil-square me-2"></i>

                                Update Status

                            </a>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ORDER INFORMATION -->

            <div class="card content-card mb-4">

                <div class="card-header">

                    <h5 class="mb-0 fw-bold">

                        <i class="bi bi-info-circle me-2"></i>

                        Order Information

                    </h5>

                </div>


                <div class="card-body">


                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">
                            Order ID
                        </span>

                        <strong>
                            #<?php echo $order["id"]; ?>
                        </strong>

                    </div>


                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">
                            Customer ID
                        </span>

                        <strong>
                            #<?php echo $order["user_id"]; ?>
                        </strong>

                    </div>


                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">
                            WhatsApp
                        </span>

                        <?php if ($order["whatsapp_sent"]): ?>

                            <span class="badge bg-success">

                                <i class="bi bi-whatsapp me-1"></i>

                                Sent

                            </span>

                        <?php else: ?>

                            <span class="badge bg-secondary">

                                Not Confirmed

                            </span>

                        <?php endif; ?>

                    </div>


                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            Order Date
                        </span>

                        <strong>

                            <?php

                            echo date(
                                "d M Y",
                                strtotime(
                                    $order["created_at"]
                                )
                            );

                            ?>

                        </strong>

                    </div>

                </div>

            </div>


            <!-- ACTIONS -->

            <div class="card content-card">

                <div class="card-header">

                    <h5 class="mb-0 fw-bold">

                        <i class="bi bi-lightning-charge me-2"></i>

                        Quick Actions

                    </h5>

                </div>


                <div class="card-body d-grid gap-2">

                    <a
                        href="order_status.php?id=<?php echo $order["id"]; ?>"
                        class="btn btn-primary">

                        <i class="bi bi-arrow-repeat me-2"></i>

                        Change Order Status

                    </a>


                    <a
                        href="orders.php"
                        class="btn btn-outline-secondary">

                        <i class="bi bi-list-ul me-2"></i>

                        All Orders

                    </a>

                </div>

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