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

		$cur_week = new DatePeriod($MONDAY, DateInterval::createFromDateString('1 day'), $SUNDAY);
		$timetable = json_decode($person['timetable'], true);
		?>

		<div class="timetable">
			<div class="lessons">
				<?php
				$day = $SCHOOL_DAY;
				if (isset($timetable[$day->format('Y-m-d')]) && !is_null($timetable[$day->format('Y-m-d')])) {

					// Currently it's not used, but it could be used if needed:

					// $has_any_lesson = false;
					// $has_any_cabinet = false;

					// foreach ($timetable[$day->format('Y-m-d')] as $item) {
					// 	if (!is_null($item)) {
					// 		$type = $item[0];
					// 		$name = $item[1];

					// 		if (!is_null($name) && ($type == 'lesson')) {
					// 			$has_any_lesson = true;

					// 			preg_match_all('/(.*)\[(\d+)\]/', $name, $match, PREG_PATTERN_ORDER);

					// 			if (isset($match[2][0]) && trim($match[2][0])) {
					// 				$has_any_cabinet = true;
					// 				break;
					// 			}
					// 		}
					// 	}
					// }
					// $zoom_day = ($has_any_lesson && !$has_any_cabinet);
					?>

					<h5><?php echo $weekdays[get_weekday($day)] ?></h5>

					<table>
						<tr>
							<th>Урок</th>
							<th>Начало</th>
							<th>Конец</th>
							<th>Кабинет</th>
						</tr>

						<?php
						$lesson_index = 0;
						foreach ($timetable[$day->format('Y-m-d')] as $item) {
							?>

							<tr>
								<?php
								if (!is_null($item)) {
									$type = $item[0];
									$name = $item[1];
									$start_time = $item[2];
									$end_time = $item[3];

									if (is_null($name)) {
										++$lesson_index;
										?>

										<td colspan="4">
											<div class="no_lesson" title="<?php echo $weekdays[get_weekday($day)] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] . ': нет ' . $lesson_index . '-го урока' ?>">Перерыв</div>
										</td>

										<?php
									} else if ($type == 'lesson' || $type == 'vacation') {
										++$lesson_index;

										preg_match_all('/(.*)\[(\d+)\]/', $name, $match, PREG_PATTERN_ORDER);

										if (isset($match[1][0]) && trim($match[1][0])) $name = trim($match[1][0]);

										$cabinet = (isset($match[2][0]) ? trim($match[2][0]) : '');

										$start_time = new DateTime($start_time);
										$end_time = new DateTime($end_time);

										?>
										<td><?php echo handle_lesson_name($name) ?></td>

										<td><?php echo $start_time->format('H:i') ?></td>
										<td><?php echo $end_time->format('H:i') ?></td>
										<td><?php echo $cabinet ?></td>

										<?php
									}
								}
								?>
							</tr>

							<?php
						}
						?>
					</table>

					<?php
				}
				?>
			</div>

			<div class="zoom_lessons" title="Уроки, которые сегодня (<?php echo ltrim($TODAY->format('d'), '0') . ' ' . $months_genetive[$TODAY->format('m') - 1] ?>) проходят дистанционно на платформе https://zoom.us">

				<h3>Уроки в <img class="zoom-icon" src="/files/icons/zoom_blue.svg" alt="zoom"></h3>
				<div class="details" title="<?php echo ltrim($TODAY->format('d'), '0') . ' ' . $months_genetive[$TODAY->format('m') - 1] ?>">сегодня</div>

				<table></table>
			</div>

			<?php

			$has_cources = false;
			foreach ($cur_week as $day) {
				foreach ($timetable[$TODAY->format('Y-m-d')] as $item) {
					if (!is_null($item)) {
						$type = $item[0];
						$name = $item[1];

						if ($type == 'event' && $name) {
							$has_cources = true;
							break;
						}
					}
				}
				if ($has_cources) break;
			}

			if ($has_cources) {
				$day = $SCHOOL_COURSES_DAY;
				$weekday_index = get_weekday($day);
				?>

				<div class="cources">
					<div class="day" title="<?php echo $weekdays[$weekday_index] ?>, <?php echo ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] ?>">
						<h5><?php echo $weekdays[$weekday_index] ?></h5>

						<ul>
							<?php

							foreach ($timetable[$day->format('Y-m-d')] as $item) {
								if (!is_null($item)) {
									$type = trim($item[0]);
									$name = trim($item[1]);
									// $start_time = trim($item[2]);
									// $end_time = trim($item[3]);

									if ($type == 'event') {
										// $start_time = new DateTime($start_time);
										// $end_time = new DateTime($end_time);

										preg_match_all('/(.*)\[(\d+)\]/', $name, $match, PREG_PATTERN_ORDER);

										if (isset($match[1][0]) && trim($match[1][0])) {
											$name = trim($match[1][0]);
										}

										if (isset($match[2][0]) && trim($match[2][0])) {
											$cabinet = trim($match[2][0]);
										} else {
											$cabinet = '';
										}

										?>
										<li title="<?php echo "{$name} (кабинет {$cabinet} )"?>">
											<?php
											// echo $start_time->format('H:i') . ' - ' . $end_time->format('H:i');

											echo "{$name} <span>{$cabinet}</span>";
											?>
										</li>
										<?php
									}
								}
							}

							?>
						</ul>
					</div>
				</div>

				<?php
			}
			?>

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
