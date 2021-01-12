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

	<?php require_once '../src/header.php' ?>

	<main>

		<?php
		include_once __DIR__ . '/../src/alerts.php';
		?>

		<div class="menu">
			<ul>
				<li><a href="/">Главная</a></li>
				<li><a href="/timetable">Расписание</a></li>
				<li><a>Задания</a></li>
				<li><a href="/announcements">Объявления</a></li>
				<li><a href="/marks">Оценки</a></li>
				<li><a>Сообщения</a></li>
			</ul>
		</div>

		<div class="timetable">
			<h3>Расписание</h3>

			<table>
				
			</table>
		</div>

	</main>

	<script type="text/javascript" src="/src/event.js"></script>
	<script type="text/javascript" src="/src/ajax.js"></script>
	<script type="text/javascript" src="build/timetable.min.js"></script>
</body>

</html>