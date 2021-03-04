<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/session.php';

if (!$AUTHORIZED) {
	logout();
	redirect('/login/');
	exit;
}
?>

<!DOCTYPE html>
<html lang="ru"<?php if (isset($_COOKIE['dark']) && $_COOKIE['dark']) echo ' class="dark"'?>>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="build/help.min.css">
	<?php include_once __DIR__ . '/../src/favicon.php' ?>
	<title>NetSchool PTHS | Помощь</title>
</head>
<body>

	<?php require_once __DIR__ . '/../src/header.php' ?>

	<main>

		<?php
		include_once __DIR__ . '/../src/message_alerts.php';
		require_once __DIR__ . '/../src/menu.php';
		?>

		<div class="help">
		
			<div class="help_tile">
			<div class="content">
			<!-- <h2>Помощь</h2> -->
			<li>
				<?php
					echo file_get_contents("help_text.html");
				?>
			</li>
			</div>
			</div>
		</div>
	</main>

	<script type="text/javascript" src="/src/event.js" defer></script>
	<script type="text/javascript" src="/src/build/ajax.min.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<!-- <script type="text/javascript" src="build/announcements.min.js" defer></script> -->
</body>

</html>