<?php
// Checkout: review the order before paying. Customers must be logged in.
// (The payment step itself is added in Task 13.)
require_once __DIR__ . "/../core/core.php";
require_login();
require_once __DIR__ . "/../controllers/CartController.php";

$cartController = new CartController();
$ip    = get_ip();
$items = $cartController->getCartItems($ip);
$total = $cartController->getCartTotal($ip);

if (empty($items)) {
    $_SESSION['error'] = "Your cart is empty.";
    redirect(base_url() . "/view/cart.php");
}

$page_title = "Checkout";
require __DIR__ . "/layout/header.php";
?>
<div class="container page-layout">
    <?php require __DIR__ . "/layout/sidebar.php"; ?>
    <main class="content">
        <h1 class="page-title">Checkout</h1>
        <?php require __DIR__ . "/layout/flash.php"; ?>

        <div class="card">
            <h2>Order summary</h2>
            <div class="table-wrap">
                <table class="cart-table">
                    <thead>
                        <tr><th>Product</th><th>Quantity</th><th>Unit price</th><th class="text-right">Total</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= e($item['product_title']) ?></td>
                                <td><?= (int)$item['qty'] ?></td>
                                <td>GHS <?= number_format((float)$item['product_price'], 2) ?></td>
                                <td class="text-right">GHS <?= number_format((float)$item['product_price'] * (int)$item['qty'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right">Total</td>
                            <td class="text-right">GHS <?= number_format($total, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <p class="muted">Payment is not available yet.</p>
            <div class="cart-actions">
                <div><a class="btn btn-outline" href="<?= e($base) ?>/view/cart.php">Back to cart</a></div>
            </div>
        </div>
    </main>
</div>
<?php require __DIR__ . "/layout/footer.php"; ?>
