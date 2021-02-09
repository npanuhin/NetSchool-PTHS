<?php
require_once __DIR__ . '/../src/config.php';

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
	<?php include_once __DIR__ . '/../src/favicon.php' ?>
	<title>NetSchool PTHS | Объявления</title>
</head>
<body>

	<?php require_once __DIR__ . '/../src/header.php' ?>

	<main>

		<?php
		include_once __DIR__ . '/../src/message_alerts.php';
		require_once __DIR__ . '/../src/menu.php';
		?>

		<div class="announcements">
			<!-- <h2>Объявления</h2> -->

			<ul>

				<?php

				try {
					$announcements = $db->getAll('SELECT * FROM `announcements` ORDER BY `date` DESC');
				} catch (Exception $e) {
					exit(json_encode(array('message', 'Database request failed')));
				}

				function color_from_string($string) {
					global $profile_colors;
					return $profile_colors[hexdec(substr(sha1($string), 0, 10)) % count($profile_colors)];
				}

				foreach ($announcements as $announcement) {
					$announcement_id = $announcement['id'];
					$author = trim($announcement['author']);
					$title = nl2br(trim($announcement['title']));
					$date = new DateTime(trim($announcement['date']));
					$article = preg_replace(
						'/(' . replace_school_class_regex(trim($person['class'])) . ')(?![^<]*>|[^<>]*<\/)/imu',
						'<span class="school_class" title="Это ваш класс (тут нет никакой ссылки)">\1</span>',
						nl2br(trim($announcement['text']))
					);
					?>

					<li class="announcement" announcement_id="<?php echo $announcement_id ?>" title="<?php echo $title ?>">
						<div class="author" title="<?php echo $author ?>">
							<div class="profile_photo" style="<?php if ($author && array_key_exists($author, $profile_photos)) {echo 'background-image: url(\'' . $profile_photos[$author] . '\')';} else {echo 'background: ' . color_from_string($author);} ?>"></div>
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

	<script type="text/javascript" src="/src/event.js" defer></script>
	<script type="text/javascript" src="/src/build/ajax.min.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<!-- <script type="text/javascript" src="build/announcements.min.js" defer></script> -->
</body>

</html>