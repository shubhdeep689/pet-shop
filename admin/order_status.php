<?php

require_once "auth.php";
require_once "../config/db.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: orders.php");
    exit();
}

$order_id = (int) $_GET["id"];

$allowedStatuses = [
    "Pending",
    "Confirmed",
    "Preparing",
    "Completed",
    "Cancelled"
];

$error = "";

/*
|--------------------------------------------------------------------------
| Handle Status Update
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $newStatus = trim($_POST["status"] ?? "");

    if (!in_array($newStatus, $allowedStatuses, true)) {
        $error = "Invalid order status selected.";
    } else {

        $stmt = $conn->prepare("
            UPDATE orders
            SET status = ?
            WHERE id = ?
        ");

        if (!$stmt) {
            $error = "Database error: " . $conn->error;
        } else {

            $stmt->bind_param("si", $newStatus, $order_id);

            if ($stmt->execute()) {
                $stmt->close();

                header(
                    "Location: order_view.php?id=" .
                    $order_id .
                    "&success=updated"
                );
                exit();

            } else {
                $error = "Failed to update order status.";
                $stmt->close();
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Get Order
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        id,
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

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    header("Location: orders.php?error=not_found");
    exit();
}

$order = $result->fetch_assoc();

$stmt->close();

$currentStatus = $order["status"];

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Update Order Status | Brar Pet Shop Admin</title>

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

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .top-title {
            background: white;
            border-radius: 15px;
            padding: 22px 25px;
            margin-bottom: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.05);
        }

        .card-box {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.05);
        }

        .order-summary {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
        }

        .status-option {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: 0.2s;
        }

        .status-option:hover {
            border-color: #adb5bd;
            background: #f8f9fa;
        }

        .status-option.selected {
            border-color: #0d6efd;
            background: #f0f6ff;
        }

        .status-option input {
            transform: scale(1.2);
        }

        .status-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
            font-size: 18px;
        }

        .status-option.selected .status-icon {
            background: #0d6efd;
            color: white;
        }

        .status-flow {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin: 20px 0 30px;
        }

        .flow-step {
            padding: 8px 14px;
            border-radius: 30px;
            background: #e9ecef;
            font-size: 13px;
            font-weight: 600;
        }

        .flow-arrow {
            color: #6c757d;
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

            .status-flow {
                justify-content: flex-start;
            }

        }

    </style>

</head>

<body>

<!-- ================= SIDEBAR ================= -->

<div class="sidebar">

    <a href="index.php" class="sidebar-brand">
        <i class="bi bi-heart-fill me-2"></i>
        Brar Pet Shop
    </a>

    <a href="index.php" class="sidebar-link">
        <i class="bi bi-speedometer2"></i>
        Dashboard
    </a>

    <a href="pets.php" class="sidebar-link">
        <i class="bi bi-heart"></i>
        Pets
    </a>

    <a href="products.php" class="sidebar-link">
        <i class="bi bi-bag"></i>
        Products
    </a>

    <a href="orders.php" class="sidebar-link active">
        <i class="bi bi-cart-check"></i>
        Orders
    </a>

    <a href="users.php" class="sidebar-link">
        <i class="bi bi-people"></i>
        Users
    </a>

    <hr class="border-secondary">

    <a href="../index.php" class="sidebar-link">
        <i class="bi bi-globe"></i>
        View Website
    </a>

    <a href="logout.php" class="sidebar-link text-danger">
        <i class="bi bi-box-arrow-right"></i>
        Logout
    </a>

</div>


<!-- ================= MAIN CONTENT ================= -->

<div class="main-content">

    <!-- PAGE HEADER -->

    <div class="top-title">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <h3 class="fw-bold mb-1">
                    <i class="bi bi-arrow-repeat me-2"></i>
                    Update Order Status
                </h3>

                <p class="text-muted mb-0">
                    Change the current status of Order #<?php echo $order_id; ?>
                </p>

            </div>

            <a
                href="order_view.php?id=<?php echo $order_id; ?>"
                class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Back to Order

            </a>

        </div>

    </div>


    <!-- ERROR -->

    <?php if ($error !== ""): ?>

        <div class="alert alert-danger">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <div class="row g-4">

        <!-- ================= STATUS FORM ================= -->

        <div class="col-lg-7">

            <div class="card-box">

                <h5 class="fw-bold mb-1">
                    Select New Status
                </h5>

                <p class="text-muted mb-4">
                    Choose the current stage of this customer's order.
                </p>


                <form
                    method="POST"
                    action="order_status.php?id=<?php echo $order_id; ?>">

                    <?php foreach ($allowedStatuses as $status): ?>

                        <?php

                        $icon = "bi-clock";

                        if ($status === "Confirmed") {
                            $icon = "bi-check-circle";
                        } elseif ($status === "Preparing") {
                            $icon = "bi-box-seam";
                        } elseif ($status === "Completed") {
                            $icon = "bi-check-circle-fill";
                        } elseif ($status === "Cancelled") {
                            $icon = "bi-x-circle-fill";
                        }

                        $isSelected = ($currentStatus === $status);

                        ?>

                        <label
                            class="status-option d-flex align-items-center gap-3 <?php echo $isSelected ? 'selected' : ''; ?>"
                            onclick="selectStatus(this)">

                            <input
                                type="radio"
                                name="status"
                                value="<?php echo htmlspecialchars($status); ?>"
                                <?php echo $isSelected ? "checked" : ""; ?>
                                onchange="updateSelected(this)">

                            <div class="status-icon">

                                <i class="bi <?php echo $icon; ?>"></i>

                            </div>

                            <div>

                                <div class="fw-bold">

                                    <?php echo htmlspecialchars($status); ?>

                                    <?php if ($isSelected): ?>

                                        <span class="badge bg-primary ms-2">
                                            Current
                                        </span>

                                    <?php endif; ?>

                                </div>

                                <small class="text-muted">

                                    <?php

                                    if ($status === "Pending") {
                                        echo "Order has been received and is waiting for confirmation.";
                                    } elseif ($status === "Confirmed") {
                                        echo "Order has been confirmed by the shop.";
                                    } elseif ($status === "Preparing") {
                                        echo "Order is currently being prepared.";
                                    } elseif ($status === "Completed") {
                                        echo "Order has been successfully completed.";
                                    } elseif ($status === "Cancelled") {
                                        echo "Order has been cancelled.";
                                    }

                                    ?>

                                </small>

                            </div>

                        </label>

                    <?php endforeach; ?>


                    <div class="alert alert-warning mt-4">

                        <i class="bi bi-info-circle-fill me-2"></i>

                        <strong>Note:</strong>
                        Changing the status does not send WhatsApp messages
                        automatically.

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary w-100 btn-lg">

                        <i class="bi bi-check-lg me-2"></i>
                        Update Order Status

                    </button>

                </form>

            </div>

        </div>


        <!-- ================= ORDER SUMMARY ================= -->

        <div class="col-lg-5">

            <div class="card-box">

                <h5 class="fw-bold mb-4">
                    <i class="bi bi-receipt me-2"></i>
                    Order Summary
                </h5>


                <div class="order-summary">

                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">
                            Order ID
                        </span>

                        <strong>
                            #<?php echo $order_id; ?>
                        </strong>

                    </div>


                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">
                            Customer
                        </span>

                        <strong>
                            <?php echo htmlspecialchars($order["customer_name"]); ?>
                        </strong>

                    </div>


                    <div class="mb-3">

                        <div class="text-muted small mb-1">
                            Email
                        </div>

                        <div class="fw-semibold text-break">
                            <?php echo htmlspecialchars($order["customer_email"]); ?>
                        </div>

                    </div>


                    <hr>


                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">
                            Order Total
                        </span>

                        <strong class="text-success fs-5">

                            ₹<?php echo number_format(
                                (float)$order["total_amount"],
                                2
                            ); ?>

                        </strong>

                    </div>


                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">
                            Current Status
                        </span>

                        <span class="badge bg-secondary">

                            <?php echo htmlspecialchars($currentStatus); ?>

                        </span>

                    </div>


                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            Order Date
                        </span>

                        <span>
                            <?php
                            echo date(
                                "d M Y, h:i A",
                                strtotime($order["created_at"])
                            );
                            ?>
                        </span>

                    </div>

                </div>


                <!-- STATUS FLOW -->

                <h6 class="fw-bold mt-4 mb-2">
                    Order Flow
                </h6>

                <div class="status-flow">

                    <span class="flow-step">
                        Pending
                    </span>

                    <span class="flow-arrow">
                        →
                    </span>

                    <span class="flow-step">
                        Confirmed
                    </span>

                    <span class="flow-arrow">
                        →
                    </span>

                    <span class="flow-step">
                        Preparing
                    </span>

                    <span class="flow-arrow">
                        →
                    </span>

                    <span class="flow-step">
                        Completed
                    </span>

                </div>


                <?php if ($currentStatus === "Cancelled"): ?>

                    <div class="alert alert-danger mb-0">

                        <i class="bi bi-x-circle-fill me-2"></i>

                        This order is currently cancelled.

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>


<script>

function updateSelected(radio) {

    document.querySelectorAll(".status-option").forEach(function(option) {
        option.classList.remove("selected");

        const currentRadio = option.querySelector('input[type="radio"]');

        if (currentRadio && currentRadio.checked) {
            option.classList.add("selected");
        }
    });

}

function selectStatus(option) {

    const radio = option.querySelector('input[type="radio"]');

    if (radio) {
        radio.checked = true;
        updateSelected(radio);
    }

}

</script>


</body>
</html>