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

			$has_bells = false;
			foreach ($timetable[$TODAY->format('Y-m-d')] as $item) {
				if (!is_null($item)) {
					$type = trim($item[0]);
					if ($type == 'lesson') {
						$has_bells = true;
						break;
					}
				}
			}

			?>

			<div class="float_wrapper">

				<div class="holidays<?php if (!$has_bells) echo ' wide' ?>" title="Расписание каникул">
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

				<?php

				if ($has_bells) {
					?>

					<div class="bells" title="Звонки на сегодня (<?php echo ltrim($TODAY->format('d'), '0') . ' ' . $months_genetive[$TODAY->format('m') - 1] ?>)">
						<h3>Звонки</h3>
						<div class="details" title="<?php echo ltrim($TODAY->format('d'), '0') . ' ' . $months_genetive[$TODAY->format('m') - 1] ?>">на сегодня</div>

						<ul>
							<?php

							$lesson_index = 0;

							foreach ($timetable[$TODAY->format('Y-m-d')] as $item) {
								if (!is_null($item)) {
									$type = trim($item[0]);
									$name = trim($item[1]);
									$start_time = trim($item[2]);
									$end_time = trim($item[3]);

									if ($type == 'lesson') {
										++$lesson_index;
										$start_time = new DateTime($start_time);
										$end_time = new DateTime($end_time);
										?>
										
										<li title="<?php echo $lesson_index ?> урок: с <?php echo $start_time->format('H:i') . ' до ' . $end_time->format('H:i') ?>">
											<?php echo $start_time->format('H:i') . ' - ' . $end_time->format('H:i') ?>
										</li>
										<?php
									}
								}
							}

							?>
							</ul>
					</div>

					<?php
				}
				?>

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
					<h3>Спецкурсы<span>на этой неделе</span></h3>

					<?php
					$weekday_index = 0;

					foreach ($cur_week as $day) {
						?>
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

											if (trim($match[1][0])) {
												$name = trim($match[1][0]);
											}
											$cabinet = trim($match[2][0]);

											?>
											<li title="<?php echo $name ?> (кабинет <?php echo $cabinet ?>)">
												<?php
												// echo $start_time->format('H:i') . ' - ' . $end_time->format('H:i');

												echo $name . ' <span>' . $cabinet . '</span>';
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

				<?php
			}
			?>
		</div>

	</main>

	<script type="text/javascript" src="/src/event.js" defer></script>
	<script type="text/javascript" src="/src/build/ajax.min.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<!-- <script type="text/javascript" src="build/timetable.min.js" defer></script> -->
</body>

</html>