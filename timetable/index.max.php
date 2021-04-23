<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/session.php';

if (!$AUTHORIZED) {
	logout();
	redirect('/login/');
	exit;
}
get_person();
?>

<!DOCTYPE html>
<html lang="ru"<?php if (isset($_COOKIE['dark']) && $_COOKIE['dark']) echo ' class="dark"'?>>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="build/timetable.min.css">
	<?php include_once __DIR__ . '/../src/favicon.html' ?>
	<title>NetSchool PTHS | Расписание</title>
</head>

<body>
	<?php require_once __DIR__ . '/../src/header.php' ?>
	
	<main>
		<?php
		include_once __DIR__ . '/../src/message_alerts.php';
		require_once __DIR__ . '/../src/menu.html';
		?>

		<div class="timetable">
			<?php
			$cur_week = new DatePeriod($MONDAY, DateInterval::createFromDateString('1 day'), $SUNDAY);
			$timetable = json_decode($person['timetable'], true);
			?>


			<div class="zoom_lessons" title="Уроки, которые сегодня (<?php echo ltrim($TODAY->format('d'), '0') . ' ' . $months_genetive[$TODAY->format('m') - 1] ?>) проходят дистанционно на платформе https://zoom.us">
				<h3>Уроки в <img class="zoom-icon" src="/files/icons/zoom_blue.svg" alt="zoom"></h3>
				<div class="details" title="<?php echo ltrim($TODAY->format('d'), '0') . ' ' . $months_genetive[$TODAY->format('m') - 1] ?>">сегодня</div>

				<table></table>
			</div>


			<div class="zoom_courses" title="Спецкурсы, которые сегодня (<?php echo ltrim($TODAY->format('d'), '0') . ' ' . $months_genetive[$TODAY->format('m') - 1] ?>) проходят дистанционно на платформе https://zoom.us">
				<h3>Спецкурсы в <img class="zoom-icon" src="/files/icons/zoom_blue.svg" alt="zoom"></h3>
				<div class="details" title="<?php echo ltrim($TODAY->format('d'), '0') . ' ' . $months_genetive[$TODAY->format('m') - 1] ?>">сегодня</div>

				<table></table>
			</div>


			<div class="holidays" title="Расписание каникул">
				<h3>Каникулы</h3>

				<?php

				$holidays = $db->getAll('SELECT ?n, ?n, ?n FROM `holidays`', 'name', 'start', 'end');

				foreach ($holidays as $holiday) {
					$holiday_start = new DateTime(trim($holiday['start']));
					$holiday_end = new DateTime(trim($holiday['end']));
					?>

					<div class="holiday" title="<?php echo $holiday['name'] . ' каникулы: с ' . ltrim($holiday_start->format('d'), '0') . ' ' . $months_genetive[$holiday_start->format('m') - 1] . ' по ' . ltrim($holiday_end->format('d'), '0') . ' ' . $months_genetive[$holiday_end->format('m') - 1] ?>">

						<h6><?php echo $holiday['name'] ?></h6>

						<?php

						echo ltrim($holiday_start->format('d'), '0') . ' ' . $months_genetive[$holiday_start->format('m') - 1]
							 . ' - ' .
							 ltrim($holiday_end->format('d'), '0') . ' ' . $months_genetive[$holiday_end->format('m') - 1];

						?>
					</div>
					
					<?php
				}
				
				?>
			</div>

		</div>

	</main>

	<?php include_once __DIR__ . '/../src/online_media/online_media.php' ?>

	<script type="text/javascript" src="/src/lib/event.js" defer></script>
	<script type="text/javascript" src="/src/build/ajax.min.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<script type="text/javascript" src="/src/online_media/build/online_media.min.js" defer></script>
	<script type="text/javascript" src="build/timetable.min.js" defer></script>
</body>

</html>
