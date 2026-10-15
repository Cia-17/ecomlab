<?php

// Bring in the Customer model class
require_once __DIR__ . "/../classes/CustomerClass.php";

// The controller sits between the "outside world" (actions/functions/views)
// and the model (Customer). Its job is to receive plain data, pass it to
// the model, and hand back whatever the model returns. This keeps the
// model focused on the database, and keeps things like forms/JSON out of
// the model entirely.
class CustomerController
{
    // Holds the Customer instance this controller talks to.
    private $customer;

    // Runs automatically when `new CustomerController()` is called.
    // Creates one Customer instance (and therefore one database
    // connection, since Customer extends Database) for this controller
    // to reuse across its methods.
    public function __construct()
    {
        $this->customer = new Customer();
    }

    // Insert a new customer. This just forwards the data straight to the
    // model's insertCustomer() method - a controller method usually stays
    // this thin unless extra logic (validation, formatting, etc.) belongs
    // here instead of in the action file.
    public function insert($name, $email, $pass, $country, $city, $contact, $image, $role)
    {
        return $this->customer->insertCustomer($name, $email, $pass, $country, $city, $contact, $image, $role);
    }

    // Get the full list of customers from the model.
    public function selectAll()
    {
        return $this->customer->getAllCustomers();
    }


    // Task 3: register a new customer. $data keys: name, email, password,
    // country, city, contact, image. Returns ['success' => true] or
    // ['success' => false, 'error' => '...'].
    public function register($data)
    {
        if ($this->customer->emailExists($data['email'])) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        // Hash BEFORE it reaches the model - plain text is never stored.
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);

        try {
            $ok = $this->customer->insertCustomer(
                $data['name'], $data['email'], $hash,
                $data['country'], $data['city'], $data['contact'],
                $data['image'], 2   // 2 = customer, 1 = admin
            );
        } catch (PDOException $e) {
            if (function_exists('log_error')) log_error($e->getMessage());
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }

        return $ok
            ? ['success' => true]
            : ['success' => false, 'error' => 'Registration failed. Please try again.'];
    }

    public function login($email, $pass)
    {
    return $this->customer->login($email, $pass);
    }

    // To keep building this app, add one method here for every new
    // Customer model method you create (e.g. update(), delete(), findByEmail()).
}
