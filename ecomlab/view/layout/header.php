<?php
if (!isset($controller)) {
    require_once __DIR__ . "/../../controllers/ProductController.php";
    $controller = new ProductController();
}
?>
<header class="site-header">
    <div class="brand">
        <a href="<?= (strpos($_SERVER['PHP_SELF'], '/view/') !== false) ? '../index.php' : 'index.php' ?>">
            Ecom Store
        </a>
    </div>

    <nav class="top-nav">
        <?php if (isset($_SESSION['customer_id'])): ?>
            <span class="welcome">Welcome <?= htmlspecialchars($_SESSION['customer_name']) ?></span>
            <a href="<?= (strpos($_SERVER['PHP_SELF'], '/view/') !== false) ? '../logout.php' : 'logout.php' ?>">Logout</a>
            <?php if (is_admin()): ?>
                <a href="<?= (strpos($_SERVER['PHP_SELF'], '/view/') !== false) ? 'admin/product.php' : 'view/admin/product.php' ?>">Admin</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?= (strpos($_SERVER['PHP_SELF'], '/view/') !== false) ? 'register.php' : 'view/register.php' ?>">Register</a>
            <a href="<?= (strpos($_SERVER['PHP_SELF'], '/view/') !== false) ? 'login.php' : 'view/login.php' ?>">Login</a>
        <?php endif; ?>
    </nav>

    <form action="<?= (strpos($_SERVER['PHP_SELF'], '/view/') !== false) ? 'search_results.php' : 'view/search_results.php' ?>" method="GET" class="top-search">
        <input type="text" name="user_query" placeholder="Search products...">
        <button type="submit">Search</button>
    </form>
</header>