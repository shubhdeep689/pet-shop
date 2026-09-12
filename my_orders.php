<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION["user_id"];

$orders = [];

$stmt = $conn->prepare("
    SELECT
        o.id,
        o.customer_name,
        o.customer_email,
        o.total_amount,
        o.status,
        o.created_at,
        COUNT(oi.id) AS item_count
    FROM orders o
    LEFT JOIN order_items oi
        ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY
        o.id,
        o.customer_name,
        o.customer_email,
        o.total_amount,
        o.status,
        o.created_at
    ORDER BY o.id DESC
");

if ($stmt) {

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    $stmt->close();
}

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

function statusIcon($status)
{
    switch ($status) {

        case "Confirmed":
            return "bi-check-circle";

        case "Preparing":
            return "bi-box-seam";

        case "Completed":
            return "bi-check-circle-fill";

        case "Cancelled":
            return "bi-x-circle-fill";

        default:
            return "bi-clock";
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

    <title>My Orders | Brar Pet Shop</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>

        body {
            background: #f8f9fa;
        }

        .page-header {
            background: linear-gradient(
                135deg,
                #0d6efd,
                #6610f2
            );
            color: white;
            padding: 70px 20px;
            margin-bottom: 40px;
        }

        .order-card {
            background: white;
            border: none;
            border-radius: 18px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.07);
            transition: 0.25s;
            overflow: hidden;
        }

        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.10);
        }

        .order-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: #eef4ff;
            color: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .order-number {
            font-weight: 700;
            font-size: 18px;
        }

        .order-total {
            font-size: 21px;
            font-weight: 700;
            color: #198754;
        }

        .empty-box {
            background: white;
            border-radius: 20px;
            padding: 70px 25px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.06);
        }

        .empty-icon {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #eef4ff;
            color: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            margin: 0 auto 20px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .stat-number {
            font-size: 25px;
            font-weight: 700;
        }

    </style>

</head>

<body>

<?php include "navbar.php"; ?>


<!-- ================= HEADER ================= -->

<section class="page-header">

    <div class="container text-center">

        <h1 class="fw-bold mb-2">
            <i class="bi bi-receipt me-2"></i>
            My Orders
        </h1>

        <p class="mb-0 opacity-75">
            Track and view all your orders from Brar Pet Shop
        </p>

    </div>

</section>


<div class="container pb-5">


    <!-- ================= STATS ================= -->

    <?php

    $totalOrders = count($orders);

    $completedOrders = 0;
    $pendingOrders = 0;
    $totalSpent = 0;

    foreach ($orders as $order) {

        $totalSpent += (float) $order["total_amount"];

        if ($order["status"] === "Completed") {
            $completedOrders++;
        }

        if (
            $order["status"] === "Pending" ||
            $order["status"] === "Confirmed" ||
            $order["status"] === "Preparing"
        ) {
            $pendingOrders++;
        }
    }

    ?>

    <div class="row g-3 mb-4">

        <div class="col-md-4">

            <div class="stat-card">

                <div class="text-muted small">
                    Total Orders
                </div>

                <div class="stat-number">
                    <?php echo $totalOrders; ?>
                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="stat-card">

                <div class="text-muted small">
                    Active Orders
                </div>

                <div class="stat-number text-primary">
                    <?php echo $pendingOrders; ?>
                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="stat-card">

                <div class="text-muted small">
                    Total Spent
                </div>

                <div class="stat-number text-success">

                    ₹<?php echo number_format($totalSpent, 2); ?>

                </div>

            </div>

        </div>

    </div>


    <!-- ================= ORDERS ================= -->

    <?php if (empty($orders)): ?>

        <div class="empty-box">

            <div class="empty-icon">

                <i class="bi bi-bag-x"></i>

            </div>

            <h3 class="fw-bold">
                No Orders Yet
            </h3>

            <p class="text-muted mb-4">
                You haven't placed any orders yet.
                Start exploring our pets and products.
            </p>

            <a
                href="pets.php"
                class="btn btn-primary">

                <i class="bi bi-heart-fill me-2"></i>
                Browse Pets

            </a>

            <a
                href="product.php"
                class="btn btn-outline-primary ms-2">

                <i class="bi bi-bag me-2"></i>
                Browse Products

            </a>

        </div>

    <?php else: ?>


        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h4 class="fw-bold mb-1">
                    Your Order History
                </h4>

                <p class="text-muted mb-0">
                    <?php echo $totalOrders; ?>
                    order<?php echo $totalOrders !== 1 ? "s" : ""; ?>
                    found
                </p>

            </div>

        </div>


        <div class="row g-4">

            <?php foreach ($orders as $order): ?>

                <div class="col-12">

                    <div class="order-card">

                        <div class="p-4">

                            <div class="row align-items-center g-3">


                                <!-- ORDER ICON -->

                                <div class="col-auto">

                                    <div class="order-icon">

                                        <i class="bi bi-receipt"></i>

                                    </div>

                                </div>


                                <!-- ORDER INFO -->

                                <div class="col">

                                    <div class="order-number">

                                        Order
                                        #<?php echo (int) $order["id"]; ?>

                                    </div>

                                    <div class="text-muted small">

                                        <i class="bi bi-calendar3 me-1"></i>

                                        <?php
                                        echo date(
                                            "d M Y, h:i A",
                                            strtotime($order["created_at"])
                                        );
                                        ?>

                                        <span class="mx-2">•</span>

                                        <i class="bi bi-box me-1"></i>

                                        <?php echo (int) $order["item_count"]; ?>

                                        item<?php echo $order["item_count"] != 1 ? "s" : ""; ?>

                                    </div>

                                </div>


                                <!-- STATUS -->

                                <div class="col-md-auto">

                                    <span class="badge rounded-pill px-3 py-2 <?php echo statusBadge($order["status"]); ?>">

                                        <i class="bi <?php echo statusIcon($order["status"]); ?> me-1"></i>

                                        <?php echo htmlspecialchars($order["status"]); ?>

                                    </span>

                                </div>


                                <!-- TOTAL -->

                                <div class="col-md-auto text-md-end">

                                    <div class="text-muted small">
                                        Order Total
                                    </div>

                                    <div class="order-total">

                                        ₹<?php echo number_format(
                                            (float)$order["total_amount"],
                                            2
                                        ); ?>

                                    </div>

                                </div>


                                <!-- VIEW -->

                                <div class="col-md-auto">

                                  <!-- RECEIPT -->

<div class="col-md-auto">

    <a
        href="order_receipt.php?id=<?php echo (int)$order["id"]; ?>"
        class="btn btn-outline-primary">

        <i class="bi bi-printer me-1"></i>

        View / Print Receipt

    </a>

</div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


</div>


<?php include "footer.php"; ?>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>