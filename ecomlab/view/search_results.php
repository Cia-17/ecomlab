<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

$query = trim($_GET['user_query'] ?? '');
$controller = new ProductController();
$products = $query === '' ? [] : $controller->searchProducts($query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Search Results</h1>

    <nav>
        <a href="../index.php">Home</a>
    </nav>

    <form action="search_results.php" method="GET" class="search-form">
        <input type="text" name="user_query" placeholder="Search products..."
               value="<?= htmlspecialchars($query) ?>">
        <button type="submit">Search</button>
    </form>

    <p>Results for: <strong><?= htmlspecialchars($query) ?></strong></p>

    <?php if (empty($products)): ?>
        <p>No products found.</p>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <?php if (!empty($p['product_image'])): ?>
                        <img src="../images/products/<?= htmlspecialchars($p['product_image']) ?>" alt="">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($p['product_title']) ?></h3>
                    <p>GHS <?= number_format((float)$p['product_price'], 2) ?></p>
                    <a href="single_product.php?pro_id=<?= (int)$p['product_id'] ?>">Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>