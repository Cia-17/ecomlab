<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect("../view/admin/brand.php");
}

$name = trim($_POST['brand_name'] ?? '');

if ($name === '') {
    $_SESSION['error'] = "Brand name is required.";
    redirect("../view/admin/brand.php");
}

$controller = new ProductController();
if ($controller->addBrand($name)) {
    $_SESSION['success'] = "Brand added.";
} else {
    $_SESSION['error'] = "Could not add brand.";
}
redirect("../view/admin/brand.php");