<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/CartController.php";

$cartPage = base_url() . "/view/cart.php";
const MAX_CART_QTY = 99;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($cartPage);
}

// The cart page sends two matching lists: update[] (product ids) and qty[]
$ids  = $_POST['update'] ?? [];
$qtys = $_POST['qty'] ?? [];

if (!is_array($ids) || !is_array($qtys) || count($ids) === 0 || count($ids) !== count($qtys)) {
    $_SESSION['error'] = "Nothing to update.";
    redirect($cartPage);
}

// Check EVERYTHING first, so a mistake in one row does not leave the cart
// half-updated.
$updates = [];
$ids  = array_values($ids);
$qtys = array_values($qtys);
foreach ($ids as $i => $rawId) {
    $id  = filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $qty = is_string($qtys[$i]) ? trim($qtys[$i]) : '';

    if ($id === false) {
        $_SESSION['error'] = "Invalid product.";
        redirect($cartPage);
    }
    if (!ctype_digit($qty) || (int)$qty < 1 || (int)$qty > MAX_CART_QTY) {
        $_SESSION['error'] = "Quantity must be a whole number from 1 to " . MAX_CART_QTY . ".";
        redirect($cartPage);
    }
    $updates[$id] = (int)$qty;
}

try {
    $cart = new CartController();
    $ip   = get_ip();
    foreach ($updates as $id => $qty) {
        $cart->updateQty($id, $qty, $ip);
    }
    $_SESSION['success'] = "Cart updated.";
} catch (Throwable $e) {
    log_error($e->getMessage());
    $_SESSION['error'] = "Could not update the cart. Please try again.";
}

redirect($cartPage);
