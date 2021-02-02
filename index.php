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
		?>

		<div class="tasks <?php if (!$has_announcements || true) echo 'wide' ?>">
			<h2>Задания</h2>
			<ul>
				<li>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Voluptate maxime sapiente eos ex fugit odit omnis blanditiis totam, pariatur voluptatem vero animi quam provident ullam sunt, architecto explicabo inventore, veritatis.</li>
				<!-- <li>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Voluptates odio nihil optio aut alias voluptatum sequi libero magnam quia cupiditate. Velit alias illo, enim sed eos laboriosam quas perferendis natus?</li>
				<li>Lorem ipsum dolor sit amet consectetur adipisicing elit. Est natus unde illo aliquam nam. Officia libero nemo ducimus, rerum officiis quisquam, eaque aut delectus. Suscipit repudiandae iste, at. Ex, explicabo?</li> -->
			</ul>
		</div>

		<?php
		if ($has_announcements) {
			?>
			<div class="announcements">
				<?php // include_once __DIR__ . "/files/icons/cross.svg" ?>
				<div class="title"></div>
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

			$today = new DateTime('today');
			$tomorrow = new DateTime('tomorrow');
			
			$cur_datetime = new DateTime('now');

			// echo $today->format('Y');
			$cur_monday = new DateTime('monday this week');
			
			$cur_year = $today->format('Y');
			if ($today->format('m') < 9) --$cur_year;

			$year_begin = new DateTime($cur_year . '-09-01 monday this week');
			$year_end = new DateTime(($cur_year + 1) . '-05-31 monday this week next week');

			$week_period = new DatePeriod($year_begin, DateInterval::createFromDateString('1 week'), $year_end);
			foreach ($week_period as $monday) {
				$sunday = new DateTime($monday->format('Y-m-d') . ' Sunday this week');
				$school_week_start = new DateTime($monday->format('Y-m-d') . ' Saturday last week 15:00');
				$school_week_end = new DateTime($monday->format('Y-m-d') . ' Saturday this week 14:59');
				?>

				<div class="<?php echo $monday->format('Y-m-d'); if ($school_week_start <= $cur_datetime && $cur_datetime < $school_week_end) echo ' shown'; if ($monday == $cur_monday) echo ' cur_week'; ?>">

					<h3 title="Неделя с <?php echo $monday->format('d') . ' ' . $months_genetive[$monday->format('m') - 1] . ' ' . $monday->format('Y')?> по <?php echo $sunday->format('d') . ' ' . $months_genetive[$sunday->format('m') - 1] . ' ' . $sunday->format('Y')?>">
						<?php echo ltrim($monday->format('d'), '0') . ' ' . $months_genetive[$monday->format('m') - 1]?>
						-
						<?php echo ltrim($sunday->format('d'), '0') . ' ' . $months_genetive[$sunday->format('m') - 1]?>
					</h3>

					<?php
					if ( $today->getTimestamp() < $monday->getTimestamp() || $sunday->getTimestamp() < $today->getTimestamp()) {
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

							<div class="<?php echo $day->format('Y-m-d'); if ($day == $today) echo ' today' ?>" title="<?php echo $weekdays[$weekday_index] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] ?>">

								<?php

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

								<?php

								if (array_key_exists($day->format('Y-m-d'), $timetable) && !is_null($timetable[$day->format('Y-m-d')])) {
									?>

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

													<li class="no_lesson">
														<div></div>
													</li>

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
													$cur_time = new DateTime("now");
													?>

													<li
														<?php
														$obj_classes = array();
														if ($type == 'vacation') {
															array_push($obj_classes, 'vacation');
														}

														if (
															$type == 'lesson' &&
															$start_time->getTimestamp() <= $cur_time->getTimestamp() &&
															$cur_time->getTimestamp() <= $end_time->getTimestamp()
														) {
															array_push($obj_classes, 'cur_lesson');
														}

														if (!empty($obj_classes)) echo ' class="' . implode(' ', $obj_classes) . '"';

														?>

														title="<?php echo $weekdays[$weekday_index] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] . ': ' . $lesson_index . ' урок' ?>"
													>

														<a><?php echo handle_lesson_name($name) ?></a>
														<div class="details">
															<h5<?php if (!is_null($start_time) || !is_null($end_time) || $cabinet) echo ' style="margin-bottom: 7px"' ?>>
																<?php echo handle_lesson_name($name) ?>
															</h5>
															<?php
															// echo 'Тип: ' . $type . '<br>';

															if ($start_time) echo 'Начало: ' . $start_time->format('Y-m-d H:i') . '<br>';
															if ($end_time) echo 'Конец: ' . $end_time->format('Y-m-d H:i') . '<br>';
															if ($cabinet) echo 'Кабинет: ' . $cabinet . '<br>';
															
															?>
														</div>
													</li>
													
													<?php
												}
											} else {
												?>

												++$lesson_index;

												<li class="no_lesson">
													<div></div>
												</li>

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
				<path d="M38.5156 38.4654L8.41667 68.5633C6.50201 70.4789 3.39772 70.4789 1.484 68.5633C-0.429887 66.6494 -0.429887 63.5453 1.484 61.6316L28.1169 34.9995L1.48477 8.36834C-0.429113 6.45368 -0.429113 3.34987 1.48477 1.43599C3.39865 -0.478663 6.50279 -0.478663 8.41744 1.43599L38.5164 31.5344C39.4733 32.4918 39.9513 33.7453 39.9513 34.9993C39.9513 36.254 39.4724 37.5084 38.5156 38.4654Z"/>
			</svg>
		</div>
		
	</main>

	<script type="text/javascript" src="/src/event.js" defer></script>
	<script type="text/javascript" src="/src/ajax.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<script type="text/javascript" src="build/home.min.js" defer></script>
</body>

</html>