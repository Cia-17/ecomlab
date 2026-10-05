<?php
require_once __DIR__ . "/../../core/core.php";
require_admin();

require_once __DIR__ . "/../../controllers/ProductController.php";


$controller = new ProductController();
$brands = $controller->getAllBrands();

$editing = false;
$editBrand = null;
if (isset($_GET['edit_id'])) {
    $editBrand = $controller->getBrandById((int)$_GET['edit_id']);
    if ($editBrand) $editing = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Brands</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <h1>Manage Brands</h1>

    <nav>
        <a href="../../index.php">Home</a> |
        <a href="brand.php">Brands</a> |
        <a href="category.php">Categories</a>
    </nav>

    <?php if (!empty($_SESSION['success'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <h2><?= $editing ? "Edit Brand" : "Add Brand" ?></h2>

    <form method="POST" action="../../actions/<?= $editing ? 'update_brand_action.php' : 'add_brand_action.php' ?>">
        <?php if ($editing): ?>
            <input type="hidden" name="brand_id" value="<?= (int)$editBrand['brand_id'] ?>">
        <?php endif; ?>
        <div>
            <label>Brand Name</label>
            <input type="text" name="brand_name" value="<?= $editing ? htmlspecialchars($editBrand['brand_name']) : '' ?>" required>
        </div>
        <button type="submit"><?= $editing ? "Update" : "Add" ?></button>
        <?php if ($editing): ?>
            <a href="brand.php">Cancel</a>
        <?php endif; ?>
    </form>

    <h2>All Brands</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Name</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php if (empty($brands)): ?>
                <tr><td colspan="3">No brands yet.</td></tr>
            <?php else: foreach ($brands as $b): ?>
                <tr>
                    <td><?= (int)$b['brand_id'] ?></td>
                    <td><?= htmlspecialchars($b['brand_name']) ?></td>
                    <td><a href="brand.php?edit_id=<?= (int)$b['brand_id'] ?>">Edit</a></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</body>
</html>