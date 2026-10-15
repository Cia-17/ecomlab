<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

require_admin();

$back = base_url() . "/view/admin/category.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($back);
}

$name = clean_input($_POST['cat_name'] ?? '');

if ($name === '') {
    $_SESSION['error'] = "Category name is required.";
    redirect($back);
}
if (text_length($name) < 2 || text_length($name) > 100) {
    $_SESSION['error'] = "Category name must be between 2 and 100 characters.";
    redirect($back);
}

try {
    $controller = new ProductController();
    if ($controller->addCategory($name)) {
        $_SESSION['success'] = "Category added.";
    } else {
        $_SESSION['error'] = "Could not add category.";
    }
} catch (Throwable $e) {
    log_error($e->getMessage());
    $_SESSION['error'] = "Could not add category.";
}
redirect($back);
