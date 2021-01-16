<?php

// Insert if not exists
$db->query('
		INSERT INTO `messages` (`user_id`, `pro_tip_hover`)
		SELECT * FROM (
			SELECT
			?i AS `user_id`,
			?s AS `pro_tip_hover`
		) AS tmp
		WHERE NOT EXISTS (
		    SELECT 1 FROM `messages` WHERE `user_id` = ?i
		) LIMIT 1;
	',
	$_SESSION['user_id'],
	'<p>ProTip!</p> Наведите на элемент, чтобы увидеть дополнительную информацию.',
	$_SESSION['user_id']
);

// Get the row
$pro_tip_hover = $db->getOne('SELECT `pro_tip_hover` FROM `messages` WHERE `user_id` = ?i', $_SESSION['user_id']);

if ($pro_tip_hover) {
	?>

	<div class="message_alert" id="message_alert_pro_tip_hover">
		<?php

		echo nl2br($pro_tip_hover);
		// include __DIR__ . '/../files/icons/cross.svg';
		
		?>

		<svg class="cross-icon" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
			<title>Закрыть</title>
			<path d="M13.3 10.0006L19.4873 3.81337C20.1709 3.12974 20.1709 2.02141 19.4873 1.33885L18.6624 0.514004C17.9786 -0.169839 16.8703 -0.169839 16.1877 0.514004L10.0006 6.70106L3.81337 0.512722C3.12974 -0.170907 2.02141 -0.170907 1.33885 0.512722L0.512722 1.33756C-0.170907 2.02141 -0.170907 3.12974 0.512722 3.8123L6.70106 10.0006L0.514004 16.1877C-0.169839 16.8715 -0.169839 17.9799 0.514004 18.6624L1.33885 19.4873C2.02247 20.1709 3.13081 20.1709 3.81337 19.4873L10.0006 13.3L16.1877 19.4873C16.8715 20.1709 17.9799 20.1709 18.6624 19.4873L19.4873 18.6624C20.1709 17.9786 20.1709 16.8703 19.4873 16.1877L13.3 10.0006Z" />
		</svg>
	</div>

	<?php
}

?>