<?php
// Shared page header: <head>, top bar, navigation and search box.
// Included at the top of every view. Set $page_title before including it.
// HTML + minimal PHP only (no SQL).
require_once __DIR__ . "/../../core/core.php";
require_once __DIR__ . "/../../controllers/CartController.php";
$base  = base_url();

// Cart summary for the bar under the header (not shown on admin pages)
$showCartBar = ($sidebar_mode ?? '') !== 'admin';
$cartCount = 0;
$cartTotal = 0;
if ($showCartBar) {
    try {
        $headerCart = new CartController();
        $cartCount  = $headerCart->getCartCount(get_ip());
        $cartTotal  = $headerCart->getCartTotal(get_ip());
    } catch (Throwable $e) {
        log_error("Cart summary failed: " . $e->getMessage());
    }
}
$title = isset($page_title) ? $page_title . " | Ecom Store" : "Ecom Store";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="<?= e($base) ?>/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="<?= e($base) ?>/index.php">Shoppn</a>

        <form class="header-search" action="<?= e($base) ?>/view/search_results.php" method="GET" role="search">
            <input type="text" name="user_query" maxlength="100" placeholder="Search products"
                   aria-label="Search products" value="<?= e($_GET['user_query'] ?? '') ?>">
            <button type="submit" class="btn btn-accent">Search</button>
        </form>

        <nav class="main-nav" aria-label="Main">
            <?php if (is_admin()): ?>
                <a href="<?= e($base) ?>/view/admin/brand.php">Brand</a>
                <a href="<?= e($base) ?>/view/admin/category.php">Category</a>
                <a href="<?= e($base) ?>/view/admin/product.php">Product</a>
            <?php endif; ?>

            <?php if (is_logged_in()): ?>
                <span class="welcome">Welcome, <?= e($_SESSION['customer_name'] ?? '') ?></span>
                <a href="<?= e($base) ?>/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?= e($base) ?>/view/register.php">Register</a>
                <a href="<?= e($base) ?>/view/login.php">Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<?php if ($showCartBar): ?>
<div class="cart-bar">
    <div class="container cart-bar-inner">
        <span><strong>Shopping Cart</strong></span>
        <span>Total Items: <strong><?= (int)$cartCount ?></strong></span>
        <span>Total Price: <strong>GHS <?= number_format($cartTotal, 2) ?></strong></span>
        <a href="<?= e($base) ?>/view/cart.php">Go to Cart</a>
    </div>
</div>
<?php endif; ?>
