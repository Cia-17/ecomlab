<?php

ob_start();


session_start();

date_default_timezone_set('Africa/Accra');


function redirect($url)
{
    header("Location: $url");
    exit;
}

function is_logged_in()
{
    return isset($_SESSION['customer_id']);
}


function is_admin()
{
    return isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1;
}


function require_login()
{
    if (!is_logged_in()) {
        $_SESSION['error'] = "Please log in to continue.";
        redirect("../view/login.php");
    }
}


function require_admin()
{
    if (!is_admin()) {
        $_SESSION['error'] = "Admin access required.";
        redirect("../index.php");
    }
}


function current_user_id()
{
    return $_SESSION['customer_id'] ?? null;
}


function current_user_name()
{
    return $_SESSION['customer_name'] ?? null;
}