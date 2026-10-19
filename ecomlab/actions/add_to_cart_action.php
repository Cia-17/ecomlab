<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/CartController.php";
require_once __DIR__ . "/../controllers/ProductController.php";

// The "Add to Cart" links send the product id in the URL: ?add_cart=N
$back = safe_back(base_url() . "/index.php");

$id = filter_var($_GET['add_cart'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) {
    $_SESSION['error'] = "Invalid product.";
    redirect($back);
}

try {
    // The product must really exist
    $products = new ProductController();
    if (!$products->getProductById($id)) {
        $_SESSION['error'] = "That product does not exist.";
        redirect($back);
    }

    $cart = new CartController();
    $ip   = get_ip();

    if ($cart->isInCart($id, $ip)) {
        $_SESSION['error'] = "This item is already in your cart. Go to cart to update quantity.";
        redirect($back);
    }

    if ($cart->addToCart($id, 1, $ip, current_user_id())) {
        $_SESSION['success'] = "Item added to your cart.";
    } else {
        $_SESSION['error'] = "Could not add the item to your cart.";
    }
} catch (Throwable $e) {
    log_error($e->getMessage());
    $_SESSION['error'] = "Could not add the item to your cart.";
}

redirect($back);
