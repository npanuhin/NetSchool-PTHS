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
		<link rel="stylesheet" type="text/css" href="build/home.min.css">
		<title>NetSchool PTHS</title>
	</head>
	<body>

		<header>
			<div class="exit_icon">
				<?php include_once "files/icons/sign-out.svg" ?>
			</div>
			<div class="name"><?php echo $_SESSION["last_name"] . ' ' . $_SESSION["first_name"]?></div>
		</header>

		<script type="text/javascript" src="/src/event.js"></script>
		<script type="text/javascript" src="/src/ajax.js"></script>
		<script type="text/javascript" src="build/home.min.js"></script>
	</body>
</html>