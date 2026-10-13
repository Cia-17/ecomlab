<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect("../view/admin/product.php");
}

$cat      = (int)($_POST['product_cat'] ?? 0);
$brand    = (int)($_POST['product_brand'] ?? 0);
$title    = trim($_POST['product_title'] ?? '');
$price    = (float)($_POST['product_price'] ?? 0);
$desc     = trim($_POST['product_desc'] ?? '');
$keywords = trim($_POST['product_keywords'] ?? '');

if ($cat <= 0 || $brand <= 0 || $title === '' || $price <= 0) {
    $_SESSION['error'] = "All required fields must be filled, and price must be greater than zero.";
    redirect("../view/admin/product.php");
}

$filename = null;

if (!empty($_FILES['product_image']['name'])) {
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($_FILES['product_image']['type'], $allowed)) {
        $_SESSION['error'] = "Only JPG, PNG, GIF and WEBP images are allowed.";
        redirect("../view/admin/product.php");
    }

    if ($_FILES['product_image']['size'] > 2 * 1024 * 1024) {
        $_SESSION['error'] = "Image must be smaller than 2 MB.";
        redirect("../view/admin/product.php");
    }

    $ext = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $ext;

    $target = __DIR__ . "/../images/products/" . $filename;

    if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $target)) {
        $_SESSION['error'] = "Could not save the uploaded image. Check the images/products/ folder permissions.";
        redirect("../view/admin/product.php");
    }
}

$controller = new ProductController();
if ($controller->addProduct($cat, $brand, $title, $price, $desc, $filename, $keywords)) {
    $_SESSION['success'] = "Product added.";
} else {
    $_SESSION['error'] = "Could not add product.";
}
redirect("../view/admin/product.php");