<?php
// Shared sidebar.
//   Shop pages (default): categories and brands, loaded through ProductController.
//   Admin pages: set $sidebar_mode = 'admin' before including to get the admin menu.
require_once __DIR__ . "/../../core/core.php";
$base = base_url();
$sidebarMode = $sidebar_mode ?? 'shop';

if ($sidebarMode === 'admin'):
    $adminLinks = [
        '/view/admin/brand.php'    => 'Brands',
        '/view/admin/category.php' => 'Categories',
        '/view/admin/product.php'  => 'Products',
        '/view/customers.php'      => 'Customers',
    ];
    $currentScript = $_SERVER['SCRIPT_NAME'] ?? '';
?>
<aside class="sidebar" aria-label="Admin menu">
    <h2>Manage</h2>
    <ul>
        <?php foreach ($adminLinks as $path => $label):
            $active = substr($currentScript, -strlen($path)) === $path; ?>
            <li><a href="<?= e($base . $path) ?>"<?= $active ? ' class="active" aria-current="page"' : '' ?>><?= e($label) ?></a></li>
        <?php endforeach; ?>
    </ul>
    <h2>Store</h2>
    <ul>
        <li><a href="<?= e($base) ?>/index.php">View store</a></li>
    </ul>
</aside>
<?php else:
    if (!isset($controller)) {
        require_once __DIR__ . "/../../controllers/ProductController.php";
        $controller = new ProductController();
    }
    $sidebarCategories = $controller->getAllCategories();
    $sidebarBrands     = $controller->getAllBrands();
    $activeCat   = isset($_GET['cat'])   ? (int)$_GET['cat']   : 0;
    $activeBrand = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
?>
<aside class="sidebar" aria-label="Browse products">
    <h2>Categories</h2>
    <ul>
        <?php if (empty($sidebarCategories)): ?>
            <li class="empty">None yet</li>
        <?php else: foreach ($sidebarCategories as $c): ?>
            <li><a href="<?= e($base) ?>/index.php?cat=<?= (int)$c['cat_id'] ?>"<?= $activeCat === (int)$c['cat_id'] ? ' class="active"' : '' ?>><?= e($c['cat_name']) ?></a></li>
        <?php endforeach; endif; ?>
    </ul>

    <h2>Brands</h2>
    <ul>
        <?php if (empty($sidebarBrands)): ?>
            <li class="empty">None yet</li>
        <?php else: foreach ($sidebarBrands as $b): ?>
            <li><a href="<?= e($base) ?>/index.php?brand=<?= (int)$b['brand_id'] ?>"<?= $activeBrand === (int)$b['brand_id'] ? ' class="active"' : '' ?>><?= e($b['brand_name']) ?></a></li>
        <?php endforeach; endif; ?>
    </ul>
</aside>
<?php endif; ?>
