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
			<div class="lessons">
				<div class="article">
					<?php
						$day = $SCHOOL_DAY;
						if (isset($timetable[$day->format('Y-m-d')]) && !is_null($timetable[$day->format('Y-m-d')])) {

							$lessons = [];
							$vacations = [];
							$has_any_lesson = false;
							$has_any_cabinet = false;

							foreach ($timetable[$day->format('Y-m-d')] as $item) {
								if (!is_null($item)) {
									$type = $item[0];
									$name = $item[1];

									if (!is_null($name) && ($type == 'lesson')) {
										$has_any_lesson = true;

										preg_match_all('/(.*)\[(\d+)\]/', $name, $match, PREG_PATTERN_ORDER);

										if (isset($match[2][0]) && trim($match[2][0])) {
											$has_any_cabinet = true;
											break;
										}
									}
								}
							}

							$zoom_day = ($has_any_lesson && !$has_any_cabinet);

							?>
							<h6><?php echo $weekdays[get_weekday($day)] ?></h6>
							<table>
								<tr>
									<th>Урок</th>
									<th>Начало</th>
									<th>Конец</th>
									<th>Кабинет</th>
								</tr>
								<tbody>
									<?php

									$lesson_index = 0;

									foreach ($timetable[$day->format('Y-m-d')] as $item) {
										echo "<tr>";

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

												if (isset($match[1][0]) && trim($match[1][0])) {
													$name = trim($match[1][0]);
												}

												$cabinet = (isset($match[2][0]) ? trim($match[2][0]) : '');

												$start_time = new DateTime($start_time);
												$end_time = new DateTime($end_time);

												$classes = array();
												if ($type == 'vacation') {
													$classes[] = 'vacation';
												}

												if ($type == 'lesson' && $start_time <= $NOW && $NOW <= $end_time) {
													$classes[] = 'cur_lesson';
												}

												if (!empty($classes)) echo ' class="' . implode(' ', $classes) . '"';

												?>

													<td>
														<?php echo handle_lesson_name($name) ?>
													</td>

													<?php

													$details = [];

													// if ($start_time) $details[] = 'Тип: ' . $type;
													echo '<td>', $start_time->format('H:i'),'</td>';
													echo '<td>', $end_time->format('H:i'),'</td>';
													echo '<td>', $cabinet,'</td>';

													?>

											<?php
										}
									} else {
										++$lesson_index;
										?>
										<td colspan="4">>
											<div class="no_lesson" title="<?php echo $weekdays[get_weekday($day)] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] . ': нет ' . $lesson_index . '-го урока' ?>"></div>
										</td>

										<?php
									}
									echo "</tr>";
								}
								?>

							<?php
						} else {
							?>

							<div class="pending">
								<p>Раписание на этот день не загружено</p>
							</div>

							<?php
						}
						?>
						</tbody>
					</table>
				</div>
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
				?>

				<div class="cources" title="Спецкурсы на неделе с <?php echo ltrim($MONDAY->format('d'), '0') . ' ' . $months_genetive[$MONDAY->format('m') - 1] ?> по <?php echo ltrim($SUNDAY->format('d'), '0') . ' ' . $months_genetive[$SUNDAY->format('m') - 1] ?>">
					<h3>Спецкурсы<br><span>на этой неделе</span></h3>
					<div class="table">
					<?php
					$weekday_index = 0;

					foreach ($cur_week as $day) {
						?>
						<div class="day" title="<?php echo $weekdays[$weekday_index] ?>, <?php echo ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] ?>">
							<h3><?php echo $weekdays[$weekday_index] ?></h3>

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

											if (trim($match[1][0])) {
												$name = trim($match[1][0]);
											}
											$cabinet = trim($match[2][0]);

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
						<?php
						++$weekday_index;
					}

					?>
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

					<div class="holiday" title="<?php echo $holiday['name'] ?> каникулы: с <?php echo ltrim($holiday_start->format('d'), '0') . ' ' . $months_genetive[$holiday_start->format('m') - 1] ?> по <?php echo ltrim($holiday_end->format('d'), '0') . ' ' . $months_genetive[$holiday_end->format('m') - 1] ?>">

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
