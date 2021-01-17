<?php
require_once '../src/config.php';

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
	<link rel="stylesheet" type="text/css" href="build/timetable.min.css">
	<title>NetSchool PTHS | Расписание</title>
</head>
<body>

	<?php require_once __DIR__ . '/../src/header.php' ?>

	<main>

		<?php
		include_once __DIR__ . '/../src/message_alerts.php';
		require_once __DIR__ . '/../src/menu.php';
		?>

		<div class="timetable">

			<?php

			$timetable = json_decode($person['timetable'], true);

			$today = new DateTime('today');

			$has_bells = false;

			foreach ($timetable[$today->format('Y-m-d')] as $item) {
				if (!is_null($item)) {
					$has_bells = true;
					break;
				}
			}


			?>

			<div class="float_wrapper">

				<div class="holidays<?php if (!$has_bells) echo ' wide' ?> " title="Расписание каникул">
					<h3>Каникулы</h3>

					<?php

					$holidays = $db->getAll('SELECT ?n, ?n, ?n FROM `holidays`', 'name', 'start', 'end');

					foreach ($holidays as $holiday) {
						$holiday_start = new DateTime($holiday['start']);
						$holiday_end = new DateTime($holiday['end']);
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

					<div class="bells" title="Звонки на сегодня (<?php echo ltrim($today->format('d'), '0') . ' ' . $months_genetive[$today->format('m') - 1] ?>)">
						<h3>Звонки</h3>
						<div class="details" title="<?php echo ltrim($today->format('d'), '0') . ' ' . $months_genetive[$today->format('m') - 1] ?>">на сегодня</div>

						<ul>
							<?php

							foreach ($timetable[$today->format('Y-m-d')] as $item) {
								if (!is_null($item)) {
									$has_anything = true;

									$type = $item[0];
									$name = $item[1];
									$start_time = $item[2];
									$end_time = $item[3];

									if ($type == 'lesson') {
										$start_time = new DateTime($start_time);
										$end_time = new DateTime($end_time);
										?>
										<li>
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

			<!-- <div class="cources">
				<h3>Спецкурсы</h3>
			</div> -->

		</div>

	</main>

	<script type="text/javascript" src="/src/event.js" defer></script>
	<script type="text/javascript" src="/src/ajax.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<!-- <script type="text/javascript" src="build/timetable.min.js" defer></script> -->
</body>

</html>