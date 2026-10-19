<?php
// core.php - included at the top of every page.
// Starts the session and holds the small helpers shared by the whole app.
// No SQL, no HTML, no business logic in here.

ob_start();

// Safer session cookie (JavaScript cannot read it). Only on PHP 7.3+.
if (PHP_VERSION_ID >= 70300 && session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Africa/Accra');


/* ------------------------------------------------------------------
   URLs
------------------------------------------------------------------- */

// Returns the web path of the project folder, e.g. "/~student/shoppn"
// (or "" if the project sits at the web root). It works out the answer
// from the script that is running, so links and redirects are correct
// from ANY depth: index.php, view/*.php, view/admin/*.php, actions/*.php.
//
// If the auto-detection ever gets it wrong on your server, add this line
// to core/db_cred.php:   define('BASE_URL', '/your/folder');
function base_url()
{
    if (defined('BASE_URL')) {
        return rtrim(BASE_URL, '/');
    }

    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $root   = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $script = str_replace('\\', '/', (string)realpath($_SERVER['SCRIPT_FILENAME'] ?? ''));
    $name   = $_SERVER['SCRIPT_NAME'] ?? '';

    if ($script !== '' && strpos($script, $root) === 0) {
        $relative = substr($script, strlen($root));            // e.g. /view/admin/brand.php
        $base = rtrim(substr($name, 0, strlen($name) - strlen($relative)), '/');
    } else {
        $base = '';
    }
    return $base;
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

// Where to send the visitor "back to" after an action (e.g. Add to Cart).
// Uses the page they came from, but ONLY if it is on this same site - the
// Referer header is sent by the browser and can be anything, so an outside
// address would let someone bounce visitors to another website.
function safe_back($default)
{
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ($ref === '') {
        return $default;
    }

    $host = parse_url($ref, PHP_URL_HOST);
    $port = parse_url($ref, PHP_URL_PORT);
    $path = (string)parse_url($ref, PHP_URL_PATH);
    $here = $_SERVER['HTTP_HOST'] ?? '';

    if ($host && strcasecmp($host . ($port ? ':' . $port : ''), $here) === 0
        && strpos($path, '/actions/') === false) {
        return $ref;
    }
    return $default;
}


/* ------------------------------------------------------------------
   Request / input helpers
------------------------------------------------------------------- */

// Client IP (used by the cart in a later task). REMOTE_ADDR is the only
// value the visitor cannot fake, so that is the one we trust.
function get_ip()
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// Server-side sanitising for text fields: trim + strip HTML tags.
// Do NOT use this on passwords.
function clean_input($value)
{
    return trim(strip_tags((string)$value));
}

// Length in characters (falls back to bytes if mbstring is not installed).
function text_length($value)
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

// Escape a value for safe output inside HTML.
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Form values saved by an action so the form can be re-filled after an
// error. Reading them also clears them, so they only show once.
function pull_old()
{
    $old = $_SESSION['old'] ?? [];
    unset($_SESSION['old']);
    return $old;
}


/* ------------------------------------------------------------------
   Error logger  ->  error/error.log
------------------------------------------------------------------- */

function log_error($message)
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    $file = __DIR__ . '/../error/error.log';

    // If the log file cannot be written, fall back to PHP's own log.
    if (!@error_log($line, 3, $file)) {
        error_log($message);
    }
}


/* ------------------------------------------------------------------
   Login state and access control
------------------------------------------------------------------- */

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
        redirect(base_url() . "/view/login.php");
    }
}

// Call this at the very top of every admin page / action, before any output.
function require_admin()
{
    if (!is_admin()) {
        $_SESSION['error'] = "Admin access required.";
        redirect(base_url() . "/index.php");
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
