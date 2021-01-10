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
	<title>NetSchool PTHS</title>
</head>
<body>

	<?php require_once '../src/header.php' ?>

	<main>

		<div class="announcements">
			<!-- <h2>Объявления</h2> -->

			<ul>

				<?php

				try {
					$announcements = $db->getAll('SELECT * FROM `announcements` ORDER BY `date`');
				} catch (Exception $e) {
					exit(json_encode(array('message', 'Database request failed')));
				}

				foreach ($announcements as $announcement) {
					?>

					<li class="announcement" announcement_id="<?php echo $announcement['id'] ?>">
						<div class="author">
							<div class="profile_photo">
								<?php

								// TODO

								?>
							</div>
							<div class="name">
								<?php echo $announcement['author'] ?>
							</div>
						</div>
						<div class="content">
							<h4><?php echo $announcement['title'] ?></h4>
							
							<div class="date">
								<?php

								$date = new DateTime($announcement['date']);

								echo $date->format('d.m.Y')

								?>
							</div>
							<article>
								<?php echo $announcement['text'] ?>
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