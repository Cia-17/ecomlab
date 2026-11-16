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

    // Task 13 ------------------------------------------------------

    // Turns the visitor's cart into a paid order. Everything happens in one
    // transaction: either ALL the steps succeed or NOTHING is saved.
    // Returns the new order_id (or false if the cart is empty).
    public function processPayment($customer_id, $ip, $reference, $amount)
    {
        // Same payment reference already recorded (page refreshed / callback
        // hit twice)? Return that order instead of creating a second one.
        $existing = $this->fetchOne("SELECT order_id FROM payment WHERE reference = ?", [$reference]);
        if ($existing) {
            return (int)$existing['order_id'];
        }

        $items = $this->fetchAll("SELECT p_id, qty FROM cart WHERE ip_add = ?", [$ip]);
        if (empty($items)) {
            return false;
        }

        $pdo = $this->getConnection();
        try {
            $pdo->beginTransaction();

            // 1. invoice number + 2. the order
            $invoice_no = mt_rand(100000, 999999);
            $this->execute(
                "INSERT INTO orders (customer_id, invoice_no, order_date, order_status) VALUES (?, ?, ?, 'paid')",
                [$customer_id, $invoice_no, date('Y-m-d')]
            );
            $order_id = (int)$pdo->lastInsertId();

            // 3. copy the cart rows into orderdetails
            foreach ($items as $item) {
                $this->execute(
                    "INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)",
                    [$order_id, $item['p_id'], $item['qty']]
                );
            }

            // record the payment (the unique reference stops double-recording)
            $this->execute(
                "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date, reference) VALUES (?, ?, ?, 'GHS', ?, ?)",
                [$amount, $customer_id, $order_id, date('Y-m-d'), $reference]
            );

            // 4. empty the cart
            $this->execute("DELETE FROM cart WHERE ip_add = ?", [$ip]);

            $pdo->commit();
            return $order_id;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;   // the Action logs it and shows a friendly message
        }
    }

    // One order with its items. Only returns it if it belongs to this customer.
    // Returns ['order' => row, 'items' => rows], or false if not found.
    public function getOrderById($order_id, $customer_id)
    {
        $order = $this->fetchOne(
            "SELECT o.order_id, o.invoice_no, o.order_date, o.order_status,
                    p.amt, p.currency, p.reference
             FROM orders o
             LEFT JOIN payment p ON p.order_id = o.order_id
             WHERE o.order_id = ? AND o.customer_id = ?",
            [$order_id, $customer_id]
        );
        if (!$order) {
            return false;
        }

        $items = $this->fetchAll(
            "SELECT od.qty, pr.product_title, pr.product_price
             FROM orderdetails od
             JOIN products pr ON od.product_id = pr.product_id
             WHERE od.order_id = ?",
            [$order_id]
        );
        return ['order' => $order, 'items' => $items];
    }
}
