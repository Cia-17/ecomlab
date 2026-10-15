<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

$query = clean_input($_GET['user_query'] ?? '');
if (text_length($query) > 100) {
    $query = substr($query, 0, 100);
}

$controller = new ProductController();
$products = $query === '' ? [] : $controller->searchProducts($query);

$page_title = "Search results";
require __DIR__ . "/layout/header.php";
?>
<div class="container page-layout">
    <?php require __DIR__ . "/layout/sidebar.php"; ?>
    <main class="content">
        <h1 class="page-title">Search results</h1>
        <?php require __DIR__ . "/layout/flash.php"; ?>

        <?php if ($query === ''): ?>
            <p class="muted">Type something in the search box to find products.</p>
        <?php else: ?>
            <p class="muted">Results for <strong><?= e($query) ?></strong></p>
        <?php endif; ?>

        <?php if ($query !== ''): require __DIR__ . "/layout/product_grid.php"; endif; ?>
    </main>
</div>
<?php require __DIR__ . "/layout/footer.php"; ?>
