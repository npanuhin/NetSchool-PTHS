<?php
require_once '../src/config.php';

if (!isset($_SESSION['user_id']) || !verifySession()) {
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
	<link rel="stylesheet" type="text/css" href="build/marks.min.css">
	<title>NetSchool PTHS</title>
</head>
<body>

	<?php

	require_once '../src/error.php';

	try {
	    $db = dbConnect();
	} catch (Exception $e) {
	    error('Database connection failed');
	}

	try {
	    $data = $db->getAll('SELECT * FROM `users` WHERE `id` = ?i LIMIT ?i', $_SESSION["user_id"], 2);
	} catch (Exception $e) {
	    exit(json_encode(array('message', 'Database request failed')));
	}

	if (count($data) > 1) error('Please, contact administrator (too many rows)');

	if (count($data) == 0) error('ID not found, please try to login again<br><a href="/src/logout.php">(click to logout)</a>');

	$person = $data[0];
	?>

	<div class="menubar">
		<div class="menu_icon_wrapper" title="Menu">
			<?php include_once '../files/icons/menu.svg' ?>
		</div>
		<!-- <div class="moon_icon_wrapper" title="Night mode">
			<?php // include_once 'files/icons/moon.svg' ?>
		</div> -->
	</div>

	<div class="statusbar">
		<div class="name"><?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']?></div>
		<div class="last_update" title="Время последнего обновления">
			<?php echo date('H', strtotime($person['last_update'])) ?>
			<span>:</span>
			<?php echo date('i', strtotime($person['last_update'])) ?>
		</div>
		<div class="exit_icon" title="Выйти"><?php include_once '../files/icons/sign-out.svg' ?></div>
	</div>

	<main>
		
	</main>

	<script type="text/javascript" src="/src/event.js"></script>
	<script type="text/javascript" src="/src/ajax.js"></script>
	<script type="text/javascript" src="build/home.min.js"></script>
</body>

</html>