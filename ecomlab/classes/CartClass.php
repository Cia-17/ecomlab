<?php
require_once __DIR__ . "/../core/db_class.php";

// Model for the shopping cart (Tasks 11 and 12).
// Only SQL and plain return values live here: no HTML, no form data,
// no session and no redirects.
//
// The cart table is tied to the visitor's IP address (guests can shop
// without an account), so every method takes the $ip to work on.
class Cart extends Database
{
    // Task 11 ------------------------------------------------------

    // Is this product already in this visitor's cart? Returns true/false.
    public function isInCart($product_id, $ip)
    {
        $sql = "SELECT p_id FROM cart WHERE p_id = ? AND ip_add = ?";
        return $this->fetchOne($sql, [$product_id, $ip]) !== false;
    }

    // Adds one product to the cart. $customer_id is accepted so the method
    // matches the lab sheet, but the cart row is identified by IP (p_id,
    // ip_add, qty), exactly as the sheet's INSERT shows.
    public function addToCart($product_id, $qty, $ip, $customer_id = null)
    {
        $sql = "INSERT INTO cart (p_id, ip_add, qty) VALUES (?, ?, ?)";
        return $this->execute($sql, [$product_id, $ip, $qty]);
    }

    // Every cart row for this IP, joined with the product details.
    public function getCartItems($ip)
    {
        $sql = "SELECT cart.*, products.product_title, products.product_price, products.product_image
                FROM cart
                JOIN products ON cart.p_id = products.product_id
                WHERE cart.ip_add = ?
                ORDER BY products.product_title ASC";
        return $this->fetchAll($sql, [$ip]);
    }

    // Number of different products in the cart. Returns an integer.
    public function getCartCount($ip)
    {
        $sql = "SELECT COUNT(*) AS total FROM cart WHERE ip_add = ?";
        $row = $this->fetchOne($sql, [$ip]);
        return (int)($row['total'] ?? 0);
    }

    // Sum of qty * unit price for the whole cart. Returns a float.
    public function getCartTotal($ip)
    {
        $sql = "SELECT SUM(cart.qty * products.product_price) AS total
                FROM cart
                JOIN products ON cart.p_id = products.product_id
                WHERE cart.ip_add = ?";
        $row = $this->fetchOne($sql, [$ip]);
        return (float)($row['total'] ?? 0);
    }

    // Task 12 ------------------------------------------------------

    public function removeFromCart($product_id, $ip)
    {
        $sql = "DELETE FROM cart WHERE p_id = ? AND ip_add = ?";
        return $this->execute($sql, [$product_id, $ip]);
    }

    // The new quantity must be a whole number of at least 1.
    public function updateQty($product_id, $new_qty, $ip)
    {
        if (!is_numeric($new_qty) || (int)$new_qty < 1) {
            return false;
        }
        $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ?";
        return $this->execute($sql, [(int)$new_qty, $product_id, $ip]);
    }
}
