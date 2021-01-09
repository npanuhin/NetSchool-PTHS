<?php

require_once __DIR__ . '/../src/error.php';

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

<header>

	<div class="menubar">
		<div class="menu_icon_wrapper" title="Menu">
			<?php include_once __DIR__ . '/../files/icons/menu.svg' ?>
		</div>
		<!-- <div class="moon_icon_wrapper" title="Night mode">
			<?php // include_once __DIR__ . '/../files/icons/moon.svg' ?>
		</div> -->
	</div>

	<div class="statusbar">
		<div class="name"><?php echo $person['first_name'] . ' ' . $person['last_name']?></div>
		<div class="last_update" title="Время последнего обновления">
			<?php echo date('H', strtotime($person['last_update'])) ?>
			<span>:</span>
			<?php echo date('i', strtotime($person['last_update'])) ?>
		</div>
		<div class="exit_icon" title="Выйти"><?php include_once __DIR__ . '/../files/icons/sign-out.svg' ?></div>
	</div>

	<div class="menu">
		<ul>
			<li><a href="/">Главная</a></li>
			<li><a>Расписание</a></li>
			<li><a>Задания</a></li>
			<li><a href="/announcements">Объявления</a></li>
			<li><a href="/marks">Оценки</a></li>
			<li><a>Сообщения</a></li>
		</ul>
	</div>
	
</header>