
<?php
$currentPage = basename($_SERVER['SCRIPT_NAME']);

$menuItems = [
    [
        'file'  => 'index.php',
        'label' => 'Dashboard',
        'icon'  => 'bi-speedometer2'
    ],
    [
        'file'  => 'pets.php',
        'label' => 'Pets',
        'icon'  => 'bi-heart'
    ],
    [
        'file'  => 'products.php',
        'label' => 'Products',
        'icon'  => 'bi-bag'
    ],
    [
        'file'  => 'orders.php',
        'label' => 'Orders',
        'icon'  => 'bi-cart-check'
    ],
    [
        'file'  => 'users.php',
        'label' => 'Users',
        'icon'  => 'bi-people'
    ],
    [
        'file'  => 'messages.php',
        'label' => 'Contact Messages',
        'icon'  => 'bi-envelope'
    ]
];
?>

<aside class="sidebar">

    <!-- BRAND -->
    <a href="index.php" class="sidebar-brand">
        <span class="brand-icon">
            <i class="bi bi-heart-fill"></i>
        </span>

        <span class="brand-text">
            Brar Pet Shop
            <small>ADMIN PANEL</small>
        </span>
    </a>

    <div class="sidebar-divider"></div>

    <!-- SECTION TITLE -->
    <div class="sidebar-heading">
        MAIN MENU
    </div>

    <!-- NAVIGATION -->
    <nav class="sidebar-nav">

        <?php foreach ($menuItems as $item): ?>

            <?php $isActive = ($currentPage === $item['file']); ?>

            <a
                href="<?php echo htmlspecialchars($item['file'], ENT_QUOTES, 'UTF-8'); ?>"
                class="sidebar-link <?php echo $isActive ? 'active' : ''; ?>"
                <?php echo $isActive ? 'aria-current="page"' : ''; ?>
            >
                <span class="nav-icon">
                    <i class="bi <?php echo htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                </span>

                <span class="nav-label">
                    <?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
                </span>

                <?php if ($isActive): ?>
                    <span class="active-indicator"></span>
                <?php endif; ?>
            </a>

        <?php endforeach; ?>

    </nav>

    <!-- BOTTOM LINKS -->
    <div class="sidebar-bottom">

        <div class="sidebar-divider"></div>

        <a href="../index.php" class="sidebar-link">
            <span class="nav-icon">
                <i class="bi bi-globe"></i>
            </span>

            <span class="nav-label">View Website</span>

            <i class="bi bi-box-arrow-up-right external-icon"></i>
        </a>

        <a href="logout.php" class="sidebar-link logout-link">
            <span class="nav-icon">
                <i class="bi bi-box-arrow-right"></i>
            </span>

            <span class="nav-label">Logout</span>
        </a>

        <!-- ADMIN PROFILE -->
        <div class="admin-profile">

            <div class="admin-avatar">
                <i class="bi bi-person-fill"></i>
            </div>

            <div class="admin-info">
                <strong>
                    <?php
                    echo htmlspecialchars(
                        $_SESSION['admin_username'] ?? 'Admin',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </strong>

                <small>Administrator</small>
            </div>

            <i class="bi bi-shield-check admin-shield"></i>

        </div>

    </div>

</aside>
