
<?php
require_once "auth.php";
require_once "../config/db.php";

/*
|--------------------------------------------------------------------------
| Security Helper
|--------------------------------------------------------------------------
*/

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

/*
|--------------------------------------------------------------------------
| Search and Pagination
|--------------------------------------------------------------------------
*/

$search = trim($_GET["search"] ?? "");

$page = filter_input(
    INPUT_GET,
    "page",
    FILTER_VALIDATE_INT
);

$page = ($page && $page > 0) ? $page : 1;

$perPage = 10;
$offset = ($page - 1) * $perPage;

$totalMessages = 0;
$messages = [];
$totalPages = 1;
$error = "";

/*
|--------------------------------------------------------------------------
| Fetch Contact Messages
|--------------------------------------------------------------------------
*/

try {

    if ($search !== "") {

        $searchTerm = "%" . $search . "%";

        // Count matching messages.
        $countStmt = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM contact_messages
            WHERE full_name LIKE ?
               OR email LIKE ?
               OR subject LIKE ?
               OR message LIKE ?
        ");

        if (!$countStmt) {
            throw new Exception("Unable to prepare message search.");
        }

        $countStmt->bind_param(
            "ssss",
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm
        );

        $countStmt->execute();

        $countResult = $countStmt->get_result();
        $countRow = $countResult->fetch_assoc();

        $totalMessages = (int)$countRow["total"];

        $countStmt->close();

        // Fetch the current page.
        $stmt = $conn->prepare("
            SELECT
                id,
                full_name,
                email,
                phone,
                pet_type,
                subject,
                message,
                created_at
            FROM contact_messages
            WHERE full_name LIKE ?
               OR email LIKE ?
               OR subject LIKE ?
               OR message LIKE ?
            ORDER BY created_at DESC, id DESC
            LIMIT ? OFFSET ?
        ");

        if (!$stmt) {
            throw new Exception("Unable to prepare message query.");
        }

        $stmt->bind_param(
            "ssssii",
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $perPage,
            $offset
        );

    } else {

        // Count all messages.
        $result = $conn->query("
            SELECT COUNT(*) AS total
            FROM contact_messages
        ");

        if (!$result) {
            throw new Exception("Unable to count contact messages.");
        }

        $countRow = $result->fetch_assoc();
        $totalMessages = (int)$countRow["total"];

        // Fetch messages for the current page.
        $stmt = $conn->prepare("
            SELECT
                id,
                full_name,
                email,
                phone,
                pet_type,
                subject,
                message,
                created_at
            FROM contact_messages
            ORDER BY created_at DESC, id DESC
            LIMIT ? OFFSET ?
        ");

        if (!$stmt) {
            throw new Exception("Unable to prepare message query.");
        }

        $stmt->bind_param("ii", $perPage, $offset);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    $stmt->close();

    $totalPages = max(
        1,
        (int)ceil($totalMessages / $perPage)
    );

    // Prevent requests for pages beyond the last page.
    if ($page > $totalPages) {
        header(
            "Location: messages.php" .
            ($search !== ""
                ? "?search=" . urlencode($search)
                : "")
        );
        exit;
    }

} catch (Throwable $exception) {

    error_log(
        "Contact messages page error: " .
        $exception->getMessage()
    );

    $error = "Unable to load contact messages. Please check your database configuration and table.";
}

/*
|--------------------------------------------------------------------------
| Pagination URL Helper
|--------------------------------------------------------------------------
*/

function pageUrl($pageNumber, $search)
{
    $params = [
        "page" => $pageNumber
    ];

    if ($search !== "") {
        $params["search"] = $search;
    }

    return "messages.php?" . http_build_query($params);
}

$startRecord = $totalMessages > 0
    ? $offset + 1
    : 0;

$endRecord = min(
    $offset + $perPage,
    $totalMessages
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Contact Messages | Brar Pet Shop Admin</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    >

    <style>

        body {
            background: #f5f7fb;
            font-family: Arial, sans-serif;
            color: #212529;
        }

        /* Sidebar */

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

        /* Main Content */

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .page-header,
        .message-card {
            background: white;
            border-radius: 15px;
            padding: 24px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        }

        .page-header {
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 22px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff3cd;
            color: #856404;
            font-size: 23px;
        }

        .message-text {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            line-height: 1.7;
        }

        .customer-avatar {
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e7f1ff;
            color: #0d6efd;
            font-weight: bold;
            font-size: 17px;
        }

        .table th {
            color: #6c757d;
            font-size: 13px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .message-subject {
            font-weight: 600;
            color: #212529;
        }

        .empty-state {
            padding: 65px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 55px;
            color: #adb5bd;
        }

        /* Mobile */

        @media (max-width: 768px) {

            .sidebar {
                width: 100%;
                min-height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                padding: 16px;
            }

            .page-header,
            .message-card {
                padding: 17px;
            }

        }

    </style>

    <link rel="stylesheet" href="sidebar.css">

</head>

<body>

<?php require __DIR__ . "/sidebar.php"; ?>

<!-- Main Content -->

<div class="main-content">

    <!-- Header -->

    <div class="page-header">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <h3 class="fw-bold mb-2">
                    <i class="bi bi-envelope-paper text-primary me-2"></i>
                    Contact Messages
                </h3>

                <p class="text-muted mb-0">
                    View and manage messages submitted by your customers.
                </p>

            </div>

            <a href="index.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i>
                Dashboard
            </a>

        </div>

    </div>

    <!-- Message Statistics -->

    <div class="stat-card">

        <div class="d-flex align-items-center gap-3">

            <div class="stat-icon">
                <i class="bi bi-envelope-open"></i>
            </div>

            <div>
                <div class="text-muted small">
                    Total Contact Messages
                </div>

                <h3 class="fw-bold mb-0">
                    <?php echo number_format($totalMessages); ?>
                </h3>
            </div>

        </div>

    </div>

    <!-- Messages Table -->

    <div class="message-card">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

            <div>
                <h5 class="fw-bold mb-1">Customer Inbox</h5>

                <small class="text-muted">
                    <?php echo $startRecord; ?>–<?php echo $endRecord; ?>
                    of <?php echo $totalMessages; ?> messages
                </small>
            </div>

            <!-- Search -->

            <form
                method="GET"
                action="messages.php"
                class="d-flex gap-2"
                role="search"
            >

                <input
                    type="search"
                    name="search"
                    class="form-control"
                    placeholder="Search messages..."
                    value="<?php echo e($search); ?>"
                    aria-label="Search contact messages"
                >

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i>
                </button>

                <?php if ($search !== ""): ?>

                    <a
                        href="messages.php"
                        class="btn btn-outline-secondary"
                        title="Clear search"
                    >
                        <i class="bi bi-x-lg"></i>
                    </a>

                <?php endif; ?>

            </form>

        </div>

        <?php if ($error !== ""): ?>

            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo e($error); ?>
            </div>

        <?php elseif (empty($messages)): ?>

            <div class="empty-state">

                <i class="bi bi-inbox"></i>

                <h5 class="fw-bold mt-3">
                    <?php echo $search !== ""
                        ? "No matching messages"
                        : "No contact messages yet"; ?>
                </h5>

                <p class="text-muted mb-0">
                    <?php echo $search !== ""
                        ? "Try another search term."
                        : "Customer messages will appear here when they submit the contact form."; ?>
                </p>

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Pet Type</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($messages as $index => $item): ?>

                        <tr>

                            <!-- Customer -->

                            <td>

                                <div class="d-flex align-items-center gap-2">

                                    <div class="customer-avatar">

                                        <?php
                                        $initial = function_exists("mb_substr")
                                            ? mb_substr($item["full_name"], 0, 1, "UTF-8")
                                            : substr($item["full_name"], 0, 1);

                                        echo e(function_exists("mb_strtoupper")
                                            ? mb_strtoupper($initial, "UTF-8")
                                            : strtoupper($initial));
                                        ?>

                                    </div>

                                    <div>

                                        <div class="fw-semibold">
                                            <?php echo e($item["full_name"]); ?>
                                        </div>

                                        <small class="text-muted">
                                            <?php echo e($item["email"]); ?>
                                        </small>

                                    </div>

                                </div>

                            </td>

                            <!-- Subject -->

                            <td>
                                <span class="message-subject">
                                    <?php echo e($item["subject"]); ?>
                                </span>
                            </td>

                            <!-- Pet Type -->

                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?php echo e(ucfirst($item["pet_type"])); ?>
                                </span>
                            </td>

                            <!-- Date -->

                            <td>
                                <small>
                                    <?php
                                    $timestamp = strtotime($item["created_at"]);

                                    echo $timestamp
                                        ? e(date("d M Y, h:i A", $timestamp))
                                        : e($item["created_at"]);
                                    ?>
                                </small>
                            </td>

                            <!-- View Message -->

                            <td>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#messageModal<?php echo (int)$item["id"]; ?>"
                                    title="Read full message"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    View
                                </button>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <!-- Pagination -->

            <?php if ($totalPages > 1): ?>

                <nav aria-label="Contact message pages" class="mt-4">

                    <ul class="pagination justify-content-center flex-wrap">

                        <li class="page-item <?php echo $page <= 1 ? "disabled" : ""; ?>">

                            <a
                                class="page-link"
                                href="<?php echo e(pageUrl(max(1, $page - 1), $search)); ?>"
                            >
                                Previous
                            </a>

                        </li>

                        <?php

                        $firstPage = max(1, $page - 2);
                        $lastPage = min($totalPages, $page + 2);

                        for ($p = $firstPage; $p <= $lastPage; $p++):

                        ?>

                            <li class="page-item <?php echo $p === $page ? "active" : ""; ?>">

                                <a
                                    class="page-link"
                                    href="<?php echo e(pageUrl($p, $search)); ?>"
                                >
                                    <?php echo $p; ?>
                                </a>

                            </li>

                        <?php endfor; ?>

                        <li class="page-item <?php echo $page >= $totalPages ? "disabled" : ""; ?>">

                            <a
                                class="page-link"
                                href="<?php echo e(pageUrl(min($totalPages, $page + 1), $search)); ?>"
                            >
                                Next
                            </a>

                        </li>

                    </ul>

                </nav>

            <?php endif; ?>

            <!-- Message Detail Modals -->

            <?php foreach ($messages as $item): ?>

                <div
                    class="modal fade"
                    id="messageModal<?php echo (int)$item["id"]; ?>"
                    tabindex="-1"
                    aria-hidden="true"
                >

                    <div class="modal-dialog modal-dialog-centered modal-lg">

                        <div class="modal-content">

                            <div class="modal-header">

                                <div>

                                    <h5 class="modal-title fw-bold">
                                        <i class="bi bi-envelope-open text-primary me-2"></i>
                                        <?php echo e($item["subject"]); ?>
                                    </h5>

                                    <small class="text-muted">
                                        Message #<?php echo (int)$item["id"]; ?>
                                    </small>

                                </div>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                ></button>

                            </div>

                            <div class="modal-body">

                                <div class="row g-3 mb-4">

                                    <div class="col-md-6">

                                        <label class="form-label text-muted small">
                                            CUSTOMER NAME
                                        </label>

                                        <div class="fw-semibold">
                                            <?php echo e($item["full_name"]); ?>
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label text-muted small">
                                            EMAIL
                                        </label>

                                        <div class="text-break">
                                            <?php echo e($item["email"]); ?>
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label text-muted small">
                                            PHONE
                                        </label>

                                        <div>
                                            <?php echo e($item["phone"]); ?>
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label text-muted small">
                                            PET TYPE
                                        </label>

                                        <div>
                                            <?php echo e(ucfirst($item["pet_type"])); ?>
                                        </div>

                                    </div>

                                    <div class="col-12">

                                        <label class="form-label text-muted small">
                                            DATE RECEIVED
                                        </label>

                                        <div>
                                            <?php
                                            $timestamp = strtotime($item["created_at"]);

                                            echo $timestamp
                                                ? e(date("d M Y, h:i A", $timestamp))
                                                : e($item["created_at"]);
                                            ?>
                                        </div>

                                    </div>

                                </div>

                                <hr>

                                <label class="form-label text-muted small">
                                    CUSTOMER MESSAGE
                                </label>

                                <div class="message-text bg-light rounded p-3"><?php
                                    echo e($item["message"]);
                                ?></div>

                            </div>

                            <div class="modal-footer">

                                <a
                                    href="mailto:<?php echo e($item["email"]); ?>?subject=<?php echo rawurlencode("Re: " . $item["subject"]); ?>"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-reply me-1"></i>
                                    Reply by Email
                                </a>

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal"
                                >
                                    Close
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <p class="text-muted text-center small mt-4">
        Brar Pet Shop Admin Panel
    </p>

</div>

<!-- Bootstrap JavaScript -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
