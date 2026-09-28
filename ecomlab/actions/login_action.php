<?php
require_once "../core/core.php";
require_once "../controller/CustomerController.php";

header("Content-Type: application/json");

$email = $_POST['customer_email'] ?? '';
$pass  = $_POST['customer_pass'] ?? '';

if ($email === '' || $pass === '') {
    echo json_encode(["success" => false, "message" => "Email and password required."]);
    exit;
}

$controller = new CustomerController();
$customer = $controller->login($email, $pass);

if ($customer) {
    $_SESSION['customer_id']    = $customer['customer_id'];
    $_SESSION['customer_name']  = $customer['customer_name'];
    $_SESSION['customer_email'] = $customer['customer_email'];
    $_SESSION['user_role']      = $customer['user_role'];
    echo json_encode(["success" => true, "message" => "Login successful."]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid email or password."]);
}