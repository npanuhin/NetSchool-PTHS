<?php
require_once __DIR__ . '/../src/config.php';

if (!isset($_SESSION['user_id']) || !verifySession()) {
	logout();
	redirect('/login/');
	exit;
}

$default_mark = 1;
$default_mark_rate = 10;
?>

<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="build/marks.min.css">
	<?php include_once __DIR__ . '/../src/favicon.php' ?>
	<title>NetSchool PTHS | Оценки</title>
</head>
<body>

	<?php require_once __DIR__ . '/../src/header.php' ?>

	<main>

		<?php
		include_once __DIR__ . '/../src/message_alerts.php';
		require_once __DIR__ . '/../src/menu.php';
		?>

		<div class="marks">
			<h3>Оценки</h3>

			<?php
			$diary = json_decode($person['diary'], true);
			if (is_null($diary)) {
				?>

				<p>Оценки не загружены</p>

				<?php
			} else {

				$all_days = [];
				$all_average_marks = [];
				$table = [];
				foreach ($diary as $day => $tasks) {
					foreach ($tasks as $task_data) {
						$lesson = trim($task_data[0]);
						$task_type = trim($task_data[1]);
						$task = trim($task_data[2]);
						$mark_rate = $task_data[3];
						$mark = $task_data[4];
						$task_expired = $task_data[5];
						$lesson_ext = trim($task_data[6][0]);
						$task_data_ext = $task_data[6][1];

						if (!array_key_exists($lesson, $table)) $table[$lesson] = [];
						if (!array_key_exists($day, $table[$lesson])) $table[$lesson][$day] = [];

						$table[$lesson][$day][] = [$mark, $mark_rate, $task, $task_type, $task_expired, $lesson_ext, $task_data_ext];

						if (!in_array($day, $all_days)) $all_days[] = $day;
					}
				}
				$all_lessons = array_keys($table);

				sort($all_days);
				sort($all_lessons);
				?>

				<div>
					<ul>
						<li></li>
						<li id="scroll_left">
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
					
					<table>
						<tr>
							<?php
							$cur_month = null;
							$width = 0;

							foreach ($all_days as $day) {
								$datetime = new DateTime($day);
								if (is_null($cur_month)) $cur_month = $datetime->format('m');

								if ($datetime->format('m') != $cur_month) {
									?>

									<td colspan="<?php echo $width ?>" title="<?php echo $months[(int)($cur_month - 1)] ?>">
										<span>
											<?php echo $months[(int)($cur_month - 1)] ?>
										</span>
									</td>

									<?php
									$cur_month = $datetime->format('m');
									$width = 0;
								}

								++$width;
							}

							if ($width != 0) {
								?>

								<td colspan="<?php echo $width ?>" title="<?php echo $months[(int)($cur_month - 1)] ?>">
									<span>
										<?php echo $months[(int)($cur_month - 1)] ?>
									</span>
								</td>

								<?php
							}
							?>
						</tr>

						<tr>
							<?php
							foreach ($all_days as $day) {
								$datetime = new DateTime($day);
								?>
								<td title="<?php echo $weekdays[$datetime->format('w')] . ', ' . ltrim($datetime->format('d'), '0') . ' ' . $months_genetive[$datetime->format('m') - 1] ?>">
									<?php echo $datetime->format('d') ?>
									<br>
									<?php echo $weekdays_short[$datetime->format('w')] ?>
								</td>
								<?php
							}
							?>
						</tr>

						<?php
						foreach ($all_lessons as $lesson) {
							$days = $table[$lesson];
							?>
							<tr>

								<?php
								$filled_days_expired = [];
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

											$filled_days_expired[$day] = ($filled_days_expired[$day] | $task_expired);
										}
									}
								}
								$all_average_marks[$lesson] = $average_mark / $rate_summ;

								$empty_width = 0;
								foreach ($all_days as $day) {
									if (array_key_exists($day, $filled_days_expired)) {

										if ($empty_width) {
											?>
											<td<?php if ($empty_width > 1) echo ' colspan="' . $empty_width . '"' ?>></td>
											<?php
										}

										?>

										<td class="filled<?php if ($filled_days_expired[$day]) echo ' expired' ?>">
											<div>
												<?php
												if (array_key_exists($day, $days)) {
													$tasks = $days[$day];

													$task_index = 0;

													for ($task_index = 0; $task_index < count($tasks); ++$task_index) {
														$task_data = $tasks[$task_index];

														$mark = $task_data[0];
														$mark_rate = $task_data[1];
														$task = $task_data[2];
														$task_type = $task_data[3];
														$task_expired = $task_data[4];
														$task_lesson_ext = $task_data[5];
														$task_data_ext = $task_data[6];

														if (!is_null($mark) || $task_expired) {
															if (is_null($mark)) $mark = $default_mark;
															if (is_null($mark_rate)) $mark_rate = $default_mark_rate;
															?>
															
															<span
																<?php

																$classes = [];

																if ($mark > $all_average_marks[$lesson]) $classes[] = 'high';
																if ($task_expired) $classes[] = 'expired';

																if (!empty($classes)) echo ' class="' . implode(' ', $classes) . '"';

																if ($task) echo ' data-name="' . $task . '"';
																// if ($task_lesson_ext) echo ' data-task="' . $task_lesson_ext . '"';
																if ($task_type) echo ' data-tasktype="' . handle_task_type($task_type) . '"';
																if ($mark_rate) echo ' data-mark_rate="' . $mark_rate . '"';
																if ($task_expired) echo ' data-task_expired';

																$ext_data_index = 0;
																foreach ($task_data_ext as $key => $value) {
																	if ($key && $value && !in_array($key, $disabled_task_data_keys)) {
																		// echo ' data-extdata_key' . $ext_data_index . '="' . $key . '"';
																		// echo ' data-extdata_value' . $ext_data_index . '="' . $value . '"';
																		++$ext_data_index;
																	}
																}

																?>

																id="<?php echo $day . '-' . $lesson . '-' . $task_index ?>"
															>

																<?php echo $mark ?>

																<div>
																	<?php

																	if ($task) {
																		?>

																		<h5><?php echo $task ?></h5>

																		<?php
																	}

																	$task_data = [];

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
												}
												?>
											</div>
										</td>

										<?php
										$empty_width = 0;

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

					<ul>
						<li></li>
						<li id="scroll_right">
							<svg viewBox="0 0 40 70" xmlns="http://www.w3.org/2000/svg">
								<title>Листать вперёд</title>
								<path d="M1.43565 31.2376L31.5346 1.1396C33 -0.297211 34.4999 -0.297251 35.5 0.702789C36.5 1.70283 36.5 3.20279 35 4.70292L5 34.7036L35 64.7029C36.5 66.2028 36.5 67.7025 35.5 68.7028C34.4999 69.7031 33 69.7028 31.5338 68.2669L1.43487 38.1685C0.47793 37.2111 0 35.9577 0 34.7036C0 33.4489 0.47886 32.1945 1.43565 31.2376Z"/>
							</svg>
						</li>

						<?php
						foreach ($all_average_marks as $lesson => $average_mark) {
							?>
							<li>
								<?php echo round($average_mark, 2, PHP_ROUND_HALF_DOWN) ?>
							</li>
							<?php
						}
						?>
					</ul>
				</div>

				<div class="details">
					
				</div>

				<?php
			}
			?>
		</div>

	</main>

	<script type="text/javascript" src="/src/event.js" defer></script>
	<script type="text/javascript" src="/src/ajax.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<script type="text/javascript" src="build/marks.min.js" defer></script>
</body>

</html>