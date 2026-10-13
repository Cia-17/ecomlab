<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

$id = (int)($_GET['pro_id'] ?? 0);
if ($id <= 0) {
    redirect("../index.php");
}

$controller = new ProductController();
$product = $controller->getProductById($id);
if (!$product) {
    redirect("../index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($product['product_title']) ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1><?= htmlspecialchars($product['product_title']) ?></h1>

    <nav>
        <a href="../index.php">Home</a>
    </nav>

    <div class="single-product">
        <div class="single-product-image">
            <?php if (!empty($product['product_image'])): ?>
                <img src="../images/products/<?= htmlspecialchars($product['product_image']) ?>" alt="">
            <?php else: ?>
                <div class="no-image">No image</div>
            <?php endif; ?>
        </div>

        <div class="single-product-info">
            <h2><?= htmlspecialchars($product['product_title']) ?></h2>
            <p class="product-price">GHS <?= number_format((float)$product['product_price'], 2) ?></p>
            <p><strong>Category:</strong> <?= htmlspecialchars($product['cat_name'] ?? '—') ?></p>
            <p><strong>Brand:</strong> <?= htmlspecialchars($product['brand_name'] ?? '—') ?></p>
            <p><strong>Keywords:</strong> <?= htmlspecialchars($product['product_keywords'] ?? '—') ?></p>

            <h3>Description</h3>
            <p><?= nl2br(htmlspecialchars($product['product_desc'] ?? '')) ?></p>

            <a href="../index.php" class="btn-back">&larr; Back to products</a>
        </div>
    </div>
</body>
</html>