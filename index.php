<?php
require_once "src/config.php";

if (!verifySession()) {
	logout();
	redirect('/login/');
	exit;
}

// logout();

// print_r($_SESSION);

if (!isset($_SESSION["user_id"]) || !verifySession()) {
	logout();
	redirect('/login/');
	exit;
}
?>


<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Announcements</title>
	</head>
	<body>
		<?php
		echo $_SESSION["last_name"] . " " . $_SESSION["first_name"];
		?>

		<br>

		<input id="logout" type="submit" value="logout">

		<script type="text/javascript" src="/src/event.js"></script>
		<script type="text/javascript" src="/src/ajax.js"></script>
		<script type="text/javascript" src="main.js"></script>
	</body>
</html>