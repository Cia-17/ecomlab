<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

require_admin();

$back = base_url() . "/view/admin/brand.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($back);
}

$name = clean_input($_POST['brand_name'] ?? '');

if ($name === '') {
    $_SESSION['error'] = "Brand name is required.";
    redirect($back);
}
if (text_length($name) < 2 || text_length($name) > 100) {
    $_SESSION['error'] = "Brand name must be between 2 and 100 characters.";
    redirect($back);
}

try {
    $controller = new ProductController();
    if ($controller->addBrand($name)) {
        $_SESSION['success'] = "Brand added.";
    } else {
        $_SESSION['error'] = "Could not add brand.";
    }
} catch (Throwable $e) {
    log_error($e->getMessage());
    $_SESSION['error'] = "Could not add brand.";
}
redirect($back);
