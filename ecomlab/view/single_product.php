<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

$id = (int)($_GET['pro_id'] ?? 0);
if ($id <= 0) {
    redirect(base_url() . "/index.php");
}

$controller = new ProductController();
$product = $controller->getProductById($id);
if (!$product) {
    redirect(base_url() . "/index.php");
}

$page_title = $product['product_title'];
require __DIR__ . "/layout/header.php";
?>
<div class="container page-layout">
    <?php require __DIR__ . "/layout/sidebar.php"; ?>
    <main class="content">
        <?php require __DIR__ . "/layout/flash.php"; ?>

        <div class="card single-product">
            <div class="single-product-image">
                <?php if (!empty($product['product_image'])): ?>
                    <img src="<?= e($base) ?>/images/products/<?= e($product['product_image']) ?>" alt="<?= e($product['product_title']) ?>">
                <?php else: ?>
                    <span class="no-image">No image</span>
                <?php endif; ?>
            </div>

            <div class="single-product-info">
                <h1><?= e($product['product_title']) ?></h1>
                <p class="single-price">GHS <?= number_format((float)$product['product_price'], 2) ?></p>

                <dl class="details">
                    <dt>Category</dt><dd><?= e($product['cat_name'] ?? '-') ?></dd>
                    <dt>Brand</dt><dd><?= e($product['brand_name'] ?? '-') ?></dd>
                    <dt>Keywords</dt><dd><?= e($product['product_keywords'] ?: '-') ?></dd>
                </dl>

                <h2>Description</h2>
                <p><?= nl2br(e($product['product_desc'] ?? '')) ?></p>

                <div class="form-actions">
                    <a class="btn btn-primary" href="<?= e(base_url()) ?>/actions/add_to_cart_action.php?add_cart=<?= (int)$product['product_id'] ?>">Add to Cart</a>
                    <a class="btn btn-outline" href="<?= e(base_url()) ?>/index.php">Back to products</a>
                </div>
            </div>
        </div>
    </main>
</div>
<?php require __DIR__ . "/layout/footer.php"; ?>
