<?php
// Admin-only list of customers. (It used to be public, which exposed
// every customer's email and phone number to anyone.)
require_once __DIR__ . "/../core/core.php";
require_admin();
require_once __DIR__ . "/../functions/customer_functions.php";

$customers = getAllCustomersList();

$page_title   = "Customers";
$sidebar_mode = 'admin';
require __DIR__ . "/layout/header.php";
?>
<div class="container page-layout">
    <?php require __DIR__ . "/layout/sidebar.php"; ?>
    <main class="content">
        <h1 class="page-title">Customers</h1>
        <?php require __DIR__ . "/layout/flash.php"; ?>

        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Name</th><th>Email</th><th>Country</th><th>City</th><th>Contact</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr><td colspan="6">No customers yet.</td></tr>
                        <?php else: foreach ($customers as $customer): ?>
                            <tr>
                                <td><?= (int)$customer['customer_id'] ?></td>
                                <td><?= e($customer['customer_name']) ?></td>
                                <td><?= e($customer['customer_email']) ?></td>
                                <td><?= e($customer['customer_country']) ?></td>
                                <td><?= e($customer['customer_city']) ?></td>
                                <td><?= e($customer['customer_contact']) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php require __DIR__ . "/layout/footer.php"; ?>
