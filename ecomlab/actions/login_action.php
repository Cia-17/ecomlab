<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/CustomerController.php";

$back = base_url() . "/view/login.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($back);
}

$email = clean_input($_POST['customer_email'] ?? '');
$pass  = $_POST['customer_pass'] ?? '';        // passwords are never trimmed/stripped

if ($email === '' || $pass === '') {
    $_SESSION['error'] = "Email and password are required.";
    redirect($back);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Please enter a valid email address.";
    redirect($back);
}

$controller = new CustomerController();
$customer = $controller->login($email, $pass);

if (!$customer) {
    $_SESSION['error'] = "Invalid email or password.";
    redirect($back);
}

session_regenerate_id(true);                    // new session id after login
$_SESSION['customer_id']    = $customer['customer_id'];
$_SESSION['customer_name']  = $customer['customer_name'];
$_SESSION['customer_email'] = $customer['customer_email'];
$_SESSION['user_role']      = (int)$customer['user_role'];

redirect(base_url() . "/index.php");
