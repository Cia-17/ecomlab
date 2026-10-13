<?php
if (!isset($controller)) {
    require_once __DIR__ . "/../../controllers/ProductController.php";
    $controller = new ProductController();
}
$sidebarCategories = $controller->getAllCategories();
$sidebarBrands     = $controller->getAllBrands();
?>
<aside class="sidebar">
    <h3>Categories</h3>
    <ul>
        <?php if (empty($sidebarCategories)): ?>
            <li>None yet</li>
        <?php else: foreach ($sidebarCategories as $c): ?>
            <li><a href="index.php?cat=<?= (int)$c['cat_id'] ?>"><?= htmlspecialchars($c['cat_name']) ?></a></li>
        <?php endforeach; endif; ?>
    </ul>

    <h3>Brands</h3>
    <ul>
        <?php if (empty($sidebarBrands)): ?>
            <li>None yet</li>
        <?php else: foreach ($sidebarBrands as $b): ?>
            <li><a href="index.php?brand=<?= (int)$b['brand_id'] ?>"><?= htmlspecialchars($b['brand_name']) ?></a></li>
        <?php endforeach; endif; ?>
    </ul>
</aside>