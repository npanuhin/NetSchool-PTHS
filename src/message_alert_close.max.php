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

	$messages = json_decode($db -> getRow('SELECT msg_data FROM `messages` WHERE `user_id` = ?i', $person['id'])['msg_data']);
	foreach ($messages as $index => $message) {
		if ($message->id == $name){
			array_splice($messages, $index, 1);
			break;
		}
	}
	$db->query('
		UPDATE netschool.messages
		SET msg_data = ?s
		WHERE `user_id` = ?i',
		json_encode($messages), 
		$person['id']);

} catch (Exception $e) {
	// print_r($e);
	telegram_log("Database request failed\n\n" . $e->getMessage());
	exit(json_encode(array('message', 'Database request failed')));
}

echo 'success';
?>
