<?php
require_once __DIR__ . "/../core/core.php";

$page_title   = "Login";
$page_scripts = ['validate.js'];
require __DIR__ . "/layout/header.php";
?>
<main class="container auth-wrap">
    <div class="card auth-card">
        <h1>Log in</h1>
        <?php require __DIR__ . "/layout/flash.php"; ?>

        <form id="login-form" action="<?= e(base_url()) ?>/actions/login_action.php" method="POST" novalidate>
            <div class="field">
                <label for="customer_email">Email</label>
                <input type="email" id="customer_email" name="customer_email" maxlength="50" autocomplete="email">
                <span class="field-error"></span>
            </div>
            <div class="field">
                <label for="customer_pass">Password</label>
                <input type="password" id="customer_pass" name="customer_pass" autocomplete="current-password">
                <span class="field-error"></span>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Log in</button>
        </form>

        <p class="auth-switch">New here? <a href="<?= e(base_url()) ?>/view/register.php">Create an account</a></p>
    </div>
</main>
<?php require __DIR__ . "/layout/footer.php"; ?>
