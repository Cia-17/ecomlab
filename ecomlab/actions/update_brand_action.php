<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect("../view/admin/brand.php");
}

$id   = (int)($_POST['brand_id'] ?? 0);
$name = trim($_POST['brand_name'] ?? '');

if ($id <= 0 || $name === '') {
    $_SESSION['error'] = "Invalid brand.";
    redirect("../view/admin/brand.php");
}

$controller = new ProductController();
if ($controller->updateBrand($id, $name)) {
    $_SESSION['success'] = "Brand updated.";
} else {
    $_SESSION['error'] = "Could not update brand.";
}
redirect("../view/admin/brand.php");