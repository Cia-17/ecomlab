<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

require_admin();

$back = base_url() . "/view/admin/brand.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($back);
}

$id   = (int)($_POST['brand_id'] ?? 0);
$name = clean_input($_POST['brand_name'] ?? '');

if ($id <= 0 || $name === '') {
    $_SESSION['error'] = "Invalid brand.";
    redirect($back);
}
if (text_length($name) < 2 || text_length($name) > 100) {
    $_SESSION['error'] = "Brand name must be between 2 and 100 characters.";
    redirect($back . "?edit_id=" . $id);
}

try {
    $controller = new ProductController();
    if ($controller->updateBrand($id, $name)) {
        $_SESSION['success'] = "Brand updated.";
    } else {
        $_SESSION['error'] = "Could not update brand.";
    }
} catch (Throwable $e) {
    log_error($e->getMessage());
    $_SESSION['error'] = "Could not update brand.";
}
redirect($back);
