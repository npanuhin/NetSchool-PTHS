<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/session.php';

if (!$AUTHORIZED) {
	logout();
	redirect('/login/');
	exit;
}

$remove_success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['confirm'])) {

	try {
		$db->query('DELETE FROM `users` WHERE `id` = ?i', $person['id']);
		$db->query('DELETE FROM `messages` WHERE `user_id` = ?i', $person['id']);
		logout();

		$remove_success = true;

	} catch (Exception $e) {
		telegram_log("Database connection failed\n\n" . $e->getMessage());
		echo 'Database connection failed';
	}
}
?>


<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php include_once __DIR__ . '/../src/favicon.php' ?>
	<title>NetSchool PTHS | Удалить аккаунт</title>
</head>

<body>
	<?php

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['confirm'])) {

		if ($remove_success) {
			echo 'Ваш аккаунт успешно удалён';
			telegram_log("User deleted\nUsername: {$person['username']}");

		} else {
			echo 'Ошибка при удалении аккаунта';
			telegram_log("User deletion error\nUsername: {$person['username']}");
		}

	} else {
		?>

		<form action="" method="POST">
			<input type="submit" name="confirm" value="Удалить аккаунт">
		</form>

		<?php
	}
	?>
</body>

</html>