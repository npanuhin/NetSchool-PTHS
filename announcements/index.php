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
	<link rel="stylesheet" type="text/css" href="build/announcements.min.css">
	<title>NetSchool PTHS | Объявления</title>
</head>
<body>

	<?php require_once '../src/header.php' ?>

	<main>

		<?php
		include_once __DIR__ . '/../src/alerts.php';
		?>

		<div class="menu">
			<ul>
				<li><a href="/">Главная</a></li>
				<li><a href="/timetable">Расписание</a></li>
				<li><a>Задания</a></li>
				<li><a href="/announcements">Объявления</a></li>
				<li><a href="/marks">Оценки</a></li>
				<li><a>Сообщения</a></li>
			</ul>
		</div>

		<div class="announcements">
			<!-- <h2>Объявления</h2> -->

			<ul>

				<?php

				try {
					$announcements = $db->getAll('SELECT * FROM `announcements` ORDER BY `date` DESC');
				} catch (Exception $e) {
					exit(json_encode(array('message', 'Database request failed')));
				}

				foreach ($announcements as $announcement) {
					$announcement_id = $announcement['id'];
					$author = trim($announcement['author']);
					$title = nl2br(trim($announcement['title']));
					$date = new DateTime(trim($announcement['date']));
					$article = nl2br(trim($announcement['text']));
					?>

					<li class="announcement" announcement_id="<?php echo $announcement_id ?>" title="<?php echo $title ?>">
						<div class="author">
							<div class="profile_photo" <?php if ($author && array_key_exists($author, $profile_photos)) echo ' style="background-image: url(\'' . $profile_photos[$author] . '\')"' ?>></div>
							<div class="name">
								<?php echo $author ?>
							</div>
						</div>
						<div class="content">
							<h4><?php echo $title ?></h4>
							
							<div class="date" title="<?php echo ltrim($date->format('d'), '0') . ' ' . $months_genetive[$date->format('m') - 1] ?>">
								<?php

								echo $date->format('d.m.Y')

								?>
							</div>
							<article>
								<?php echo $article ?>
							</article>
						</div>
					</li>

					<?php
				}

				?>
			</ul>
		</div>

	</main>

	<script type="text/javascript" src="/src/event.js"></script>
	<script type="text/javascript" src="/src/ajax.js"></script>
	<script type="text/javascript" src="build/announcements.min.js"></script>
</body>

</html>