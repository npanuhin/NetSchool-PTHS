<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

if (!$AUTHORIZED) {
	logout();
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	redirect();
	exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST) || !isset($_POST['name']) || !$_POST['name']) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	redirect();
	exit;
}
get_person();


$name = trim($_POST['name']);

try {
	$db->query('UPDATE `messages` SET ?n = NULL WHERE `user_id` = ?i', $name, $person['id']);

} catch (Exception $e) {
	// print_r($e);
	telegram_log("Database request failed\n\n" . $e->getMessage());
	exit(json_encode(array('message', 'Database request failed')));
}

echo 'success';
?>
