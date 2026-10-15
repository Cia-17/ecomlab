<?php
require_once __DIR__ . "/../../core/core.php";
require_admin();                       // first thing - before any output

require_once __DIR__ . "/../../controllers/ProductController.php";

$controller = new ProductController();
$brands = $controller->getAllBrands();

$editing = false;
$editBrand = null;
if (isset($_GET['edit_id'])) {
    $editBrand = $controller->getBrandById((int)$_GET['edit_id']);
    if ($editBrand) $editing = true;
}

$page_title   = "Manage Brands";
$page_scripts = ['validate.js'];
$sidebar_mode = 'admin';
require __DIR__ . "/../layout/header.php";
?>
<div class="container page-layout">
    <?php require __DIR__ . "/../layout/sidebar.php"; ?>
    <main class="content">
        <h1 class="page-title">Manage Brands</h1>
        <?php require __DIR__ . "/../layout/flash.php"; ?>

        <section class="card">
            <h2><?= $editing ? "Edit Brand" : "Add Brand" ?></h2>
            <form id="brand-form" method="POST" novalidate
                  action="<?= e(base_url()) ?>/actions/<?= $editing ? 'update_brand_action.php' : 'add_brand_action.php' ?>">
                <?php if ($editing): ?>
                    <input type="hidden" name="brand_id" value="<?= (int)$editBrand['brand_id'] ?>">
                <?php endif; ?>
                <div class="field">
                    <label for="brand_name">Brand name</label>
                    <input type="text" id="brand_name" name="brand_name" maxlength="100"
                           value="<?= $editing ? e($editBrand['brand_name']) : '' ?>">
                    <span class="field-error"></span>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?= $editing ? "Update brand" : "Add brand" ?></button>
                    <?php if ($editing): ?>
                        <a class="btn btn-outline" href="brand.php">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>All Brands</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Name</th><th class="col-action">Action</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($brands)): ?>
                            <tr><td colspan="3">No brands yet.</td></tr>
                        <?php else: foreach ($brands as $row): ?>
                            <tr>
                                <td><?= (int)$row['brand_id'] ?></td>
                                <td><?= e($row['brand_name']) ?></td>
                                <td class="col-action"><a class="btn btn-outline btn-sm" href="brand.php?edit_id=<?= (int)$row['brand_id'] ?>">Edit</a></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<?php require __DIR__ . "/../layout/footer.php"; ?>
