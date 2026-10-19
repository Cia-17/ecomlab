<?php
// Cart view: shows what is in the visitor's cart (tied to their IP address).
// It only displays data - the changes are made by the remove / update actions.
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/CartController.php";

$cartController = new CartController();
$ip    = get_ip();
$items = $cartController->getCartItems($ip);
$total = $cartController->getCartTotal($ip);

$page_title   = "Shopping Cart";
$page_scripts = ['validate.js'];
require __DIR__ . "/layout/header.php";
?>
<div class="container page-layout">
    <?php require __DIR__ . "/layout/sidebar.php"; ?>
    <main class="content">
        <h1 class="page-title">Shopping Cart</h1>
        <?php require __DIR__ . "/layout/flash.php"; ?>
        <div id="cart-js-message" class="alert alert-error" role="alert" hidden></div>

        <?php if (empty($items)): ?>
            <p class="empty-state">Your cart is empty.</p>
            <p><a class="btn btn-primary" href="<?= e($base) ?>/index.php">Continue Shopping</a></p>
        <?php else: ?>

            <!-- Two separate forms (Task 12). The inputs and buttons in the table
                 belong to them through the form="..." attribute. -->
            <form id="remove-form" action="<?= e($base) ?>/actions/remove_from_cart_action.php" method="POST"></form>
            <form id="update-form" action="<?= e($base) ?>/actions/update_qty_action.php" method="POST" novalidate></form>

            <div class="card">
                <div class="table-wrap">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Remove</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit price</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="remove[]" value="<?= (int)$item['p_id'] ?>"
                                               form="remove-form" aria-label="Remove <?= e($item['product_title']) ?>">
                                    </td>
                                    <td>
                                        <div class="cart-product">
                                            <?php if (!empty($item['product_image'])): ?>
                                                <img class="thumb" src="<?= e($base) ?>/images/products/<?= e($item['product_image']) ?>" alt="">
                                            <?php else: ?>
                                                <span class="thumb thumb-empty"></span>
                                            <?php endif; ?>
                                            <a href="<?= e($base) ?>/view/single_product.php?pro_id=<?= (int)$item['p_id'] ?>"><?= e($item['product_title']) ?></a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="field">
                                            <input type="text" inputmode="numeric" class="qty-input" name="qty[]"
                                                   value="<?= (int)$item['qty'] ?>" maxlength="2" form="update-form"
                                                   aria-label="Quantity for <?= e($item['product_title']) ?>">
                                            <input type="hidden" name="update[]" value="<?= (int)$item['p_id'] ?>" form="update-form">
                                            <span class="field-error"></span>
                                        </div>
                                    </td>
                                    <td>GHS <?= number_format((float)$item['product_price'], 2) ?></td>
                                    <td class="text-right">GHS <?= number_format((float)$item['product_price'] * (int)$item['qty'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right">Subtotal</td>
                                <td class="text-right">GHS <?= number_format($total, 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="cart-actions">
                    <div>
                        <button type="submit" form="remove-form" class="btn btn-outline">Remove from Cart</button>
                        <button type="submit" form="update-form" class="btn btn-outline">Update Quantity</button>
                    </div>
                    <div>
                        <a class="btn btn-outline" href="<?= e($base) ?>/index.php">Continue Shopping</a>
                        <a class="btn btn-primary" href="<?= e($base) ?>/view/checkout.php">Checkout</a>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </main>
</div>
<?php require __DIR__ . "/layout/footer.php"; ?>
