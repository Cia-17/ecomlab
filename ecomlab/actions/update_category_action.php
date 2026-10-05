<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";


require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect("../view/admin/category.php");
}

$id   = (int)($_POST['cat_id'] ?? 0);
$name = trim($_POST['cat_name'] ?? '');

if ($id <= 0 || $name === '') {
    $_SESSION['error'] = "Invalid category.";
    redirect("../view/admin/category.php");
}

$controller = new ProductController();
if ($controller->updateCategory($id, $name)) {
    $_SESSION['success'] = "Category updated.";
} else {
    $_SESSION['error'] = "Could not update category.";
}
redirect("../view/admin/category.php");