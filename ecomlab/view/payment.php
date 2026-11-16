<?php
// Payment page: shows the total and opens the Paystack pop-up.
// The real work happens afterwards in actions/process_payment_action.php.
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

$publicKey = defined('PAYSTACK_PUBLIC_KEY') ? PAYSTACK_PUBLIC_KEY : '';
$amountPesewas = (int)round($total * 100);

$page_title = "Payment";
require __DIR__ . "/layout/header.php";
?>
<div class="container page-layout">
    <?php require __DIR__ . "/layout/sidebar.php"; ?>
    <main class="content">
        <h1 class="page-title">Payment</h1>
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

            <?php if ($publicKey === ''): ?>
                <div class="alert alert-error">Paystack public key is missing in core/db_cred.php.</div>
            <?php else: ?>
                <div class="cart-actions">
                    <div><a class="btn btn-outline" href="<?= e($base) ?>/view/checkout.php">Back</a></div>
                    <div><button type="button" class="btn btn-primary" onclick="payWithPaystack()">Pay GHS <?= number_format($total, 2) ?></button></div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
function payWithPaystack() {
    var handler = PaystackPop.setup({
        key: <?= json_encode($publicKey) ?>,
        email: <?= json_encode($_SESSION['customer_email'] ?? '') ?>,
        amount: <?= $amountPesewas ?>,   // in pesewas (GHS x 100)
        currency: 'GHS',
        callback: function (response) {
            // Paystack says it worked - the server double-checks before saving the order
            window.location = <?= json_encode($base . '/actions/process_payment_action.php') ?> + '?ref=' + encodeURIComponent(response.reference);
        },
        onClose: function () {
            alert('Payment cancelled.');
        }
    });
    handler.openIframe();
}
</script>
<?php require __DIR__ . "/layout/footer.php"; ?>
