<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/session.php';

if ($AUTHORIZED) {
	redirect();
	exit;
}
?>

<!DOCTYPE html>
<html lang="ru" class="loaded">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="build/login.min.css">
	<?php include_once __DIR__ . '/../src/favicon.html' ?>
	<title>NetSchool PTHS | Вход</title>
</head>

<body>
	<div class="container">
		<div class="title">NetSchool PTHS</div>

		<form id="login_form">
			<input id="username" type="text" placeholder="Логин" title="Ваш логин NetSchool">
			<br>
			<input id="password" type="password" placeholder="Пароль" title="Ваш пароль NetSchool">
			<br>
			<input type="submit" value="Войти" title="Войти в систему NetSchool PTHS">
		</form>

		<div class="message">
			<p>Загрузка<span>.</span><span>.</span><span>.</span></p>
		</div>
	</div>

	<script type="text/javascript" src="/src/lib/event.js" defer></script>
	<script type="text/javascript" src="/src/build/ajax.min.js" defer></script>
	<script type="text/javascript" src="build/login.min.js" defer></script>
</body>

</html>
