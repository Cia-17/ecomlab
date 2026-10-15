<?php
require_once __DIR__ . "/../../core/core.php";
require_admin();                       // first thing - before any output

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

// Values shown in the form: from the database when editing, or what the
// admin typed before a server-side error when adding.
$f = ['title' => '', 'price' => '', 'desc' => '', 'keywords' => '', 'cat' => '', 'brand' => ''];
if ($editing) {
    $f = [
        'title' => $editProduct['product_title'], 'price' => $editProduct['product_price'],
        'desc' => $editProduct['product_desc'], 'keywords' => $editProduct['product_keywords'],
        'cat' => $editProduct['product_cat'], 'brand' => $editProduct['product_brand'],
    ];
} else {
    $f = array_merge($f, pull_old());
}

$page_title   = "Manage Products";
$page_scripts = ['validate.js'];
$sidebar_mode = 'admin';
require __DIR__ . "/../layout/header.php";
?>
<div class="container page-layout">
    <?php require __DIR__ . "/../layout/sidebar.php"; ?>
    <main class="content">
        <h1 class="page-title">Manage Products</h1>
        <?php require __DIR__ . "/../layout/flash.php"; ?>

        <section class="card">
            <h2><?= $editing ? "Edit product" : "Add product" ?></h2>

            <form id="product-form" method="POST" enctype="multipart/form-data" novalidate
                  action="<?= e(base_url()) ?>/actions/<?= $editing ? 'update_product_action.php' : 'add_product_action.php' ?>">

                <?php if ($editing): ?>
                    <input type="hidden" name="product_id" value="<?= (int)$editProduct['product_id'] ?>">
                    <input type="hidden" name="existing_image" value="<?= e($editProduct['product_image'] ?? '') ?>">
                <?php endif; ?>

                <div class="form-grid">
                    <div class="field span-2">
                        <label for="product_title">Product title</label>
                        <input type="text" id="product_title" name="product_title" maxlength="200" value="<?= e($f['title']) ?>">
                        <span class="field-error"></span>
                    </div>

                    <div class="field">
                        <label for="product_cat">Category</label>
                        <select id="product_cat" name="product_cat">
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= (int)$c['cat_id'] ?>"<?= ((int)$f['cat'] === (int)$c['cat_id']) ? ' selected' : '' ?>><?= e($c['cat_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field-error"></span>
                    </div>

                    <div class="field">
                        <label for="product_brand">Brand</label>
                        <select id="product_brand" name="product_brand">
                            <option value="">Select a brand</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?= (int)$b['brand_id'] ?>"<?= ((int)$f['brand'] === (int)$b['brand_id']) ? ' selected' : '' ?>><?= e($b['brand_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field-error"></span>
                    </div>

                    <div class="field">
                        <label for="product_price">Price (GHS)</label>
                        <input type="number" step="0.01" min="0.01" id="product_price" name="product_price" value="<?= e($f['price']) ?>">
                        <span class="field-error"></span>
                    </div>

                    <div class="field">
                        <label for="product_keywords">Keywords</label>
                        <input type="text" id="product_keywords" name="product_keywords" maxlength="100" value="<?= e($f['keywords']) ?>">
                        <span class="hint">Comma-separated, used by search.</span>
                        <span class="field-error"></span>
                    </div>

                    <div class="field span-2">
                        <label for="product_desc">Description</label>
                        <textarea id="product_desc" name="product_desc" rows="4" maxlength="500"><?= e($f['desc']) ?></textarea>
                        <span class="field-error"></span>
                    </div>

                    <div class="field span-2">
                        <label for="product_image">Product image<?= $editing ? ' (leave empty to keep the current one)' : '' ?></label>
                        <input type="file" id="product_image" name="product_image"
                               accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp">
                        <span class="hint">JPG, PNG, GIF or WEBP, up to 2 MB.</span>
                        <span class="field-error"></span>
                        <?php if ($editing && !empty($editProduct['product_image'])): ?>
                            <div class="current-image">
                                <img src="<?= e(base_url()) ?>/images/products/<?= e($editProduct['product_image']) ?>" alt="Current product image">
                                <span class="hint">Current image</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?= $editing ? "Update product" : "Add product" ?></button>
                    <?php if ($editing): ?>
                        <a class="btn btn-outline" href="product.php">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>All Products</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Image</th><th>Title</th><th>Category</th><th>Brand</th><th>Price</th><th class="col-action">Action</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="7">No products yet.</td></tr>
                        <?php else: foreach ($products as $p): ?>
                            <tr>
                                <td><?= (int)$p['product_id'] ?></td>
                                <td>
                                    <?php if (!empty($p['product_image'])): ?>
                                        <img class="thumb" src="<?= e(base_url()) ?>/images/products/<?= e($p['product_image']) ?>" alt="">
                                    <?php else: ?>
                                        <span class="thumb thumb-empty"></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($p['product_title']) ?></td>
                                <td><?= e($p['cat_name'] ?? '') ?></td>
                                <td><?= e($p['brand_name'] ?? '') ?></td>
                                <td>GHS <?= number_format((float)$p['product_price'], 2) ?></td>
                                <td class="col-action"><a class="btn btn-outline btn-sm" href="product.php?edit_id=<?= (int)$p['product_id'] ?>">Edit</a></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<?php require __DIR__ . "/../layout/footer.php"; ?>
