<?php
require_once __DIR__ . '/src/config.php';

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
	<?php include_once __DIR__ . '/src/favicon.php' ?>
	<title>NetSchool PTHS</title>
</head>
<body>

	<?php require_once __DIR__ . '/src/header.php' ?>
	
	<main>

		<?php
		try {
			$announcements = $db->getAll('SELECT * FROM `announcements` ORDER BY `date`');
		} catch (Exception $e) {
			exit(json_encode(array('message', 'Database request failed')));
		}
		$has_announcements = (count($announcements) != 0);

		include_once __DIR__ . '/src/message_alerts.php';
		require_once __DIR__ . '/src/menu.php';

		$diary = json_decode($person['diary'], true);

		if (!is_null($diary)) {
			?>

			<div class="tasks <?php if (true || !$has_announcements) echo 'wide' ?>">
				<h2>Задания</h2>
				<ul>
					<?php

					foreach ($diary as $day => $tasks) {

						$lessons_task_index = [];
						foreach ($tasks as $task_data) {
							$lesson = $task_data[0];
							$task_type = $task_data[1];
							$task = $task_data[2];
							$mark_rate = $task_data[3];
							$mark = $task_data[4];
							$task_expired = $task_data[5];

							if (!array_key_exists($lesson, $lessons_task_index)) $lessons_task_index[$lesson] = 0;

							$task_index = $lessons_task_index[$lesson]++;

							if ($task_expired) {
								?>
								<li class="expired" title="Задание просрочено">
									<?php echo $lesson . ': ' ?><span><a href="/marks#<?php echo $day . '-' . $lesson . '-' . $task_index ?>"><?php echo $task ?></a></span>
								</li>
								<?php
							}
						}
					}

					$day = (new DateTime($SCHOOL_DAY->format('Y-m-d')))->format('Y-m-d');
					$lessons_task_index = [];
					foreach ($diary[$day] as $task_data) {
						$lesson = $task_data[0];
						$task_type = $task_data[1];
						$task = $task_data[2];
						$mark_rate = $task_data[3];
						$mark = $task_data[4];
						$task_expired = $task_data[5];

						if (!array_key_exists($lesson, $lessons_task_index)) $lessons_task_index[$lesson] = 0;

						$task_index = $lessons_task_index[$lesson]++;

						?>
						<li>
							<?php echo $lesson . ': ' ?><span><a href="/marks#<?php echo $day . '-' . $lesson . '-' . $task_index ?>"><?php echo $task ?></a></span>
						</li>
						<?php
					}

					?>
				</ul>
			</div>

			<?php
		}

		if (false && $has_announcements) {
			?>
			<div class="announcements">
				<?php // include_once __DIR__ . "/files/icons/cross.svg" ?>
			</div>
			<?php
		}
		?>

		<div class="timetable">
			<svg id="timetable_previous" viewBox="0 0 40 70" xmlns="http://www.w3.org/2000/svg">
				<title>Предыдущая неделя</title>
				<path d="M1.48438 31.5346L31.5833 1.43668C33.498 -0.478897 36.6023 -0.478897 38.516 1.43668C40.4299 3.35056 40.4299 6.45469 38.516 8.36841L11.8831 35.0005L38.5152 61.6317C40.4291 63.5463 40.4291 66.6501 38.5152 68.564C36.6013 70.4787 33.4972 70.4787 31.5826 68.564L1.4836 38.4656C0.526657 37.5082 0.0487289 36.2547 0.0487289 35.0007C0.0487289 33.746 0.527588 32.4916 1.48438 31.5346Z"/>
			</svg>

			<?php
			$timetable = json_decode($person['timetable'], true);

			$week_period = new DatePeriod($TRUE_SCHOOL_YEAR_BEGIN, DateInterval::createFromDateString('1 week'), $TRUE_SCHOOL_YEAR_END);
			foreach ($week_period as $monday) {
				$sunday = new DateTime($monday->format('Y-m-d') . ' Sunday this week');
				$school_week_start = new DateTime($monday->format('Y-m-d') . ' Saturday last week ' . $SCHOOL_DAY_BORDER->format('H:i'));
				$school_week_end = new DateTime($monday->format('Y-m-d') . ' Saturday this week ' . $SCHOOL_DAY_BORDER->format('H:i'));
				?>

				<div class="<?php echo $monday->format('Y-m-d'); if ($school_week_start <= $NOW && $NOW < $school_week_end) echo ' shown'; if ($monday == $MONDAY) echo ' cur_week'; ?>">

					<h3 title="Неделя с <?php echo $monday->format('d') . ' ' . $months_genetive[$monday->format('m') - 1] . ' ' . $monday->format('Y')?> по <?php echo $sunday->format('d') . ' ' . $months_genetive[$sunday->format('m') - 1] . ' ' . $sunday->format('Y')?>">
						<?php echo ltrim($monday->format('d'), '0') . ' ' . $months_genetive[$monday->format('m') - 1]?>
						-
						<?php echo ltrim($sunday->format('d'), '0') . ' ' . $months_genetive[$sunday->format('m') - 1]?>
					</h3>

					<?php
					if ($TODAY->getTimestamp() < $monday->getTimestamp() || $sunday->getTimestamp() < $TODAY->getTimestamp()) {
						?>
						<button>Сегодня &#8594;</button>
						<?php
					}
					?>
					
					<div>
						<?php
						$day_period = new DatePeriod($monday, DateInterval::createFromDateString('1 day'), $sunday);
						$weekday_index = 0;

						foreach ($day_period as $day) {
							?>

							<div class="<?php echo $day->format('Y-m-d'); if ($day == $TODAY) echo ' today' ?>" title="<?php echo $weekdays[$weekday_index] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] ?>">

								<?php

								if (array_key_exists($day->format('Y-m-d'), $timetable) && !is_null($timetable[$day->format('Y-m-d')])) {

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

												if (trim($match[2][0])) {
													$has_any_cabinet = true;
													break;
												}
											}
										}
									}

									$zoom_day = ($has_any_lesson && !$has_any_cabinet);

									?>

									<h4><?php echo $weekdays[$weekday_index] ?></h4>

									<div class="day_info"<?php if ($zoom_day) echo ' style="top: -11.5px"' ?>>
										<?php

										echo ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1];

										if ($zoom_day) {
											echo ', ';
											?>

											<img class="zoom-icon" src="/files/icons/zoom_blue.svg" alt="zoom" title="Этот день ПРЕДПОЛОЖИТЕЛЬНО проходит дистанционно на платформе https://zoom.us">

											<?php
										}

										?>
									</div>

									<ul>
										<?php

										$lesson_index = 0;

										foreach ($timetable[$day->format('Y-m-d')] as $item) {

											if (!is_null($item)) {
												$type = $item[0];
												$name = $item[1];
												$start_time = $item[2];
												$end_time = $item[3];

												if (is_null($name)) {
													++$lesson_index;
													?>

													<li class="no_lesson" title="<?php echo $weekdays[$weekday_index] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] . ': нет ' . $lesson_index . '-го урока' ?>"></li>

													<?php
												} else if ($type == 'lesson' || $type == 'vacation') {

													++$lesson_index;

													preg_match_all('/(.*)\[(\d+)\]/', $name, $match, PREG_PATTERN_ORDER);

													if (trim($match[1][0])) {
														$name = trim($match[1][0]);
													}
													$cabinet = trim($match[2][0]);

													$start_time = new DateTime($start_time);
													$end_time = new DateTime($end_time);
													?>

													<li
														<?php
														$classes = array();
														if ($type == 'vacation') {
															$classes[] = 'vacation';
														}

														if ($type == 'lesson' && $start_time <= $NOW && $NOW <= $end_time) {
															$classes[] = 'cur_lesson';
														}

														if (!empty($classes)) echo ' class="' . implode(' ', $classes) . '"';

														?>

														title="<?php echo $weekdays[$weekday_index] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] . ': ' . $lesson_index . ' урок' ?>"
													>

														<a><?php echo handle_lesson_name($name) ?></a>
														<div>
															<h5<?php if (!is_null($start_time) || !is_null($end_time) || $cabinet) echo ' style="margin-bottom: 7px"' ?>>
																<?php echo handle_lesson_name($name) ?>
															</h5>

															<?php

															$details = [];

															// if ($start_time) $details[] = 'Тип: ' . $type;
															if ($start_time) $details[] = 'Начало: ' . $start_time->format('Y-m-d H:i');
															if ($end_time) $details[] = 'Конец: ' . $end_time->format('Y-m-d H:i');
															if ($cabinet) $details[] = 'Кабинет: ' . $cabinet;

															echo implode('<br>', $details);

															?>
														</div>
													</li>

													<?php
												}
											} else {
												++$lesson_index;
												?>

												<li class="no_lesson" title="<?php echo $weekdays[$weekday_index] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] . ': нет ' . $lesson_index . '-го урока' ?>"></li>

												<?php
											}
										}
										?>
									</ul>

									<?php
								} else {
									?>

									<div class="pending">
										<p>Раписание на этот день не загружено</p>
									</div>

									<?php
								}
								?>
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

			<svg id="timetable_next" viewBox="0 0 40 70" xmlns="http://www.w3.org/2000/svg">
				<title>Следующая неделя</title>
				<path d="M1.48438 31.5346L31.5833 1.43668C33.498 -0.478897 36.6023 -0.478897 38.516 1.43668C40.4299 3.35056 40.4299 6.45469 38.516 8.36841L11.8831 35.0005L38.5152 61.6317C40.4291 63.5463 40.4291 66.6501 38.5152 68.564C36.6013 70.4787 33.4972 70.4787 31.5826 68.564L1.4836 38.4656C0.526657 37.5082 0.0487289 36.2547 0.0487289 35.0007C0.0487289 33.746 0.527588 32.4916 1.48438 31.5346Z"/>
			</svg>

			<div class="details"></div>
		</div>
		
	</main>

	<script type="text/javascript" src="/src/event.js" defer></script>
	<script type="text/javascript" src="/src/build/ajax.min.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<script type="text/javascript" src="build/home.min.js" defer></script>
</body>

</html>