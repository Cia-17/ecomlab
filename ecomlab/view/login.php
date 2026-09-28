<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>
<body>
	<h1>Customer Login</h1>

	<nav>
		<a href="../index.php">Home</a> |
		<a href="register.php">Register</a>
	</nav>

	<form id="loginForm">
		<div>
			<label>Email</label>
			<input type="text" name="customer_email" id="customer_email">
		</div>
		<div>
			<label>Password</label>
			<input type="password" name="customer_pass" id="customer_pass">
		</div>
		<div>
			<button type="button" onclick="loginCustomer()">Login</button>
		</div>
	</form>

	<p id="formMessage"></p>

	<script src="../js/customer.js"></script>
</body>
</html>