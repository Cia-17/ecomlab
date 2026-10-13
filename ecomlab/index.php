<?php require_once "core/core.php"; ?>
<?php require_once "controllers/ProductController.php"; ?>

<?php
$controller = new ProductController();

if (isset($_GET['cat'])) {
    $products = $controller->getProductsByCategory((int)$_GET['cat']);
    $heading  = "Products in Category";
} elseif (isset($_GET['brand'])) {
    $products = $controller->getProductsByBrand((int)$_GET['brand']);
    $heading  = "Products by Brand";
} else {
    $products = $controller->getFeaturedProducts(6);
    $heading  = "Featured Products";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Ecom Store</title>
</head>
<body>
    <?php require_once "view/layout/header.php"; ?>

    <div class="page-layout">
        <?php require_once "view/layout/sidebar.php"; ?>

        <main class="content">
            <h2><?= htmlspecialchars($heading) ?></h2>

            <?php if (empty($products)): ?>
                <p>No products found.</p>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($products as $p): ?>
                        <div class="product-card">
                            <?php if (!empty($p['product_image'])): ?>
                                <img src="images/products/<?= htmlspecialchars($p['product_image']) ?>" alt="">
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($p['product_title']) ?></h3>
                            <p>GHS <?= number_format((float)$p['product_price'], 2) ?></p>
                            <a href="view/single_product.php?pro_id=<?= (int)$p['product_id'] ?>">Details</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>