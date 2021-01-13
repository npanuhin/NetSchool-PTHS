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
		<?php echo nl2br($pro_tip_hover) ?>
		<?php include __DIR__ . '/../files/icons/cross.svg' ?>
	</div>

	<?php
}

?>