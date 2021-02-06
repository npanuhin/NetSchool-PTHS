<?php
require_once __DIR__ . '/../src/config.php';

if (!isset($_SESSION['user_id']) || !verifySession()) {
	logout();
	redirect('/login/');
	exit;
}

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
				foreach ($diary as $day => $items) {
					foreach ($items as $item) {
						$lesson = $item[0];
						$task_type = $item[1];
						$task = $item[2];
						$mark_rate = $item[3];
						$mark = $item[4];

						if (!array_key_exists($lesson, $table)) $table[$lesson] = [];
						if (!array_key_exists($day, $table[$lesson])) $table[$lesson][$day] = [];

						$table[$lesson][$day][] = [$mark, $mark_rate, $task, $task_type];

						if (!in_array($day, $all_days)) $all_days[] = $day;
					}
				}

				sort($all_days);
				$all_lessons = array_keys($table);
				sort($all_lessons);
				?>

				<div>
					<ul>
						<li></li>
						<li></li>
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
								$filled_days = [];
								$average_mark = 0;
								$rate_summ = 0;
								foreach ($days as $day => $marks) {
									foreach ($marks as $mark_data) {
										$mark = $mark_data[0];
										$mark_rate = $mark_data[1];

										if (!is_null($mark)) {
											if (is_null($mark_rate)) $mark_rate = $default_mark_rate;
											
											$average_mark += $mark * $mark_rate;
											$rate_summ += $mark_rate;

											if (!in_array($day, $filled_days)) $filled_days[] = $day;
										}
									}
								}
								$all_average_marks[$lesson] = $average_mark / $rate_summ;

								$empty_width = 0;
								foreach ($all_days as $day) {
									if (in_array($day, $filled_days)) {
										if ($empty_width) {
											?>
											<td<?php if ($empty_width > 1) echo ' colspan="' . $empty_width . '"' ?>></td>
											<?php
										}
										?>

										<td class="filled">
											<div>
												<?php
												if (array_key_exists($day, $days)) {
													$marks = $days[$day];

													foreach ($marks as $mark_data) {
														$mark = $mark_data[0];
														$mark_rate = $mark_data[1];
														$task = $mark_data[2];
														$task_type = $mark_data[3];


														if (!is_null($mark)) {
															if (is_null($mark_rate)) $mark_rate = $default_mark_rate;
															?>
															
															<span
																<?php

																if ($mark > $all_average_marks[$lesson]) echo ' class="high"';
																if ($task) echo ' data-name="' . $task . '"';
																if ($task_type) echo ' data-tasktype="' . handle_task_type($task_type) . '"';
																if ($mark_rate) echo ' data-mark_rate="' . $mark_rate . '"';

																?>
															>
																<?php echo $mark ?>
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
						<li></li>
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