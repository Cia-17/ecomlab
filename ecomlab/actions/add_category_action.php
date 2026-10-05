<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect("../view/admin/category.php");
}

$name = trim($_POST['cat_name'] ?? '');

if ($name === '') {
    $_SESSION['error'] = "Category name is required.";
    redirect("../view/admin/category.php");
}

$controller = new ProductController();
if ($controller->addCategory($name)) {
    $_SESSION['success'] = "Category added.";
} else {
    $_SESSION['error'] = "Could not add category.";
}
redirect("../view/admin/category.php");