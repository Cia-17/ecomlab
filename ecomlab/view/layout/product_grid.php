<?php
// Product cards in a grid. Used by home.php and search_results.php so both
// look the same. Expects $products (array of rows). Display only - no SQL.
$base = base_url();
if (empty($products)): ?>
    <p class="empty-state">No products found.</p>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <article class="product-card">
                <a class="product-image" href="<?= e($base) ?>/view/single_product.php?pro_id=<?= (int)$p['product_id'] ?>">
                    <?php if (!empty($p['product_image'])): ?>
                        <img src="<?= e($base) ?>/images/products/<?= e($p['product_image']) ?>" alt="<?= e($p['product_title']) ?>">
                    <?php else: ?>
                        <span class="no-image">No image</span>
                    <?php endif; ?>
                </a>
                <div class="product-body">
                    <h3><?= e($p['product_title']) ?></h3>
                    <?php if (!empty($p['brand_name'])): ?>
                        <p class="product-brand"><?= e($p['brand_name']) ?></p>
                    <?php endif; ?>
                    <p class="product-price">GHS <?= number_format((float)$p['product_price'], 2) ?></p>
                    <a class="btn btn-primary btn-block" href="<?= e($base) ?>/view/single_product.php?pro_id=<?= (int)$p['product_id'] ?>">Details</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
