<?php
require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/session.php';

if (!$AUTHORIZED) {
	logout();
	redirect('/login/');
	exit;
}
get_person();
?>

<!DOCTYPE html>
<html lang="ru"<?php if (!$UI_ERROR && isset($_COOKIE['dark']) && $_COOKIE['dark']) echo ' class="dark"'?>>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="build/home.min.css">
	<?php include_once __DIR__ . '/src/favicon.html' ?>
	<title>NetSchool PTHS</title>
</head>

<body>
	<?php require_once __DIR__ . '/src/header.php' ?>

	<main>
		<?php
		include_once __DIR__ . '/src/message_alerts.php';
		require_once __DIR__ . '/src/menu.html';

		$diary = json_decode($person['diary'], true);
		$tasks = [];

		if (!is_null($diary)) {

			// Expired tasks:
			foreach ($diary as $date => $day_tasks) {

				$lessons_task_index = [];
				foreach ($day_tasks as $task_data) {
					$lesson = $task_data[0];
					$task = $task_data[2];
					$task_expired = $task_data[5];

					$day = new DateTime($date);

					if (!isset($lessons_task_index[$lesson])) $lessons_task_index[$lesson] = 0;

					if ($task_expired && $day >= class_to_diary_period($db, $person['class'])[0]) $tasks[] = [
						'day' => $day,
						'lesson' => $lesson,
						'task_name' => $task,
						'task_index' => $lessons_task_index[$lesson]++,
						'task_expired' => true
					];
				}
			}

			// Tasks tomorrow:
			$lessons_task_index = [];
			foreach ($diary[$SCHOOL_DAY->format('Y-m-d')] as $task_data) {
				$lesson = $task_data[0];
				$task = $task_data[2];

				if (!isset($lessons_task_index[$lesson])) $lessons_task_index[$lesson] = 0;

				$tasks[] = [
					'day' => $SCHOOL_DAY,
					'lesson' => $lesson,
					'task_name' => $task,
					'task_index' => $lessons_task_index[$lesson]++,
					'task_expired' => false
				];
			}
		}

		if (!empty($tasks)) {
			?>
			<div class="tasks" title="Просроченные задания и задания текущего дня">
				<h2>Задания</h2>
				
				<ul>
					<?php
					foreach ($tasks as $task) {
						extract($task);
						?>

						<li
							<?php
							if ($task_expired) {
								?>
								class="expired" title="Задание просрочено, было задано <?php echo format_days_diff(date_diff($TODAY, $day)->format('%r%a')) ?>"
								<?php
							}
							?>
						>
							<?php echo $lesson . ': ' ?>
							
							<span>
								<a href="/diary/#<?php echo $day->format('Y-m-d') . '-' . $lesson . '-' . $task_index ?>"><?php echo $task_name ?></a>
							</span>

							<div><?php echo format_days_diff(date_diff($TODAY, $day)->format('%r%a')) ?></div>
						</li>

						<?php
					}

					?>
				</ul>
			</div>
			<?php
		}
		?>

		<div class="timetable">
			<button id="goto_today" title="Перейти к текущей неделе">Сегодня &#8594;</button>

			<svg id="timetable_previous" viewBox="0 0 40 70" xmlns="http://www.w3.org/2000/svg">
				<title>Предыдущая неделя</title>
				<?php include __DIR__ . '/files/icons/arrow_thick.path.svg' ?>
			</svg>

			<?php
			$timetable = json_decode($person['timetable'], true);

			$week_period = new DatePeriod($TRUE_SCHOOL_YEAR_BEGIN, DateInterval::createFromDateString('1 week'), $TRUE_SCHOOL_YEAR_END);
			foreach ($week_period as $monday) {
				$sunday = new DateTime($monday->format('Y-m-d') . ' Sunday this week');
				$school_week_start = new DateTime($monday->format('Y-m-d') . ' Saturday last week ' . $SCHOOL_DAY_BORDER->format('H:i'));
				$school_week_end = new DateTime($monday->format('Y-m-d') . ' Saturday this week ' . $SCHOOL_DAY_BORDER->format('H:i'));
				?>

				<div class="<?php
					echo $monday->format('Y-m-d');
					if ($school_week_start <= $NOW && $NOW < $school_week_end) echo ' shown displayed';
					if ($monday == $MONDAY) echo ' cur_week';
				?>">

					<h3 title="Неделя с <?php echo $monday->format('d') . ' ' . $months_genetive[$monday->format('m') - 1] . ' ' . $monday->format('Y')?> по <?php echo $sunday->format('d') . ' ' . $months_genetive[$sunday->format('m') - 1] . ' ' . $sunday->format('Y')?>">
						<?php
						echo ltrim($monday->format('d'), '0') . '&nbsp;' . $months_genetive[$monday->format('m') - 1] . '&nbsp;- ' . ltrim($sunday->format('d'), '0') . '&nbsp;' . $months_genetive[$sunday->format('m') - 1]
						?>
					</h3>
					
					<div>
						<?php
						$day_period = new DatePeriod($monday, DateInterval::createFromDateString('1 day'), $sunday);

						foreach ($day_period as $day) {
							?>

							<div class="<?php echo $day->format('Y-m-d'); if ($day == $TODAY) echo ' today' ?>" title="<?php echo $weekdays[get_weekday($day)] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] ?>">

								<?php

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

									<h4><?php echo $weekdays[get_weekday($day)] ?></h4>

									<div class="day_info<?php if ($zoom_day) echo ' zoom_day' ?>">
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

													<li class="no_lesson" title="<?php echo $weekdays[get_weekday($day)] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] . ': нет ' . $lesson_index . '-го урока' ?>"></li>

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

														title="<?php echo $weekdays[get_weekday($day)] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] . ': ' . $lesson_index . ' урок' ?>"
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

												<li class="no_lesson" title="<?php echo $weekdays[get_weekday($day)] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] . ': нет ' . $lesson_index . '-го урока' ?>"></li>

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
						}
						?>
					</div>
				
				</div>
				<?php
			}
			?>

			<svg id="timetable_next" viewBox="0 0 40 70" xmlns="http://www.w3.org/2000/svg">
				<title>Следующая неделя</title>
				<?php include __DIR__ . '/files/icons/arrow_thick.path.svg' ?>
			</svg>
		</div>
		
	</main>

	<div class="details"></div>

	<?php include_once __DIR__ . '/src/online_media/online_media.php' ?>

	<script type="text/javascript" src="/src/lib/event.js" defer></script>
	<script type="text/javascript" src="/src/build/ajax.min.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<script type="text/javascript" src="build/home.min.js" defer></script>
	<script type="text/javascript" src="/src/online_media/build/online_media.min.js" defer></script>
</body>

</html>
