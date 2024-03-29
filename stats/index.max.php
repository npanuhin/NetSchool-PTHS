<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/session.php';

if (!$AUTHORIZED) {
	logout();
	redirect('/login/');
	exit;
}
get_person();

$default_mark = 1;
$default_mark_rate = 10;
?>

<!DOCTYPE html>
<html lang="ru"<?php if (isset($_COOKIE['dark']) && $_COOKIE['dark']) echo ' class="dark"'?>>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="build/stats.min.css">
	<?php include_once __DIR__ . '/../src/favicon.html' ?>
	<title>NetSchool PTHS | Статистика</title>
</head>

<body>
	<?php require_once __DIR__ . '/../src/header.php' ?>

	<main>
		<?php
		include_once __DIR__ . '/../src/message_alerts.php';
		require_once __DIR__ . '/../src/menu.html';
		?>

		<div class="stats">

			<div class="tabs">
			<?php

			$diary = json_decode($person['diary'], true);
			if (is_null($diary)) {
				?>

				<h3>Оценки не загружены. Подождите немного (~2-15 min).</h3>

				<?php
			}
			else {
				?>
				<div class="tab avg_dinamic">

					<h3>Динамика среднего балла</h3>
					<canvas id="dynamics_canvas"></canvas>
				</div>
				<?php
			}
			?>
			</div>


			<div class="toggle-button-cover">
			Выбор предмета:
				<div class="button r" id="button-3">
				  <input type="checkbox" class="checkbox" id="Select_one">
				  <div class="knobs"></div>
				  <div class="layer"></div>
				</div>
			</div>
		</div>

	</main>

	<div class="details"></div>

	<div id="data" style="display:none">
		<?php

		$diary = json_decode($person['diary'], true);
		if (!is_null($diary)) {
			$period_start = $person['diary_period_start'];
			$period_end = $person['diary_period_end'];

			if (is_null($period_start) || is_null($period_end)) {

				if (is_null($period_start)) $period_start = class_to_diary_period($db, $person['class'])[0];
				if (is_null($period_end)) $period_end = class_to_diary_period($db, $person['class'])[1];

				if (!($period_start instanceof DateTime)) $period_start = new DateTime($period_start);
				if (!($period_end instanceof DateTime)) $period_end = new DateTime($period_end);

				set_diary_period($db, $person, $period_start->format('Y-m-d'), $period_end->format('Y-m-d'));

			} else {

				if (!($period_start instanceof DateTime)) $period_start = new DateTime($period_start);
				if (!($period_end instanceof DateTime)) $period_end = new DateTime($period_end);
			}

			/*
				$task_data[3] — weight
				$task_data[4] — the mark itself
				$task_data[5] — whether it is expired
			*/

			$sum_mark_points = [];
			$sum_weight = [];
			$source_marks = [];
			foreach ($diary as $day => $tasks) {
				foreach ($tasks as $task_data) {
					$lesson = handle_lesson_name(trim($task_data[0]));
					$date = new DateTime($day);
					if (($date > $period_end) || ($date < $period_start)) {
						continue;
					}
					$day_number = $date->format('Y-m-d'); // ISO format

					if (!isset($sum_mark_points[$lesson])) {
						$sum_mark_points[$lesson] = [];
						$sum_weight[$lesson] = [];
						$sum_mark_points[$lesson][$day_number] = 0;
						$sum_weight[$lesson][$day_number] = 0;
						$source_marks[$lesson] = [];
					}
					if (!is_null($task_data[4]) || $task_data[5]) {
						if (is_null($task_data[4])) $task_data[4] = $default_mark;
						// var_dump($lesson, end($sum_weight[$lesson]), $task_data[3]);
						$sum_mark_points[$lesson][$day_number] = end($sum_mark_points[$lesson]) + $task_data[4]*$task_data[3];
						$sum_weight[$lesson][$day_number] = end($sum_weight[$lesson]) + $task_data[3];
						$source_marks[$lesson][] = [$task_data[4], $day_number];
					}
				}
			}

			$result = [];
			foreach ($sum_mark_points as $lesson => $mark_points_per_lesson) {
				foreach($mark_points_per_lesson as $day_number => $_) {

					if (!isset($result[$lesson])) $result[$lesson] = [];
					if ($sum_weight[$lesson][$day_number]) {
						$result[$lesson][$day_number] = $sum_mark_points[$lesson][$day_number]/$sum_weight[$lesson][$day_number];
					}
				}
			}
			echo json_encode([$result, $source_marks]);
		}



		?>
	</div>

	<?php include_once __DIR__ . '/../src/online_media/online_media.php' ?>

	<script type="text/javascript" src="/src/lib/event.js" defer></script>
	<script type="text/javascript" src="/src/build/ajax.min.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>

	<script type="text/javascript" src="/src/lib/chart.js" defer></script>
	<script type="text/javascript" src="build/stats.min.js" defer></script>

	<script type="text/javascript" src="/src/online_media/build/online_media.min.js" defer></script>
</body>

</html>
