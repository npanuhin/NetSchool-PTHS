<?php
require_once 'src/config.php';

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

	<?php

	require_once 'src/error.php';

	try {
	    $db = dbConnect();
	} catch (Exception $e) {
	    error('Database connection failed');
	}

	try {
	    $data = $db->getAll('SELECT * FROM `users` WHERE `id` = ?i LIMIT ?i', $_SESSION["user_id"], 2);
	} catch (Exception $e) {
	    exit(json_encode(array('message', 'Database request failed')));
	}

	if (count($data) > 1) error('Please, contact administrator (too many rows)');

	if (count($data) == 0) error('ID not found, please try to login again<br><a href="/src/logout.php">(click to logout)</a>');

	$person = $data[0];
	?>

	<div class="menubar">
		<div class="menu_icon_wrapper" title="Menu">
			<?php include_once 'files/icons/menu.svg' ?>
		</div>
		<!-- <div class="moon_icon_wrapper" title="Night mode">
			<?php // include_once 'files/icons/moon.svg' ?>
		</div> -->
	</div>

	<div class="statusbar">
		<div class="name"><?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']?></div>
		<div class="last_update" title="Время последнего обновления">
			<?php echo date('H', strtotime($person['last_update'])) ?>
			<span>:</span>
			<?php echo date('i', strtotime($person['last_update'])) ?>
		</div>
		<div class="exit_icon" title="Выйти"><?php include_once 'files/icons/sign-out.svg' ?></div>
	</div>

	<main>
		<div class="menu">
			<ul>
				<li><a href="/">Главная</a></li>
				<li><a>Расписание</a></li>
				<li><a>Задания</a></li>
				<li><a>Объявления</a></li>
				<li><a href="/marks">Оценки</a></li>
				<li><a>Сообщения</a></li>
			</ul>
		</div>

		<?php
		try {
		    $announcements = $db->getAll('SELECT * FROM `announcements` ORDER BY `id` LIMIT ?i', 1);
		} catch (Exception $e) {
		    exit(json_encode(array('message', 'Database request failed')));
		}
		$has_announcements = (count($announcements) != 0);
		?>

		<div class="tasks <?php if (!$has_announcements) echo 'wide' ?>">
			<h2>Задания</h2>
			<ul>
				<li>Английский на 04.12.2020</li>
				<li>Физика на 2.10.2020</li>
				<li>Староста: по русскому тоже было дз честно...</li>
			</ul>
		</div>

		<?php
		if ($has_announcements) {
			?>
			<div class="announcements">
				<?php // include_once "files/icons/cross.svg" ?>
				<div class="title"></div>
			</div>
			<?php
		}
		?>

		<div class="timetable">
			<svg id="timetable_previous" viewBox="0 0 40 70" xmlns="http://www.w3.org/2000/svg">
				<path d="M1.48438 31.5346L31.5833 1.43668C33.498 -0.478897 36.6023 -0.478897 38.516 1.43668C40.4299 3.35056 40.4299 6.45469 38.516 8.36841L11.8831 35.0005L38.5152 61.6317C40.4291 63.5463 40.4291 66.6501 38.5152 68.564C36.6013 70.4787 33.4972 70.4787 31.5826 68.564L1.4836 38.4656C0.526657 37.5082 0.0487289 36.2547 0.0487289 35.0007C0.0487289 33.746 0.527588 32.4916 1.48438 31.5346Z"/>
			</svg>

			<?php
			$timetable = json_decode($person['timetable'], true);

			$today = new DateTime('today');
			// echo $today->format('Y');
			$cur_monday = new DateTime('monday this week');
			
			$cur_year = $today->format('Y');
			if ($today->format('m') < 9) --$cur_year;

			$year_begin = new DateTime($cur_year . '-09-01 Monday this week');
			$year_end = new DateTime(($cur_year + 1) . '-05-31 Monday this week tomorrow');

			$week_period = new DatePeriod($year_begin, DateInterval::createFromDateString('1 week'), $year_end);
			foreach ($week_period as $monday) {
				$sunday = new DateTime($monday->format('Y-m-d') . ' Sunday this week');
				?>

				<div class="week <?php echo $monday->format('Y-m-d') ?> <?php if ($monday == $cur_monday) echo 'shown'; ?>">

					<h3 title="Неделя с <?php echo $monday->format('d') . ' ' . $months_genetive[$monday->format('m') - 1] . ' ' . $monday->format('Y')?> по <?php echo $sunday->format('d') . ' ' . $months_genetive[$sunday->format('m') - 1] . ' ' . $sunday->format('Y')?>">
						<?php echo ltrim($monday->format('d'), '0') . ' ' . $months_genetive[$monday->format('m') - 1]?>
						-
						<?php echo ltrim($sunday->format('d'), '0') . ' ' . $months_genetive[$sunday->format('m') - 1]?>
					</h3>

					<?php
					if ( $today->getTimestamp() < $monday->getTimestamp() || $sunday->getTimestamp() < $today->getTimestamp()) {
						?>
						<div class="goto_today">Сегодня &#8594;</div>
						<?php
					}
					?>
					
					<div class="days">
						<?php
						$day_period = new DatePeriod($monday, DateInterval::createFromDateString('1 day'), $sunday);
						$weekday_index = 0;
						foreach ($day_period as $day) {
							?>

							<div class="day <?php echo $day->format('Y-m-d') ?> <?php if ($day == $today) echo 'today' ?>" title="<?php echo $weekdays[$weekday_index] . ', ' . ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1] ?>">

								<h4><?php echo $weekdays[$weekday_index] ?></h4>

								<div class="day_info">
									<?php

									echo ltrim($day->format('d'), '0') . ' ' . $months_genetive[$day->format('m') - 1];

									?>
								</div>

								<?php

								if (array_key_exists($day->format('Y-m-d'), $timetable) && !is_null($timetable[$day->format('Y-m-d')])) {
									?>

									<ul>
										<?php
										foreach ($timetable[$day->format('Y-m-d')] as $item) {

											if (!is_null($item)) {
												$type = $item[0];
												$start_time = new DateTime($item[1][0]);
												$end_time = new DateTime($item[1][1]);
												$name = $item[2];

												if ($type == 'lesson' || $type == 'vacation') {

													preg_match_all('/(.*)\[(\d+)\]/', $name, $match, PREG_PATTERN_ORDER);

													if (trim($match[1][0])) {
														$name = trim($match[1][0]);
													}
													$cabinet = trim($match[2][0]);
													?>

													<li<?php if ($type == 'vacation') echo ' class="vacation"' ?>>
														<a><?php echo $name ?></a>
														<div class="details">
															<h5><?php echo $name ?></h5>
															<!-- Тип: <?php echo $type ?>
															<br> -->
															Начало: <?php echo $start_time->format('Y-m-d H:i') ?>
															<br>
															Конец: <?php echo $end_time->format('Y-m-d H:i') ?>
															<?php
															
															if ($cabinet) {
																?>
																<br>
																Кабинет: <?php echo $cabinet ?>
																<?php
															}
															
															?>
														</div>
													</li>
													
													<?php
												}
											} else {
												?>

												<li>
													<div class="no_lesson"></div>
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
				<path d="M38.5156 38.4654L8.41667 68.5633C6.50201 70.4789 3.39772 70.4789 1.484 68.5633C-0.429887 66.6494 -0.429887 63.5453 1.484 61.6316L28.1169 34.9995L1.48477 8.36834C-0.429113 6.45368 -0.429113 3.34987 1.48477 1.43599C3.39865 -0.478663 6.50279 -0.478663 8.41744 1.43599L38.5164 31.5344C39.4733 32.4918 39.9513 33.7453 39.9513 34.9993C39.9513 36.254 39.4724 37.5084 38.5156 38.4654Z"/>
			</svg>
		</div>
		
	</main>

	<script type="text/javascript" src="/src/event.js"></script>
	<script type="text/javascript" src="/src/ajax.js"></script>
	<script type="text/javascript" src="build/home.min.js"></script>
</body>

</html>