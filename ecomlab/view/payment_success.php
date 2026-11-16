<?php
// Invoice shown after a successful payment. Display only - no SQL here.
require_once __DIR__ . "/../core/core.php";
require_login();
require_once __DIR__ . "/../controllers/CartController.php";

$order_id = filter_var($_GET['order_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($order_id === false || $order_id === null) {
    redirect(base_url() . "/index.php");
}

$cartController = new CartController();
$invoice = $cartController->getOrderById($order_id, current_user_id());
if (!$invoice) {
    $_SESSION['error'] = "Order not found.";
    redirect(base_url() . "/index.php");
}
$order = $invoice['order'];
$items = $invoice['items'];

$page_title = "Payment Successful";
require __DIR__ . "/layout/header.php";
?>
<div class="container page-layout">
    <?php require __DIR__ . "/layout/sidebar.php"; ?>
    <main class="content">
        <h1 class="page-title">Payment Successful</h1>
        <div class="alert alert-success">Thank you! Your payment was received.</div>

        <div class="card">
            <h2>Invoice #<?= (int)$order['invoice_no'] ?></h2>
            <p>
                Order number: <?= (int)$order['order_id'] ?><br>
                Date: <?= e($order['order_date']) ?><br>
                Status: <?= e($order['order_status']) ?><br>
                Payment reference: <?= e($order['reference']) ?>
            </p>
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
                            <td colspan="3" class="text-right">Total paid</td>
                            <td class="text-right"><?= e($order['currency']) ?> <?= number_format((float)$order['amt'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <p><a class="btn btn-primary" href="<?= e($base) ?>/index.php">Continue Shopping</a></p>
        </div>
    </main>
</div>
<?php require __DIR__ . "/layout/footer.php"; ?>
