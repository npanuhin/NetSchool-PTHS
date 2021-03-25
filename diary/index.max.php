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
	<link rel="stylesheet" type="text/css" href="build/diary.min.css">
	<?php include_once __DIR__ . '/../src/favicon.html' ?>
	<title>NetSchool PTHS | Оценки</title>
</head>

<body>

	<?php require_once __DIR__ . '/../src/header.php' ?>

	<main>

		<?php
		include_once __DIR__ . '/../src/message_alerts.php';
		require_once __DIR__ . '/../src/menu.html';
		?>

		<div class="diary">
			<h3>Дневник</h3>

			<?php

			$diary = json_decode($person['diary'], true);
			if (is_null($diary)) {
				?>

				<p>Оценки не загружены</p>

				<?php
			} else {

				$all_days = [$TODAY->format('Y-m-d')];
				$all_average_marks = [];
				$table = [];
				foreach ($diary as $day => $tasks) {
					foreach ($tasks as $task_data) {
						$lesson = handle_lesson_name(trim($task_data[0]));

						if (!isset($table[$lesson])) $table[$lesson] = [];
						if (!isset($table[$lesson][$day])) $table[$lesson][$day] = [];

						$table[$lesson][$day][] = [
							$task_data[4],          // mark
							$task_data[3],          // mark_rate
							trim($task_data[2]),    // task_name
							trim($task_data[1]),    // task_type
							$task_data[5],          // task_expired
							trim($task_data[6][0]), // teacher
							$task_data[6][1]        // task_data_ext
						];

						if (!in_array($day, $all_days)) $all_days[] = $day;
					}
				}
				$all_lessons = array_keys($table);

				sort($all_days);
				sort($all_lessons);

				$period_start = $person["diary_period_start"];
				$period_end = $person["diary_period_end"];

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

				?>

				<label class="period_start_label" title="Начало периода">
					с
					<input class="period_start" min="<?php echo $TRUE_SCHOOL_YEAR_BEGIN->format('Y-m-d') ?>" max="<?php echo $TRUE_SCHOOL_YEAR_END->format('Y-m-d') ?>" type="date" value="<?php echo $period_start->format('Y-m-d') ?>" data-default="<?php echo class_to_diary_period($db, $person['class'])[0]->format('Y-m-d') ?>">
				</label>

				<label class="period_end_label" title="Конец периода">
					по
					<input class="period_end" min="<?php echo $TRUE_SCHOOL_YEAR_BEGIN->format('Y-m-d') ?>" max="<?php echo $TRUE_SCHOOL_YEAR_END->format('Y-m-d') ?>" type="date" value="<?php echo $period_end->format('Y-m-d') ?>" data-default="<?php echo class_to_diary_period($db, $person['class'])[1]->format('Y-m-d') ?>">
				</label>


				<div class="table_layout">
					<ul class="left_column">
						<li></li>
						<li class="scroll_left">
							<svg viewBox="0 0 40 70" xmlns="http://www.w3.org/2000/svg">
								<title>Листать назад</title>
								<path d="M1.43565 31.2376L31.5346 1.1396C33 -0.297211 34.4999 -0.297251 35.5 0.702789C36.5 1.70283 36.5 3.20279 35 4.70292L5 34.7036L35 64.7029C36.5 66.2028 36.5 67.7025 35.5 68.7028C34.4999 69.7031 33 69.7028 31.5338 68.2669L1.43487 38.1685C0.47793 37.2111 0 35.9577 0 34.7036C0 33.4489 0.47886 32.1945 1.43565 31.2376Z"/>
							</svg>
						</li>

						<?php
						foreach ($all_lessons as $lesson) {
							?>
							<li title="<?php echo $lesson ?>"><?php echo short_lesson_name($lesson) ?></li>
							<?php
						}
						?>
					</ul>

					<div class="scrollbox">
					
						<table>
							<!-- Months -->
							<tr>
								<?php
								$cur_month = null;
								$empty_width = 0;

								foreach ($all_days as $day) {
									$datetime = new DateTime($day);
									if (is_null($cur_month)) $cur_month = $datetime->format('m');

									if ($datetime->format('m') != $cur_month) {
										?>

										<td colspan="<?php echo $empty_width ?>" title="<?php echo $months[(int)($cur_month - 1)] ?>">
											<span>
												<?php echo $months[(int)($cur_month - 1)] ?>
											</span>
										</td>

										<?php
										$cur_month = $datetime->format('m');
										$empty_width = 0;
									}

									++$empty_width;
								}

								if ($empty_width != 0) {
									?>

									<td colspan="<?php echo $empty_width ?>" title="<?php echo $months[(int)($cur_month - 1)] ?>">
										<span>
											<?php echo $months[(int)($cur_month - 1)] ?>
										</span>
									</td>

									<?php
								}
								?>
							</tr>

							<!-- Days -->
							<tr>
								<?php
								foreach ($all_days as $day) {
									$datetime = new DateTime($day);
									?>
									<td<?php if ($day == $TODAY->format('Y-m-d')) echo ' class="today"' ?> title="<?php echo $weekdays[get_weekday($datetime)] . ', ' . ltrim($datetime->format('d'), '0') . ' ' . $months_genetive[get_weekday($datetime)] ?>">
										<?php echo $datetime->format('d') ?>
										<br>
										<?php echo $weekdays_short[get_weekday($datetime)] ?>
									</td>
									<?php
								}
								?>
							</tr>

							<!-- Table -->
							<?php
							foreach ($all_lessons as $lesson) {
								$days = $table[$lesson];
								?>
								<tr>

									<?php
									$days_expired_key = [];
									$average_mark = 0;
									$rate_summ = 0;
									foreach ($days as $day => $marks) {
										foreach ($marks as $mark_data) {
											$mark = $mark_data[0];
											$mark_rate = $mark_data[1];
											$task_expired = $mark_data[4];

											if (!is_null($mark) || $task_expired) {
												if (is_null($mark)) $mark = $default_mark;
												if (is_null($mark_rate)) $mark_rate = $default_mark_rate;
												
												$average_mark += $mark * $mark_rate;
												$rate_summ += $mark_rate;
											}

											$days_expired_key[$day] = isset($days_expired_key[$day]) ? $days_expired_key[$day] | $task_expired : $task_expired;
										}
									}
									$all_average_marks[$lesson] = $average_mark / $rate_summ;

									$empty_width = 0;
									foreach ($all_days as $day) {
										if ((isset($days[$day]) && $days[$day]) || $day == $TODAY->format('Y-m-d')) {

											if ($empty_width) {
												?>
												<td<?php if ($empty_width > 1) echo ' colspan="' . $empty_width . '"' ?>></td>
												<?php
											}
											$empty_width = 0;
											?>

											<td class="<?php echo $day ?> <?php
												if (isset($days[$day]) && $days[$day]) {
													echo ' filled';
													if ($days_expired_key[$day])     echo ' expired';
												}
												if ($day == $TODAY->format('Y-m-d')) echo ' today';
											?>">
												<div>
													<?php
													if (isset($days[$day]) && $days[$day]) {

														$tasks = $days[$day];

														$task_index = 0;

														for ($task_index = 0; $task_index < count($tasks); ++$task_index) {
															$task_data = $tasks[$task_index];

															$mark = $task_data[0];
															$mark_rate = $task_data[1];
															$task = $task_data[2];
															$task_type = $task_data[3];
															$task_expired = $task_data[4];
															$teacher = $task_data[5];
															$task_data_ext = $task_data[6];

															$has_mark = (!is_null($mark) || $task_expired);

															if ($mark_rate) $mark_rate = $default_mark_rate;

															?>
															<span id="<?php echo $day . '-' . $lesson . '-' . $task_index ?>"
																<?php

																if (!is_null($mark) || $task_expired) {

																	// $classes = [];
																	// if ($task_expired) $classes[] = 'expired';
																	// if (!empty($classes)) echo ' class="' . implode(' ', $classes) . '"';

																	if ($task_expired) echo ' class="expired"';

																	if ($mark) {
																		echo ' data-mark="' . $mark . '"';
																	} else {
																		echo ' data-mark="' . $default_mark . '"';
																	}

																	echo ' data-rate="' . $mark_rate . '"';
																}

																?>
															>
																<?php

																if ($mark) {
																	echo $mark;
																} else if ($task_expired) {
																	echo $default_mark;
																} else {
																	echo '-';
																}

																?>

																<div>
																	<?php

																	if ($task) {
																		?>
																		<h5><?php echo $task ?></h5>
																		<?php
																	}

																	$task_data = [];

																	if ($teacher) $task_data[] = 'Учитель: ' . $teacher;
																	if ($task_type) $task_data[] = 'Тип: ' . handle_task_type($task_type);
																	if ($mark_rate) $task_data[] = 'Вес: ' . $mark_rate;

																	$ext_task_data = '';
																	foreach ($task_data_ext as $key => $value) {
																		if ($key && $value && !in_array($key, $disabled_task_data_keys)) {
																			$ext_task_data .= $key . ':<p>' . nl2br($value) . '</p>';
																		}
																	}

																	if ($ext_task_data) $task_data[] = $ext_task_data;

																	echo implode('<br>', $task_data);
																	?>
																</div>
																
															</span>

															<?php
														}
													}
													?>
												</div>
											</td>

											<?php

										} else {
											++$empty_width;
										}
									}

									if ($empty_width) {
										?>
										<td<?php if ($empty_width > 1) echo ' colspan="' . $empty_width . '"' ?>></td>
										<?php
									}
									?>

								</tr>
								<?php
							}
							?>
						</table>

						<div class="period_hidden_left"></div>
						<div class="period_hidden_right"></div>

					</div>

					<ul class="right_column">
						<li></li>
						<li class="scroll_right">
							<svg viewBox="0 0 40 70" xmlns="http://www.w3.org/2000/svg">
								<title>Листать вперёд</title>
								<path d="M1.43565 31.2376L31.5346 1.1396C33 -0.297211 34.4999 -0.297251 35.5 0.702789C36.5 1.70283 36.5 3.20279 35 4.70292L5 34.7036L35 64.7029C36.5 66.2028 36.5 67.7025 35.5 68.7028C34.4999 69.7031 33 69.7028 31.5338 68.2669L1.43487 38.1685C0.47793 37.2111 0 35.9577 0 34.7036C0 33.4489 0.47886 32.1945 1.43565 31.2376Z"/>
							</svg>
						</li>

						<?php
						foreach ($all_average_marks as $lesson => $average_mark) {
							?>
							<li>
								-
								<?php // echo round($average_mark, 2, PHP_ROUND_HALF_DOWN) ?>
							</li>
							<?php
						}
						?>
					</ul>
				</div>

				<?php
			}
			?>
		</div>

	</main>

	<div class="details">
		<?php // include_once __DIR__ . "/../files/icons/link.svg" ?>

		<svg class="link-icon" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
			<title>Копировать ссылку на задание</title>
			<path d="M326.612 185.391c59.747 59.809 58.927 155.698.36 214.59-.11.12-.24.25-.36.37l-67.2 67.2c-59.27 59.27-155.699 59.262-214.96 0-59.27-59.26-59.27-155.7 0-214.96l37.106-37.106c9.84-9.84 26.786-3.3 27.294 10.606.648 17.722 3.826 35.527 9.69 52.721 1.986 5.822.567 12.262-3.783 16.612l-13.087 13.087c-28.026 28.026-28.905 73.66-1.155 101.96 28.024 28.579 74.086 28.749 102.325.51l67.2-67.19c28.191-28.191 28.073-73.757 0-101.83-3.701-3.694-7.429-6.564-10.341-8.569a16.037 16.037 0 0 1-6.947-12.606c-.396-10.567 3.348-21.456 11.698-29.806l21.054-21.055c5.521-5.521 14.182-6.199 20.584-1.731a152.482 152.482 0 0 1 20.522 17.197zM467.547 44.449c-59.261-59.262-155.69-59.27-214.96 0l-67.2 67.2c-.12.12-.25.25-.36.37-58.566 58.892-59.387 154.781.36 214.59a152.454 152.454 0 0 0 20.521 17.196c6.402 4.468 15.064 3.789 20.584-1.731l21.054-21.055c8.35-8.35 12.094-19.239 11.698-29.806a16.037 16.037 0 0 0-6.947-12.606c-2.912-2.005-6.64-4.875-10.341-8.569-28.073-28.073-28.191-73.639 0-101.83l67.2-67.19c28.239-28.239 74.3-28.069 102.325.51 27.75 28.3 26.872 73.934-1.155 101.96l-13.087 13.087c-4.35 4.35-5.769 10.79-3.783 16.612 5.864 17.194 9.042 34.999 9.69 52.721.509 13.906 17.454 20.446 27.294 10.606l37.106-37.106c59.271-59.259 59.271-155.699.001-214.959z"/>
		</svg>
	</div>

	<?php include_once __DIR__ . '/../src/online_media/online_media.php' ?>

	<script type="text/javascript" src="/src/event.js" defer></script>
	<script type="text/javascript" src="/src/build/ajax.min.js" defer></script>
	<script type="text/javascript" src="/src/lib/chart.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<script type="text/javascript" src="build/diary.min.js" defer></script>
	<script type="text/javascript" src="/src/online_media/build/online_media.min.js" defer></script>
</body>

</html>
