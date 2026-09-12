<?php

session_start();

require_once "config/db.php";


// -----------------------------------------
// Check customer login
// -----------------------------------------

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}


$userId = (int)$_SESSION["user_id"];


// -----------------------------------------
// Get Order ID
// -----------------------------------------

$orderId = (int)($_GET["id"] ?? 0);

if ($orderId <= 0) {
    header("Location: my_orders.php");
    exit();
}


// -----------------------------------------
// Get Order
// -----------------------------------------

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
    AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $orderId,
    $userId
);

$stmt->execute();

$orderResult = $stmt->get_result();


if ($orderResult->num_rows === 0) {
    header("Location: my_orders.php");
    exit();
}


$order = $orderResult->fetch_assoc();


// -----------------------------------------
// Get Order Items
// -----------------------------------------

$stmtItems = $conn->prepare("
    SELECT
        id,
        item_name,
        item_type,
        quantity,
        price,
        subtotal
    FROM order_items
    WHERE order_id = ?
    ORDER BY id ASC
");

$stmtItems->bind_param(
    "i",
    $orderId
);

$stmtItems->execute();

$itemsResult = $stmtItems->get_result();


// -----------------------------------------
// Status Badge
// -----------------------------------------

$status = $order["status"];

$statusClass = "bg-secondary";

switch ($status) {

    case "Pending":
        $statusClass = "bg-warning text-dark";
        break;

    case "Confirmed":
        $statusClass = "bg-primary";
        break;

    case "Preparing":
        $statusClass = "bg-info text-dark";
        break;

    case "Completed":
        $statusClass = "bg-success";
        break;

    case "Cancelled":
        $statusClass = "bg-danger";
        break;

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
    Order Receipt #<?php echo $orderId; ?> - Brar Pet Shop
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
        background: #f3f4f6;
        font-family: Arial, sans-serif;
    }


    .receipt-wrapper {
        max-width: 900px;
        margin: 40px auto;
    }


    .receipt {
        background: white;
        padding: 45px;
        border-radius: 12px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
    }


    .shop-logo {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
    }


    .shop-name {
        font-size: 28px;
        font-weight: 700;
    }


    .invoice-title {
        font-size: 32px;
        font-weight: 700;
    }


    .invoice-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 18px;
    }


    .table th {
        background: #f8f9fa;
    }


    .grand-total {
        font-size: 22px;
        font-weight: 700;
    }


    .thank-you {
        border-top: 1px dashed #ccc;
        margin-top: 30px;
        padding-top: 25px;
    }


    @media print {

        body {
            background: white;
        }


        .receipt-wrapper {
            margin: 0;
            max-width: 100%;
        }


        .receipt {
            box-shadow: none;
            border-radius: 0;
            padding: 20px;
        }


        .no-print {
            display: none !important;
        }


        @page {
            margin: 12mm;
        }

    }

</style>


</head>

<body>

<div class="receipt-wrapper">


<!-- Receipt -->

<div class="receipt">


    <!-- Header -->

    <div
        class="row align-items-center mb-4">


        <div class="col-md-7">


            <div class="d-flex align-items-center">


                <img
                    src="IMAGES/BRAR%20PET%20SHOP%20LOGO.jpeg"
                    alt="Brar Pet Shop"
                    class="shop-logo me-3">


                <div>

                    <div class="shop-name">
                        Brar Pet Shop
                    </div>

                    <div class="text-muted">
                        Pets & Pet Accessories
                    </div>

                </div>

            </div>


        </div>


        <div
            class="col-md-5 text-md-end mt-3 mt-md-0">


            <div class="invoice-title">
                RECEIPT
            </div>


            <div class="text-muted">

                Order #
                <strong>
                    <?php echo $orderId; ?>
                </strong>

            </div>


        </div>


    </div>


    <hr>


    <!-- Customer + Order Information -->

    <div class="row g-4 my-3">


        <!-- Customer -->

        <div class="col-md-6">

            <div class="invoice-info h-100">

                <h6 class="fw-bold mb-3">

                    <i class="bi bi-person-circle me-2"></i>

                    Customer Information

                </h6>


                <div class="mb-2">

                    <small class="text-muted">
                        Name
                    </small>

                    <br>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $order["customer_name"]
                        );
                        ?>
                    </strong>

                </div>


                <div>

                    <small class="text-muted">
                        Email
                    </small>

                    <br>

                    <?php
                    echo htmlspecialchars(
                        $order["customer_email"]
                    );
                    ?>

                </div>

            </div>

        </div>


        <!-- Order -->

        <div class="col-md-6">

            <div class="invoice-info h-100">

                <h6 class="fw-bold mb-3">

                    <i class="bi bi-receipt me-2"></i>

                    Order Information

                </h6>


                <div class="mb-2">

                    <small class="text-muted">
                        Order Date
                    </small>

                    <br>

                    <strong>

                        <?php
                        echo date(
                            "d M Y, h:i A",
                            strtotime(
                                $order["created_at"]
                            )
                        );
                        ?>

                    </strong>

                </div>


                <div>

                    <small class="text-muted">
                        Status
                    </small>

                    <br>

                    <span
                        class="badge <?php echo $statusClass; ?>">

                        <?php
                        echo htmlspecialchars($status);
                        ?>

                    </span>

                </div>

            </div>

        </div>

    </div>


    <!-- Items -->

    <h5 class="fw-bold mt-4 mb-3">

        <i class="bi bi-bag-check me-2"></i>

        Order Items

    </h5>


    <div class="table-responsive">


        <table
            class="table table-bordered align-middle">


            <thead>

                <tr>

                    <th style="width: 45px;">
                        #
                    </th>

                    <th>
                        Item
                    </th>

                    <th>
                        Type
                    </th>

                    <th class="text-center">
                        Qty
                    </th>

                    <th class="text-end">
                        Price
                    </th>

                    <th class="text-end">
                        Subtotal
                    </th>

                </tr>

            </thead>


            <tbody>


                <?php

                $itemNumber = 1;

                while ($item = $itemsResult->fetch_assoc()):

                ?>


                    <tr>


                        <td>
                            <?php
                            echo $itemNumber++;
                            ?>
                        </td>


                        <td>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $item["item_name"]
                                );
                                ?>

                            </strong>

                        </td>


                        <td>

                            <span class="badge bg-light text-dark border">

                                <?php
                                echo htmlspecialchars(
                                    $item["item_type"]
                                );
                                ?>

                            </span>

                        </td>


                        <td class="text-center">

                            <?php
                            echo (int)$item["quantity"];
                            ?>

                        </td>


                        <td class="text-end">

                            ₹<?php
                            echo number_format(
                                (float)$item["price"],
                                2
                            );
                            ?>

                        </td>


                        <td class="text-end fw-semibold">

                            ₹<?php
                            echo number_format(
                                (float)$item["subtotal"],
                                2
                            );
                            ?>

                        </td>


                    </tr>


                <?php endwhile; ?>


            </tbody>


        </table>


    </div>


    <!-- Total -->

    <div class="row justify-content-end mt-4">


        <div class="col-md-5">


            <div class="d-flex justify-content-between mb-2">

                <span>
                    Subtotal
                </span>

                <strong>

                    ₹<?php
                    echo number_format(
                        (float)$order["total_amount"],
                        2
                    );
                    ?>

                </strong>

            </div>


            <hr>


            <div
                class="d-flex justify-content-between grand-total">

                <span>
                    Total
                </span>

                <span>

                    ₹<?php
                    echo number_format(
                        (float)$order["total_amount"],
                        2
                    );
                    ?>

                </span>

            </div>


        </div>

    </div>


    <!-- Thank You -->

    <div class="thank-you text-center">


        <h5 class="fw-bold">

            Thank You for Shopping with Brar Pet Shop! 🐾

        </h5>


        <p class="text-muted mb-0">

            We appreciate your order and look forward
            to serving you again.

        </p>


    </div>


    <!-- Buttons -->

    <div
        class="no-print d-flex justify-content-center gap-2 mt-4 flex-wrap">


        <button
            type="button"
            class="btn btn-primary"
            onclick="window.print()">

            <i class="bi bi-printer me-2"></i>

            Print / Save as PDF

        </button>


        <a
            href="order_details.php?id=<?php echo $orderId; ?>"
            class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-2"></i>

            Back to Order

        </a>


        <a
            href="my_orders.php"
            class="btn btn-outline-success">

            <i class="bi bi-receipt me-2"></i>

            My Orders

        </a>


    </div>


</div>


</div>

</body>

</html>
