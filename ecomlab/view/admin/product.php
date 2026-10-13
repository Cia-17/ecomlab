<?php
require_once __DIR__ . "/../../core/core.php";
require_admin();

require_once __DIR__ . "/../../controllers/ProductController.php";
$controller = new ProductController();

$categories = $controller->getAllCategories();
$brands     = $controller->getAllBrands();
$products   = $controller->getAllProducts();

$editing = false;
$editProduct = null;
if (isset($_GET['edit_id'])) {
    $editProduct = $controller->getProductById((int)$_GET['edit_id']);
    if ($editProduct) $editing = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Products</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<script src="../../js/validate_product.js"></script>
<body>
    <h1>Manage Products</h1>

    <nav>
        <a href="../../index.php">Home</a> |
        <a href="brand.php">Brands</a> |
        <a href="category.php">Categories</a> |
        <a href="product.php">Products</a>
    </nav>

    <?php if (!empty($_SESSION['success'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <h2><?= $editing ? "Edit Product" : "Add Product" ?></h2>

    <form method="POST"
          action="../../actions/<?= $editing ? 'update_product_action.php' : 'add_product_action.php' ?>"
          enctype="multipart/form-data">

        <?php if ($editing): ?>
            <input type="hidden" name="product_id" value="<?= (int)$editProduct['product_id'] ?>">
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editProduct['product_image'] ?? '') ?>">
        <?php endif; ?>

        <div>
            <label>Product Title</label>
            <input type="text" name="product_title" required
                   value="<?= $editing ? htmlspecialchars($editProduct['product_title']) : '' ?>">
        </div>

        <div>
            <label>Price (GHS)</label>
            <input type="number" step="0.01" min="0.01" name="product_price" required
                   value="<?= $editing ? htmlspecialchars($editProduct['product_price']) : '' ?>">
        </div>

        <div>
            <label>Description</label>
            <textarea name="product_desc" rows="4"><?= $editing ? htmlspecialchars($editProduct['product_desc']) : '' ?></textarea>
        </div>

        <div>
            <label>Keywords (comma-separated)</label>
            <input type="text" name="product_keywords"
                   value="<?= $editing ? htmlspecialchars($editProduct['product_keywords']) : '' ?>">
        </div>

        <div>
            <label>Category</label>
            <select name="product_cat" required>
                <option value="">-- Select --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int)$c['cat_id'] ?>"
                        <?= ($editing && $editProduct['product_cat'] == $c['cat_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['cat_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Brand</label>
            <select name="product_brand" required>
                <option value="">-- Select --</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= (int)$b['brand_id'] ?>"
                        <?= ($editing && $editProduct['product_brand'] == $b['brand_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['brand_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Product Image <?= $editing ? '(leave blank to keep current)' : '' ?></label>
            <input type="file" name="product_image" accept="image/*">
            <?php if ($editing && !empty($editProduct['product_image'])): ?>
                <p><img src="../../images/products/<?= htmlspecialchars($editProduct['product_image']) ?>" style="max-width:150px;" alt="current image"></p>
            <?php endif; ?>
        </div>

        <button type="submit"><?= $editing ? "Update Product" : "Add Product" ?></button>
        <?php if ($editing): ?>
            <a href="product.php">Cancel</a>
        <?php endif; ?>
    </form>

    <h2>All Products</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Image</th><th>Title</th><th>Category</th>
                <th>Brand</th><th>Price</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="7">No products yet.</td></tr>
            <?php else: foreach ($products as $p): ?>
                <tr>
                    <td><?= (int)$p['product_id'] ?></td>
                    <td>
                        <?php if (!empty($p['product_image'])): ?>
                            <img src="../../images/products/<?= htmlspecialchars($p['product_image']) ?>" style="max-width:50px;" alt="">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['product_title']) ?></td>
                    <td><?= htmlspecialchars($p['cat_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['brand_name'] ?? '') ?></td>
                    <td>GHS <?= number_format((float)$p['product_price'], 2) ?></td>
                    <td><a href="product.php?edit_id=<?= (int)$p['product_id'] ?>">Edit</a></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</body>
</html>