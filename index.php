<?php
require_once 'src/config.php';

if (!verifySession()) {
	logout();
	redirect('/login/');
	exit;
}

// print_r($_SESSION);

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
		<link rel="stylesheet" type="text/css" href="build/home.min.css">
		<title>NetSchool PTHS</title>
	</head>
	<body>

		<?php 
		include_once 'src/error.html';

		function error($message) {
			?>
			<script type="text/javascript">error("<?php echo $message ?>")</script>
			<?php
			exit;
		}

		$mysqli = dbConnect();

		if (!$mysqli) {
			error("Database connection failed");
		}

		$query = mysqli_query($mysqli, 'SELECT * FROM `users` WHERE `id` = "' . $_SESSION['user_id'] . '" LIMIT 2');

		if (mysqli_num_rows($query) > 1) error("Please, contact administrator (too many rows)");

		if (mysqli_num_rows($query) == 0) {
			error("ID not found, please try login again <a href='/src/logout.php'>(click to logout)</a>");
		}

		$person = mysqli_fetch_assoc($query);
		?>

		<div class="statusbar">
			<div class="name"><?php echo $_SESSION['last_name'] . ' ' . $_SESSION['first_name']?></div>
			<div class="last_update">
				<?php echo date('H:i:s', strtotime($person['last_update'])) ?>
			</div>
			<div class="exit_icon"><?php include_once 'files/icons/sign-out.svg' ?></div>
		</div>

		<main>
			<div class="current_tasks">
				
			</div>
			<?php
			$announcements = mysqli_query($mysqli, 'SELECT * FROM `announcements` ORDER BY `id` LIMIT 1');

			if (mysqli_num_rows($query) != 0) {
				$announcements = mysqli_fetch_assoc($announcements);
				?>
				<!-- <div class="announcements">
					<?php // include_once "files/icons/cross.svg" ?>
					<div class="title"></div>
				</div> -->
				<?php
			}
			?>
		</main>

		<?php
		mysqli_close($mysqli);
		?>

		<script type="text/javascript" src="/src/event.js"></script>
		<script type="text/javascript" src="/src/ajax.js"></script>
		<script type="text/javascript" src="build/home.min.js"></script>
	</body>
</html>