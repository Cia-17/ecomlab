<?php
// Shown when a payment could not be confirmed or saved.
require_once __DIR__ . "/../core/core.php";
require_login();

$page_title = "Payment Failed";
require __DIR__ . "/layout/header.php";
?>
<div class="container page-layout">
    <?php require __DIR__ . "/layout/sidebar.php"; ?>
    <main class="content">
        <h1 class="page-title">Payment Failed</h1>
        <?php require __DIR__ . "/layout/flash.php"; ?>
        <p>Your order was not completed. If money left your account, please contact support with your payment reference.</p>
        <p>
            <a class="btn btn-primary" href="<?= e($base) ?>/view/payment.php">Try again</a>
            <a class="btn btn-outline" href="<?= e($base) ?>/view/cart.php">Back to cart</a>
        </p>
    </main>
</div>
<?php require __DIR__ . "/layout/footer.php"; ?>
