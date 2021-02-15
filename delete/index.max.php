<?php
require_once __DIR__ . '/../src/config.php';

if (!isset($_SESSION['user_id']) || !verifySession()) {
	logout();
	redirect('/login/');
	exit;
}
?>


<!DOCTYPE html>
<html lang="ru"<?php if ($_SESSION['dark']) echo ' class="dark"'?>>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php include_once __DIR__ . '/../src/favicon.php' ?>
	<title>NetSchool PTHS | Удалить аккаунт</title>
</head>
<body>

	<?php

	if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET) && isset($_GET['confirm'])) {


		try {
			$db = dbConnect();
			$db->query('DELETE FROM `users` WHERE `id` = ?i', $_SESSION['user_id']);
			$db->query('DELETE FROM `messages` WHERE `user_id` = ?i', $_SESSION['user_id']);
			logout();

			echo 'Ваш аккаунт успешно удалён';

		} catch (Exception $e) {
			echo 'Database connection failed';
		}

	} else {
		?>

		<form action="/delete" method="GET">
			<input type="submit" name="confirm" value="Удалить аккаунт">
		</form>

		<?php
	}
	?>

</body>

</html>