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
			<?php

			$diary = json_decode($person['diary'], true);
			if (is_null($diary)) {
				?>

				<p>Оценки не загружены</p>

				<?php
			} else {
				?>
				<div class="tabs">
					<div class="tab avg_dinamic">
						<h3>Динамика среднего балла</h3>
						<canvas id="dynamics_canvas"></canvas>
					</div>
				</div>
				<?php
			}
			?>
		</div>

	</main>

	<div class="details"></div>
	
	<div id="data" style="display:none">
	
	<?php
		$diary = json_decode($person['diary'], true);
		$sum_mark_points = [];
		$sum_weight = [];
		foreach ($diary as $day => $tasks) {
			foreach ($tasks as $task_data) {
				$lesson = handle_lesson_name(trim($task_data[0]));
				$date = new DateTime($day);
				$day_number = $date->format("z");
				
				if(!isset($sum_mark_points[$lesson])){
					$sum_mark_points[$lesson] = [];
					$sum_weight[$lesson] = [];
					$sum_mark_points[$lesson][$day_number] = 0;
					$sum_weight[$lesson][$day_number] = 0;
				}
				if($task_data[3]){
					$sum_mark_points[$lesson][$day_number] += end($sum_mark_points[$lesson]) + $task_data[4]*$task_data[3];
					$sum_weight[$lesson][$day_number] += end($sum_weight[$lesson]) + $task_data[3];
				}
			}
		}
		$result = [];
		foreach($sum_mark_points as $lesson => $mark_points_per_lesson){
			foreach($mark_points_per_lesson as $day_number => $_){
				if (!isset($result[$lesson])) $result[$lesson] = [];
				//echo $sum_mark_points[$lesson][$day_number];
				$result[$lesson][$day_number] = $sum_mark_points[$lesson][$day_number]/$sum_weight[$lesson][$day_number];
			}
		}
		echo json_encode($result);
	?>
	
	</div>
 
	<script type="text/javascript" src="/src/event.js" defer></script>
	<script type="text/javascript" src="/src/build/ajax.min.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<script type="text/javascript" src="/src/lib/chart.js" defer></script>
	<script type="text/javascript" src="build/stats.min.js" defer></script>
</body>

</html>