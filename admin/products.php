<?php

require_once "auth.php";
require_once "../config/db.php";

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";

$search = trim($_GET["search"] ?? "");
$stockFilter = trim($_GET["stock"] ?? "");

$sql = "
    SELECT *
    FROM products
    WHERE 1=1
";

$params = [];
$types = "";

if ($search !== "") {
    $sql .= "
        AND (
            name LIKE ?
            OR description LIKE ?
            OR badge LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "sss";
}

if ($stockFilter === "in_stock") {
    $sql .= " AND stock > 0";
}

if ($stockFilter === "out_of_stock") {
    $sql .= " AND stock = 0";
}

if ($stockFilter === "low_stock") {
    $sql .= " AND stock > 0 AND stock <= 5";
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


// =========================
// PRODUCT STOCK STATISTICS
// =========================

$totalProducts = 0;
$totalStock = 0;
$inStockProducts = 0;
$outOfStockProducts = 0;
$lowStockProducts = 0;

$statsResult = $conn->query("
    SELECT
        COUNT(*) AS total_products,
        COALESCE(SUM(stock), 0) AS total_stock,
        SUM(stock > 0) AS in_stock,
        SUM(stock = 0) AS out_of_stock,
        SUM(stock > 0 AND stock <= 5) AS low_stock
    FROM products
");

if ($statsResult) {

    $stats = $statsResult->fetch_assoc();

    $totalProducts = (int)($stats["total_products"] ?? 0);
    $totalStock = (int)($stats["total_stock"] ?? 0);
    $inStockProducts = (int)($stats["in_stock"] ?? 0);
    $outOfStockProducts = (int)($stats["out_of_stock"] ?? 0);
    $lowStockProducts = (int)($stats["low_stock"] ?? 0);
}


// =========================
// HELPER FUNCTIONS
// =========================

function stockBadge($stock)
{
    $stock = (int)$stock;

    if ($stock <= 0) {
        return '<span class="badge bg-danger">
                    <i class="bi bi-x-circle-fill me-1"></i>
                    Out of Stock
                </span>';
    }

    if ($stock <= 5) {
        return '<span class="badge bg-warning text-dark">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Low Stock
                </span>';
    }

    return '<span class="badge bg-success">
                <i class="bi bi-check-circle-fill me-1"></i>
                In Stock
            </span>';
}

function stockNumberClass($stock)
{
    $stock = (int)$stock;

    if ($stock <= 0) {
        return "text-danger";
    }

    if ($stock <= 5) {
        return "text-warning";
    }

    return "text-success";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Manage Products - Admin Panel</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <style>

        body {
            background: #f5f7fb;
        }

        .sidebar {
            min-height: 100vh;
            background: #212529;
        }

        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 12px 18px;
            border-radius: 8px;
            margin: 4px 10px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #343a40;
            color: #fff;
        }

        .brand {
            color: white;
            font-size: 22px;
            font-weight: 700;
            padding: 22px 18px;
        }

        .main-content {
            padding: 30px;
        }

        .stat-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        }

        .product-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        }

        .product-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #eee;
        }

        .table-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .table th {
            white-space: nowrap;
        }

        .description-cell {
            max-width: 260px;
        }

        .description-text {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .stock-number {
            font-size: 20px;
            font-weight: 700;
        }

        @media (max-width: 768px) {

            .sidebar {
                min-height: auto;
            }

            .main-content {
                padding: 18px;
            }

        }

    </style>

    <link rel="stylesheet" href="sidebar.css">

</head>

<body>

<?php require __DIR__ . "/sidebar.php"; ?>

<main id="productAdminContent" class="main-content">

            <!-- Header -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Manage Products
                    </h2>

                    <p class="text-muted mb-0">
                        Manage products, prices, offers and stock.
                    </p>

                </div>

                <a
                    href="product_add.php"
                    class="btn btn-primary">

                    <i class="bi bi-plus-circle me-1"></i>

                    Add Product

                </a>

            </div>

<!-- =========================
     ALERTS
========================== -->

<?php if ($success === "stock_updated"): ?>

    <div class="alert alert-success alert-dismissible fade show">

        <i class="bi bi-check-circle-fill me-2"></i>

        Product stock updated successfully.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php elseif ($error === "stock_invalid"): ?>

    <div class="alert alert-danger alert-dismissible fade show">

        <i class="bi bi-exclamation-triangle-fill me-2"></i>

        Please enter a valid stock quantity.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php elseif ($error === "stock_failed"): ?>

    <div class="alert alert-danger alert-dismissible fade show">

        <i class="bi bi-exclamation-triangle-fill me-2"></i>

        Stock could not be updated.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php elseif ($success === "added"): ?>

    <div class="alert alert-success alert-dismissible fade show">

        <i class="bi bi-check-circle-fill me-2"></i>

        Product added successfully.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php elseif ($success === "updated"): ?>

    <div class="alert alert-success alert-dismissible fade show">

        <i class="bi bi-check-circle-fill me-2"></i>

        Product updated successfully.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php elseif ($success === "deleted"): ?>

    <div class="alert alert-success alert-dismissible fade show">

        <i class="bi bi-check-circle-fill me-2"></i>

        Product deleted successfully.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php elseif ($error === "delete_failed"): ?>

    <div class="alert alert-danger alert-dismissible fade show">

        <i class="bi bi-exclamation-triangle-fill me-2"></i>

        Product could not be deleted.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php elseif ($error === "not_found"): ?>

    <div class="alert alert-danger alert-dismissible fade show">

        Product not found.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php endif; ?>

            <!-- =========================
                 STAT CARDS
            ========================== -->

            <div class="row g-4 mb-4">

                <div class="col-md-6 col-xl-2">

                    <div class="card stat-card h-100">

                        <div class="card-body">

                            <div class="text-muted small">
                                Total Products
                            </div>

                            <h3 class="fw-bold mb-0">
                                <?php echo $totalProducts; ?>
                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-6 col-xl-2">

                    <div class="card stat-card h-100">

                        <div class="card-body">

                            <div class="text-muted small">
                                Total Stock
                            </div>

                            <h3 class="fw-bold text-primary mb-0">
                                <?php echo $totalStock; ?>
                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-6 col-xl-2">

                    <div class="card stat-card h-100">

                        <div class="card-body">

                            <div class="text-muted small">
                                In Stock
                            </div>

                            <h3 class="fw-bold text-success mb-0">
                                <?php echo $inStockProducts; ?>
                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-6 col-xl-2">

                    <div class="card stat-card h-100">

                        <div class="card-body">

                            <div class="text-muted small">
                                Low Stock
                            </div>

                            <h3 class="fw-bold text-warning mb-0">
                                <?php echo $lowStockProducts; ?>
                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-6 col-xl-2">

                    <div class="card stat-card h-100">

                        <div class="card-body">

                            <div class="text-muted small">
                                Out of Stock
                            </div>

                            <h3 class="fw-bold text-danger mb-0">
                                <?php echo $outOfStockProducts; ?>
                            </h3>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =========================
                 SEARCH / FILTER
            ========================== -->

            <div class="card product-card mb-4">

                <div class="card-body">

                    <form
                        method="GET"
                        action="products.php">

                        <div class="row g-3 align-items-end">

                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Search Products
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>

                                    <input
                                        type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Search name, description or badge..."
                                        value="<?php echo htmlspecialchars($search); ?>">

                                </div>

                            </div>


                            <div class="col-md-4">

                                <label class="form-label fw-semibold">
                                    Stock Filter
                                </label>

                                <select
                                    name="stock"
                                    class="form-select">

                                    <option value="">
                                        All Products
                                    </option>

                                    <option
                                        value="in_stock"
                                        <?php echo $stockFilter === "in_stock" ? "selected" : ""; ?>>

                                        In Stock

                                    </option>

                                    <option
                                        value="low_stock"
                                        <?php echo $stockFilter === "low_stock" ? "selected" : ""; ?>>

                                        Low Stock (1-5)

                                    </option>

                                    <option
                                        value="out_of_stock"
                                        <?php echo $stockFilter === "out_of_stock" ? "selected" : ""; ?>>

                                        Out of Stock

                                    </option>

                                </select>

                            </div>


                            <div class="col-md-2 d-flex gap-2">

                                <button
                                    type="submit"
                                    class="btn btn-primary w-100">

                                    <i class="bi bi-funnel me-1"></i>

                                    Filter

                                </button>

                                <a
                                    href="products.php"
                                    class="btn btn-outline-secondary">

                                    <i class="bi bi-arrow-counterclockwise"></i>

                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            <!-- =========================
                 PRODUCT TABLE
            ========================== -->

            <div class="card table-card">

                <div class="card-header bg-white py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <h5 class="mb-0 fw-bold">

                            <i class="bi bi-box-seam me-2"></i>

                            Product List

                        </h5>

                        <span class="text-muted small">

                            <?php echo $result->num_rows; ?> product(s)

                        </span>

                    </div>

                </div>


                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>ID</th>

                                <th>Product</th>

                                <th>Price</th>

                                <th>Stock</th>

                                <th>Badge</th>

                                <th>Description</th>

                                <th class="text-end">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if ($result->num_rows > 0): ?>

                            <?php while ($product = $result->fetch_assoc()): ?>

                                <tr>

                                    <!-- ID -->

                                    <td>

                                        <span class="fw-semibold">
                                            #<?php echo (int)$product["id"]; ?>
                                        </span>

                                    </td>


                                    <!-- PRODUCT -->

                                    <td>

                                        <div class="d-flex align-items-center">

                                            <img
                                                src="../<?php echo htmlspecialchars($product["image"]); ?>"
                                                alt="<?php echo htmlspecialchars($product["name"]); ?>"
                                                class="product-image me-3"
                                                onerror="this.src='../IMAGES/placeholder.jpg';">

                                            <div>

                                                <div class="fw-bold">

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $product["name"]
                                                    );
                                                    ?>

                                                </div>

                                                <small class="text-muted">

                                                    Product #<?php echo (int)$product["id"]; ?>

                                                </small>

                                            </div>

                                        </div>

                                    </td>


                                    <!-- PRICE -->

                                    <td>

                                        <div class="fw-bold">

                                            ₹<?php
                                            echo number_format(
                                                (float)$product["price"],
                                                2
                                            );
                                            ?>

                                        </div>

                                        <?php if (
                                            $product["old_price"] !== null &&
                                            (float)$product["old_price"] > 0
                                        ): ?>

                                            <small class="text-muted text-decoration-line-through">

                                                ₹<?php
                                                echo number_format(
                                                    (float)$product["old_price"],
                                                    2
                                                );
                                                ?>

                                            </small>

                                        <?php endif; ?>

                                    </td>


                                    <!-- STOCK -->

<td>

    <div class="stock-number
        <?php echo stockNumberClass($product["stock"]); ?>">

        <?php echo (int)$product["stock"]; ?>

    </div>

    <?php echo stockBadge($product["stock"]); ?>

    <!-- Stock Controls -->

    <div class="mt-2">

        <div class="d-flex align-items-center gap-1 flex-wrap">

            <!-- Stock Input -->

            <input
                type="number"
                min="0"
                class="form-control form-control-sm"
                style="width: 70px;"
                value="<?php echo (int)$product["stock"]; ?>"
                id="stock-<?php echo (int)$product["id"]; ?>"
                title="Stock quantity">


            <!-- Save -->

            <button
                type="button"
                class="btn btn-success btn-sm"
                onclick="updateStock(<?php echo (int)$product["id"]; ?>)"
                title="Save stock">

                <i class="bi bi-check-lg"></i>

            </button>


            


           


            

        </div>

    </div>

</td>


                                    <!-- BADGE -->

                                    <td>

                                        <?php if (!empty($product["badge"])): ?>

                                            <span class="badge bg-primary">

                                                <?php
                                                echo htmlspecialchars(
                                                    $product["badge"]
                                                );
                                                ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- DESCRIPTION -->

                                    <td class="description-cell">

                                        <div class="description-text text-muted">

                                            <?php
                                            echo htmlspecialchars(
                                                $product["description"] ?? ""
                                            );
                                            ?>

                                        </div>

                                    </td>


                                    <!-- ACTIONS -->

                                    <td class="text-end">

                                        <div class="btn-group">

                                            <a
                                                href="product_edit.php?id=<?php echo (int)$product["id"]; ?>"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit Product">

                                                <i class="bi bi-pencil-square"></i>

                                            </a>

                                            <a
                                                href="product_delete.php?id=<?php echo (int)$product["id"]; ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete Product"
                                                onclick="return confirm('Are you sure you want to delete this product?');">

                                                <i class="bi bi-trash"></i>

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center py-5">

                                    <i
                                        class="bi bi-box-seam display-5 text-muted">
                                    </i>

                                    <h5 class="mt-3">
                                        No products found
                                    </h5>

                                    <p class="text-muted mb-0">
                                        Try changing your search or stock filter.
                                    </p>

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

</main>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>
<script>
function updateStock(productId) {

    const input = document.getElementById("stock-" + productId);

    if (!input) {
        return;
    }

    const stock = parseInt(input.value);

    if (isNaN(stock) || stock < 0) {
        alert("Please enter a valid stock quantity.");
        return;
    }

    const formData = new FormData();

    formData.append("product_id", productId);
    formData.append("stock", stock);

    fetch("product_stock.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {
            location.reload();
        } else {
            alert(data.message || "Stock could not be updated.");
        }

    })
    .catch(error => {

        console.error(error);

        alert("Something went wrong while updating stock.");

    });
}
</script>
</body>

</html>