<?php
require_once __DIR__ . '/config.php';

if (
	$_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST)                ||
	!isset($_SESSION['user_id'])         || !$_SESSION['user_id']         ||
	!isset($_POST['period_start'])       || !trim($_POST['period_start']) ||
	!isset($_POST['period_end'])         || !trim($_POST['period_end'])
) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	redirect();
	exit;
}

header('Content-Type: application/json');

try {
	$period_start = new DateTime(trim($_POST['period_start']));
	$period_end = new DateTime(trim($_POST['period_end']));

	if (is_null($period_start) || is_null($period_end)) {
		telegram_log("InvalidArgumentException (set_diary_period): Wrong date format\nUser ID: {$_SESSION['user_id']}\n\n" . $e->getMessage());
		throw new InvalidArgumentException('Wrong date format');
	}

	$period_start = $period_start->format('Y-m-d');
	$period_end = $period_end->format('Y-m-d');

} catch (Exception $e) {
	// print_r($e);
	telegram_log("InvalidArgumentException (set_diary_period): Wrong date format\nUser ID: {$_SESSION['user_id']}\n\n" . $e->getMessage());
	exit(json_encode(array('message', 'InvalidArgumentException (set_diary_period): Wrong date format')));
}

try {
	$db = dbConnect();
} catch (Exception $e) {
	// print_r($e);
	telegram_log("Database connection failed\nUser ID: {$_SESSION['user_id']}\n\n" . $e->getMessage());
	exit(json_encode(array('message', 'Database connection failed')));
}

set_diary_period($db, $period_start, $period_end);

echo 'success';
?>