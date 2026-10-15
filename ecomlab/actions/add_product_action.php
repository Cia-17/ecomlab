<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";
require_once __DIR__ . "/../functions/upload_functions.php";

require_admin();

$back = base_url() . "/view/admin/product.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($back);
}

// If the upload is bigger than PHP's post_max_size, PHP drops ALL the form
// data. Detect that so the admin gets a clear message instead of a vague one.
if (empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    $_SESSION['error'] = "The upload was too large. Images must be smaller than 2 MB.";
    redirect($back);
}

$cat      = (int)($_POST['product_cat'] ?? 0);
$brand    = (int)($_POST['product_brand'] ?? 0);
$title    = clean_input($_POST['product_title'] ?? '');
$priceRaw = trim($_POST['product_price'] ?? '');
$desc     = clean_input($_POST['product_desc'] ?? '');
$keywords = clean_input($_POST['product_keywords'] ?? '');

// Keep what was typed so the form can be re-filled after an error
$_SESSION['old'] = [
    'title' => $title, 'price' => $priceRaw, 'desc' => $desc,
    'keywords' => $keywords, 'cat' => $cat, 'brand' => $brand,
];

function fail_product($message, $back)
{
    $_SESSION['error'] = $message;
    redirect($back);
}

// ---- Server-side validation ----
if ($cat <= 0 || $brand <= 0 || $title === '' || $priceRaw === '') {
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

// The chosen category and brand must really exist
if (!$controller->getCategoryById($cat) || !$controller->getBrandById($brand)) {
    fail_product("Please choose a valid category and brand.", $back);
}

// ---- Image upload: validated on the server (type, real content, size) ----
$filename = null;
$upload = save_uploaded_image($_FILES['product_image'] ?? null, __DIR__ . "/../images/products");
if ($upload['status'] === 'error') {
    fail_product($upload['error'], $back);
}
if ($upload['status'] === 'ok') {
    $filename = $upload['filename'];
}

try {
    $saved = $controller->addProduct($cat, $brand, $title, $price, $desc, $filename, $keywords);
} catch (Throwable $e) {
    log_error($e->getMessage());
    $saved = false;
}

if ($saved) {
    unset($_SESSION['old']);
    $_SESSION['success'] = "Product added.";
} else {
    if ($filename !== null) {
        @unlink(__DIR__ . "/../images/products/" . $filename);
    }
    $_SESSION['error'] = "Could not add product.";
}
redirect($back);
