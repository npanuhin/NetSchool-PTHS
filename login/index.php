<?php
require_once "../src/config.php";

if (isset($_SESSION["user_id"])) {
	redirect('/');
	exit();
}

?>

<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="build/login.min.css">
		<title>Login</title>
	</head>
	<body>
		<div class="container">
			<div class="title">NetSchool PTHS</div>
			<form id="login_form">
				<input id="username" type="text" placeholder="Username">
				<input id="password" type="password" placeholder="Password">
				<input id="submit" type="submit" value="Войти">
			</form>
		</div>

		<script type="text/javascript" src="/src/event.js"></script>
		<script type="text/javascript" src="/src/ajax.js"></script>
		<script type="text/javascript" src="login.js"></script>
	</body>
</html>