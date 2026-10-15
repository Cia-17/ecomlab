<?php
require_once __DIR__ . "/../../core/core.php";
require_admin();                       // first thing - before any output

require_once __DIR__ . "/../../controllers/ProductController.php";

$controller = new ProductController();
$categories = $controller->getAllCategories();

$editing = false;
$editCategory = null;
if (isset($_GET['edit_id'])) {
    $editCategory = $controller->getCategoryById((int)$_GET['edit_id']);
    if ($editCategory) $editing = true;
}

$page_title   = "Manage Categories";
$page_scripts = ['validate.js'];
$sidebar_mode = 'admin';
require __DIR__ . "/../layout/header.php";
?>
<div class="container page-layout">
    <?php require __DIR__ . "/../layout/sidebar.php"; ?>
    <main class="content">
        <h1 class="page-title">Manage Categories</h1>
        <?php require __DIR__ . "/../layout/flash.php"; ?>

        <section class="card">
            <h2><?= $editing ? "Edit Category" : "Add Category" ?></h2>
            <form id="category-form" method="POST" novalidate
                  action="<?= e(base_url()) ?>/actions/<?= $editing ? 'update_category_action.php' : 'add_category_action.php' ?>">
                <?php if ($editing): ?>
                    <input type="hidden" name="cat_id" value="<?= (int)$editCategory['cat_id'] ?>">
                <?php endif; ?>
                <div class="field">
                    <label for="cat_name">Category name</label>
                    <input type="text" id="cat_name" name="cat_name" maxlength="100"
                           value="<?= $editing ? e($editCategory['cat_name']) : '' ?>">
                    <span class="field-error"></span>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?= $editing ? "Update category" : "Add category" ?></button>
                    <?php if ($editing): ?>
                        <a class="btn btn-outline" href="category.php">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>All Categories</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Name</th><th class="col-action">Action</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr><td colspan="3">No categories yet.</td></tr>
                        <?php else: foreach ($categories as $row): ?>
                            <tr>
                                <td><?= (int)$row['cat_id'] ?></td>
                                <td><?= e($row['cat_name']) ?></td>
                                <td class="col-action"><a class="btn btn-outline btn-sm" href="category.php?edit_id=<?= (int)$row['cat_id'] ?>">Edit</a></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<?php require __DIR__ . "/../layout/footer.php"; ?>
