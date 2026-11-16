<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/CartController.php";

// Paystack sends the customer here after they pay: ?ref=XXXX
// We do NOT trust that alone - we ask Paystack's server whether it really
// succeeded, and whether the amount matches this customer's cart.
require_login();

$failed = base_url() . "/view/payment_failed.php";

$reference = $_GET['ref'] ?? '';
if (!preg_match('/^[A-Za-z0-9._=-]{5,100}$/', $reference)) {
    $_SESSION['error'] = "Invalid payment reference.";
    redirect($failed);
}

if (!defined('PAYSTACK_SECRET_KEY')) {
    log_error("PAYSTACK_SECRET_KEY is not set in core/db_cred.php");
    $_SESSION['error'] = "Payment is not configured.";
    redirect($failed);
}

try {
    $cart  = new CartController();
    $ip    = get_ip();
    $total = $cart->getCartTotal($ip);

    // Already recorded? (customer refreshed this page) - processPayment()
    // simply returns the existing order for the same reference.

    // Ask Paystack to verify the transaction
    $ch = curl_init('https://api.paystack.co/transaction/verify/' . rawurlencode($reference));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . PAYSTACK_SECRET_KEY],
        CURLOPT_TIMEOUT        => 30,
    ]);
    $response = curl_exec($ch);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        log_error("Paystack verify failed: " . $curlErr);
        $_SESSION['error'] = "Could not verify your payment. Please contact support with reference $reference.";
        redirect($failed);
    }

    $result = json_decode($response, true);
    $data   = $result['data'] ?? [];

    $paid = !empty($result['status'])
        && ($data['status'] ?? '') === 'success'
        && ($data['currency'] ?? '') === 'GHS'
        && (int)($data['amount'] ?? 0) === (int)round($total * 100);   // pesewas

    if (!$paid) {
        log_error("Payment not accepted. ref=$reference response=" . $response);
        $_SESSION['error'] = "Your payment could not be confirmed.";
        redirect($failed);
    }

    $order_id = $cart->processPayment(current_user_id(), $ip, $reference, $total);
    if (!$order_id) {
        $_SESSION['error'] = "Your cart was empty, so no order was created.";
        redirect($failed);
    }

    redirect(base_url() . "/view/payment_success.php?order_id=" . (int)$order_id);

} catch (Throwable $e) {
    log_error("Payment error: " . $e->getMessage());
    $_SESSION['error'] = "Something went wrong while saving your order. Please contact support with reference $reference.";
    redirect($failed);
}
