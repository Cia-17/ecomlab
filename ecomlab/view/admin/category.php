<?php
require_once __DIR__ . "/../../core/core.php";
require_admin();

require_once __DIR__ . "/../../controllers/ProductController.php";


$controller = new ProductController();
$categories = $controller->getAllCategories();

$editing = false;
$editCat = null;
if (isset($_GET['edit_id'])) {
    $editCat = $controller->getCategoryById((int)$_GET['edit_id']);
    if ($editCat) $editing = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <h1>Manage Categories</h1>

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

    <h2><?= $editing ? "Edit Category" : "Add Category" ?></h2>

    <form method="POST" action="../../actions/<?= $editing ? 'update_category_action.php' : 'add_category_action.php' ?>">
        <?php if ($editing): ?>
            <input type="hidden" name="cat_id" value="<?= (int)$editCat['cat_id'] ?>">
        <?php endif; ?>
        <div>
            <label>Category Name</label>
            <input type="text" name="cat_name" value="<?= $editing ? htmlspecialchars($editCat['cat_name']) : '' ?>" required>
        </div>
        <button type="submit"><?= $editing ? "Update" : "Add" ?></button>
        <?php if ($editing): ?>
            <a href="category.php">Cancel</a>
        <?php endif; ?>
    </form>

    <h2>All Categories</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Name</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr><td colspan="3">No categories yet.</td></tr>
            <?php else: foreach ($categories as $c): ?>
                <tr>
                    <td><?= (int)$c['cat_id'] ?></td>
                    <td><?= htmlspecialchars($c['cat_name']) ?></td>
                    <td><a href="category.php?edit_id=<?= (int)$c['cat_id'] ?>">Edit</a></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</body>
</html>