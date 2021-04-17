<?php
require_once __DIR__ . '/ui_error.php';
?>

<div class="menubar">
	<div class="menu_icon_wrapper" title="Меню">
		<?php include_once __DIR__ . '/../files/icons/menu.svg' ?>
	</div>
	<div class="moon_icon_wrapper" title="Тёмная тема">
		<?php include_once __DIR__ . '/../files/icons/moon.svg' ?>
	</div>
</div>

<div class="statusbar">
	<div class="name" title="Ваше имя"><?php echo $person['name']?></div>
	<div class="last_update" title="Время последнего обновления">
		<?php echo date('H', strtotime($person['last_update'])) ?>
		<span>:</span>
		<?php echo date('i', strtotime($person['last_update'])) ?>
	</div>
	<div class="exit_icon" title="Выйти"><?php include_once __DIR__ . '/../files/icons/sign-out.svg' ?></div>
</div>

<div id="ui_alert"></div>
<input type="checkbox" id="side-checkbox" />
<div class="side-panel">
    <label class="side-button-2" for="side-checkbox">+</label>    
    <div class="side-title">Выдвижная панель:</div>
    <p>Информация в панели</p>
</div>
<label class="side-button-1" for="side-checkbox">
	<div class="side-b side-open">Открыть</div>
	<div class="side-b side-close">Закрыть</div>
</label>