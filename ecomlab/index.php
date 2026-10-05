<!--
	This is the homepage / entry point of the app.
	It links out to the two customer pages under the view/ folder.
-->

<?php require_once "core/core.php"; ?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/style.css">
	<title>Ecom LAB</title>
</head>
<body>
	<h1>This is ecom lab started</h1>
	<nav>
		<?php if (isset($_SESSION['customer_id'])): ?>
		Welcome <?= htmlspecialchars($_SESSION['customer_name']) ?> |
		<a href="logout.php">Logout</a>
		<?php if (is_admin()): ?>
			| <a href="view/admin/brand.php">Brands</a>
			| <a href="view/admin/category.php">Categories</a>
		<?php endif; ?>
		<?php else: ?>
			<a href="view/register.php">Register</a> |
			<a href="view/login.php">Login</a>
		<?php endif; ?>
	</nav>
</body>
</html>
