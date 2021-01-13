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
	<link rel="stylesheet" type="text/css" href="build/timetable.min.css">
	<title>NetSchool PTHS | Расписание</title>
</head>
<body>

	<?php require_once __DIR__ . '/../src/header.php' ?>

	<main>

		<?php
		include_once __DIR__ . '/../src/message_alerts.php';
		require_once __DIR__ . '/../src/menu.php';
		?>

		<div class="timetable">
			<h3>Расписание</h3>

			<table>
				
			</table>
		</div>

	</main>

	<script type="text/javascript" src="/src/event.js" defer></script>
	<script type="text/javascript" src="/src/ajax.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<!-- <script type="text/javascript" src="build/timetable.min.js" defer></script> -->
</body>

</html>