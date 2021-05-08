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

			<input id="password" type="password" placeholder="Пароль" title="Ваш пароль NetSchool">

			<input type="checkbox" id="cbx" style="display: none;">
			<label for="cbx">
				<span>
					<svg viewBox="0 0 18 18">
						<path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"/>
						<polyline points="1 9 7 14 15 4"/>
					</svg>
				</span>

				Соглашаюсь <a href="/terms" title="Посмотреть политику конфиденциальности и условия пользования" target="_blank">с политикой конфиденциальности
				<br>
				и условиями пользования</a> сайтом
			</label>

			<input id="submit" type="submit" value="Войти" title="Войти в систему NetSchool PTHS">

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
