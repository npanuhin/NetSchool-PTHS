<div class="menubar">
	<div class="menu_icon_wrapper" title="Меню">
		<?php include_once __DIR__ . '/../files/icons/menu.svg' ?>
	</div>
	<div class="moon_icon_wrapper" title="Night mode">
		<?php include_once __DIR__ . '/../files/icons/moon.svg' ?>
	</div>
</div>

<div class="statusbar">
	<div class="name" title="Ваше имя"><?php echo $person['first_name'] . ' ' . $person['last_name']?></div>
	<div class="last_update" title="Время последнего обновления">
		<?php echo date('H', strtotime($person['last_update'])) ?>
		<span>:</span>
		<?php echo date('i', strtotime($person['last_update'])) ?>
	</div>
	<div class="exit_icon" title="Выйти"><?php include_once __DIR__ . '/../files/icons/sign-out.svg' ?></div>
</div>
