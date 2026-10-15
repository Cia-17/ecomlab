<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/CustomerController.php";
require_once __DIR__ . "/../functions/upload_functions.php";

$back = base_url() . "/view/register.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($back);
}

// Sanitise (never trim/strip the password - it must stay exactly as typed)
$name    = clean_input($_POST['customer_name'] ?? '');
$email   = clean_input($_POST['customer_email'] ?? '');
$pass    = $_POST['customer_pass'] ?? '';
$country = clean_input($_POST['customer_country'] ?? '');
$city    = clean_input($_POST['customer_city'] ?? '');
$contact = clean_input($_POST['customer_contact'] ?? '');

// Remember what was typed so the form can be re-filled after an error
$_SESSION['old'] = [
    'name' => $name, 'email' => $email, 'country' => $country,
    'city' => $city, 'contact' => $contact,
];

function fail_register($message, $back)
{
    $_SESSION['error'] = $message;
    redirect($back);
}

// Server-side validation (lengths match the customer table columns)
if ($name === '' || $email === '' || $pass === '' || $country === '' || $city === '' || $contact === '') {
    fail_register("All required fields must be filled.", $back);
}
if (text_length($name) < 2 || text_length($name) > 100) {
    fail_register("Name must be between 2 and 100 characters.", $back);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || text_length($email) > 50) {
    fail_register("Please enter a valid email address (maximum 50 characters).", $back);
}
$passRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>_\-]).{8,}$/';
if (!preg_match($passRegex, $pass)) {
    fail_register("Password must be at least 8 characters and include an uppercase letter, a lowercase letter, a number, and a special character.", $back);
}
if (text_length($country) > 30 || text_length($city) < 2 || text_length($city) > 30) {
    fail_register("Country and city must be 30 characters or fewer.", $back);
}
if (!preg_match('/^[0-9+\-\s]{7,15}$/', $contact)) {
    fail_register("Contact number must be 7 to 15 digits (you may use +, - and spaces).", $back);
}

// Optional profile image - validated on the server
$image = null;
$upload = save_uploaded_image($_FILES['customer_image'] ?? null, __DIR__ . "/../images/customers");
if ($upload['status'] === 'error') {
    fail_register($upload['error'], $back);
}
if ($upload['status'] === 'ok') {
    $image = $upload['filename'];
}

$controller = new CustomerController();
$result = $controller->register([
    'name' => $name, 'email' => $email, 'password' => $pass,
    'country' => $country, 'city' => $city, 'contact' => $contact,
    'image' => $image,
]);

if (!$result['success']) {
    if ($image !== null) {                       // don't leave an orphan file behind
        @unlink(__DIR__ . "/../images/customers/" . $image);
    }
    fail_register($result['error'], $back);
}

unset($_SESSION['old']);
$_SESSION['success'] = "Registration successful. Please log in.";
redirect(base_url() . "/view/login.php");
