<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST) || !isset($_SESSION['user_id']) || !$_SESSION['user_id'] || !isset($_POST['name']) || !$_POST['name']) {
	header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
	redirect();
	exit;
}

header('Content-Type: application/json');

$name = trim($_POST['name']);

try {
	$db = dbConnect();
} catch (Exception $e) {
	print_r($e);
	exit(json_encode(array('message', 'Database connection failed')));
}

try {
	$data = $db->query('UPDATE `messages` SET ?n = NULL WHERE `user_id` = ?i', $name, $_SESSION['user_id']);
	
} catch (Exception $e) {
	print_r($e);
	exit(json_encode(array('message', 'Database request failed')));
}

echo 'success';
?>