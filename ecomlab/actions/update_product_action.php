<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";
require_once __DIR__ . "/../functions/upload_functions.php";

require_admin();

$list = base_url() . "/view/admin/product.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($list);
}

if (empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    $_SESSION['error'] = "The upload was too large. Images must be smaller than 2 MB.";
    redirect($list);
}

$id       = (int)($_POST['product_id'] ?? 0);
$cat      = (int)($_POST['product_cat'] ?? 0);
$brand    = (int)($_POST['product_brand'] ?? 0);
$title    = clean_input($_POST['product_title'] ?? '');
$priceRaw = trim($_POST['product_price'] ?? '');
$desc     = clean_input($_POST['product_desc'] ?? '');
$keywords = clean_input($_POST['product_keywords'] ?? '');

$back = $list . "?edit_id=" . $id;

function fail_product($message, $back)
{
    $_SESSION['error'] = $message;
    redirect($back);
}

if ($id <= 0 || $cat <= 0 || $brand <= 0 || $title === '' || $priceRaw === '') {
    fail_product("Title, price, category and brand are required.", $back);
}
if (!is_numeric($priceRaw) || (float)$priceRaw <= 0 || (float)$priceRaw > 9999999) {
    fail_product("Price must be a number greater than zero.", $back);
}
$price = round((float)$priceRaw, 2);

if (text_length($title) < 2 || text_length($title) > 200) {
    fail_product("Title must be between 2 and 200 characters.", $back);
}
if (text_length($desc) > 500) {
    fail_product("Description must be 500 characters or fewer.", $back);
}
if (text_length($keywords) > 100) {
    fail_product("Keywords must be 100 characters or fewer.", $back);
}

$controller = new ProductController();

$current = $controller->getProductById($id);
if (!$current) {
    fail_product("That product no longer exists.", $list);
}
if (!$controller->getCategoryById($cat) || !$controller->getBrandById($brand)) {
    fail_product("Please choose a valid category and brand.", $back);
}

// Keep the current image unless a new one is uploaded. The hidden
// "existing_image" field is sent by the browser, so it can be tampered with:
// we only accept it if it matches what is really stored in the database,
// otherwise we use the database value.
$filename = $current['product_image'] ?? null;
if (isset($_POST['existing_image']) && $_POST['existing_image'] !== $filename) {
    $_POST['existing_image'] = $filename;   // ignore a tampered value
}

$upload = save_uploaded_image($_FILES['product_image'] ?? null, __DIR__ . "/../images/products");
if ($upload['status'] === 'error') {
    fail_product($upload['error'], $back);
}
$newFile = null;
if ($upload['status'] === 'ok') {
    $newFile  = $upload['filename'];
    $filename = $newFile;
}

try {
    $saved = $controller->updateProduct($id, $cat, $brand, $title, $price, $desc, $filename, $keywords);
} catch (Throwable $e) {
    log_error($e->getMessage());
    $saved = false;
}

if ($saved) {
    $_SESSION['success'] = "Product updated.";
    redirect($list);
}

if ($newFile !== null) {
    @unlink(__DIR__ . "/../images/products/" . $newFile);
}
fail_product("Could not update product.", $back);
