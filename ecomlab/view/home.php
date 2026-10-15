<?php
// Home view: featured products, or filtered by ?cat=N / ?brand=N.
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

$controller = new ProductController();

if (isset($_GET['cat'])) {
    $id       = (int)$_GET['cat'];
    $products = $controller->getProductsByCategory($id);
    $cat      = $controller->getCategoryById($id);
    $heading  = $cat ? $cat['cat_name'] : "Category";
} elseif (isset($_GET['brand'])) {
    $id       = (int)$_GET['brand'];
    $products = $controller->getProductsByBrand($id);
    $brand    = $controller->getBrandById($id);
    $heading  = $brand ? $brand['brand_name'] : "Brand";
} else {
    $products = $controller->getFeaturedProducts(6);
    $heading  = "Featured Products";
}

$page_title = $heading;
require __DIR__ . "/layout/header.php";
?>
<div class="container page-layout">
    <?php require __DIR__ . "/layout/sidebar.php"; ?>
    <main class="content">
        <h1 class="page-title"><?= e($heading) ?></h1>
        <?php require __DIR__ . "/layout/flash.php"; ?>
        <?php require __DIR__ . "/layout/product_grid.php"; ?>
    </main>
</div>
<?php require __DIR__ . "/layout/footer.php"; ?>
