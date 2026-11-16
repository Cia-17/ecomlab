-- Task 13: tables for orders and payments.
-- Safe to run more than once (CREATE TABLE IF NOT EXISTS).
-- Run it in phpMyAdmin (select the shoppn database > SQL tab > paste > Go).

CREATE TABLE IF NOT EXISTS orders (
    order_id     INT AUTO_INCREMENT PRIMARY KEY,
    customer_id  INT NOT NULL,
    invoice_no   INT NOT NULL,
    order_date   DATE NOT NULL,
    order_status VARCHAR(20) NOT NULL DEFAULT 'paid'
);

CREATE TABLE IF NOT EXISTS orderdetails (
    order_id   INT NOT NULL,
    product_id INT NOT NULL,
    qty        INT NOT NULL
);

CREATE TABLE IF NOT EXISTS payment (
    pay_id       INT AUTO_INCREMENT PRIMARY KEY,
    amt          DECIMAL(10,2) NOT NULL,
    customer_id  INT NOT NULL,
    order_id     INT NOT NULL,
    currency     VARCHAR(10) NOT NULL DEFAULT 'GHS',
    payment_date DATE NOT NULL,
    reference    VARCHAR(100) NOT NULL UNIQUE
);

-- ONLY if you ALREADY had a `payment` table from the original shoppn.sql
-- (without a reference column), run this one line as well:
-- ALTER TABLE payment ADD COLUMN reference VARCHAR(100) NULL UNIQUE;
