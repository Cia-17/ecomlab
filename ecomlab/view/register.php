<?php
require_once __DIR__ . "/../core/core.php";

$old = pull_old();     // values typed before a server-side error
$countries = [
    'Ghana', 'Nigeria', 'Kenya', 'South Africa', 'Togo', 'Ivory Coast', 'Senegal', 'Cameroon',
    'Ethiopia', 'Egypt', 'Morocco', 'Tanzania', 'Uganda', 'Rwanda', 'Zambia', 'Zimbabwe',
    'United States', 'Canada', 'United Kingdom', 'Ireland', 'France', 'Germany', 'Netherlands',
    'Spain', 'Italy', 'Portugal', 'India', 'China', 'Japan', 'South Korea', 'Singapore',
    'United Arab Emirates', 'Saudi Arabia', 'Brazil', 'Mexico', 'Australia', 'New Zealand', 'Other',
];

$page_title   = "Register";
$page_scripts = ['validate.js'];
require __DIR__ . "/layout/header.php";
?>
<main class="container auth-wrap">
    <div class="card auth-card auth-card-wide">
        <h1>Create your account</h1>
        <?php require __DIR__ . "/layout/flash.php"; ?>

        <form id="register-form" action="<?= e(base_url()) ?>/actions/customer_register_action.php"
              method="POST" enctype="multipart/form-data" novalidate>
            <div class="form-grid">
                <div class="field span-2">
                    <label for="customer_name">Full name</label>
                    <input type="text" id="customer_name" name="customer_name" maxlength="100" autocomplete="name"
                           value="<?= e($old['name'] ?? '') ?>">
                    <span class="field-error"></span>
                </div>
                <div class="field span-2">
                    <label for="customer_email">Email</label>
                    <input type="email" id="customer_email" name="customer_email" maxlength="50" autocomplete="email"
                           value="<?= e($old['email'] ?? '') ?>">
                    <span class="field-error"></span>
                </div>
                <div class="field span-2">
                    <label for="customer_pass">Password</label>
                    <input type="password" id="customer_pass" name="customer_pass" autocomplete="new-password">
                    <span class="hint">At least 8 characters with an uppercase letter, a lowercase letter, a number and a special character.</span>
                    <span class="field-error"></span>
                </div>
                <div class="field">
                    <label for="customer_country">Country</label>
                    <select id="customer_country" name="customer_country">
                        <option value="">Select a country</option>
                        <?php foreach ($countries as $c): ?>
                            <option value="<?= e($c) ?>"<?= (($old['country'] ?? '') === $c) ? ' selected' : '' ?>><?= e($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="field-error"></span>
                </div>
                <div class="field">
                    <label for="customer_city">City</label>
                    <input type="text" id="customer_city" name="customer_city" maxlength="30"
                           value="<?= e($old['city'] ?? '') ?>">
                    <span class="field-error"></span>
                </div>
                <div class="field span-2">
                    <label for="customer_contact">Contact number</label>
                    <input type="text" id="customer_contact" name="customer_contact" maxlength="15" autocomplete="tel"
                           value="<?= e($old['contact'] ?? '') ?>">
                    <span class="field-error"></span>
                </div>
                <div class="field span-2">
                    <label for="customer_image">Profile image (optional)</label>
                    <input type="file" id="customer_image" name="customer_image"
                           accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp">
                    <span class="hint">JPG, PNG, GIF or WEBP, up to 2 MB.</span>
                    <span class="field-error"></span>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Create account</button>
        </form>

        <p class="auth-switch">Already registered? <a href="<?= e(base_url()) ?>/view/login.php">Log in</a></p>
    </div>
</main>
<?php require __DIR__ . "/layout/footer.php"; ?>
