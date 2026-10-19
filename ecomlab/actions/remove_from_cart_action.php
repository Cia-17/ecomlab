<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/CartController.php";

$cartPage = base_url() . "/view/cart.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($cartPage);
}

// The cart page sends the ticked checkboxes as remove[]
$ids = $_POST['remove'] ?? [];
if (!is_array($ids) || count($ids) === 0) {
    $_SESSION['error'] = "Select at least one item to remove.";
    redirect($cartPage);
}

// Check every id first (must be a positive whole number) ...
$clean = [];
foreach ($ids as $raw) {
    $id = filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        $_SESSION['error'] = "Invalid product selected.";
        redirect($cartPage);
    }
    $clean[] = $id;
}

// ... then remove them
try {
    $cart = new CartController();
    $ip   = get_ip();
    foreach ($clean as $id) {
        $cart->removeFromCart($id, $ip);
    }
    $_SESSION['success'] = count($clean) === 1
        ? "Item removed from your cart."
        : count($clean) . " items removed from your cart.";
} catch (Throwable $e) {
    log_error($e->getMessage());
    $_SESSION['error'] = "Could not remove the item(s). Please try again.";
}

redirect($cartPage);
