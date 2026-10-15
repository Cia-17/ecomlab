<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

require_admin();

$back = base_url() . "/view/admin/category.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($back);
}

$id   = (int)($_POST['cat_id'] ?? 0);
$name = clean_input($_POST['cat_name'] ?? '');

if ($id <= 0 || $name === '') {
    $_SESSION['error'] = "Invalid category.";
    redirect($back);
}
if (text_length($name) < 2 || text_length($name) > 100) {
    $_SESSION['error'] = "Category name must be between 2 and 100 characters.";
    redirect($back . "?edit_id=" . $id);
}

try {
    $controller = new ProductController();
    if ($controller->updateCategory($id, $name)) {
        $_SESSION['success'] = "Category updated.";
    } else {
        $_SESSION['error'] = "Could not update category.";
    }
} catch (Throwable $e) {
    log_error($e->getMessage());
    $_SESSION['error'] = "Could not update category.";
}
redirect($back);
