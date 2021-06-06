<?php
require_once __DIR__ . '/config.php';

// Creates $AUTHORIZED flag, $db and $person variables

$AUTHORIZED = isset($_COOKIE['session']);

$db = null;
$person = null;

if ($AUTHORIZED) {
	try {
		$db = dbConnect();
	} catch (Exception $e) {
		// print_r($e);
		$AUTHORIZED = false;
		telegram_log("Database connection failed\n\n" . $e->getMessage());
		$UI_ERROR = 'Database connection failed';
	}
}

if ($AUTHORIZED) {
	try {
		$data = $db->getAll('SELECT 1 FROM `users` WHERE MD5(CONCAT(`id`, `username`, `password`)) = ?s LIMIT ?i', $_COOKIE['session'], 2);
	} catch (Exception $e) {
		// print_r($e);
		$AUTHORIZED = false;
		telegram_log("Database request failed\n\n" . $e->getMessage());
		$UI_ERROR = 'Database request failed (auth)';
	}

	if (count($data) > 1) {
		$AUTHORIZED = false;
		telegram_log("Too many rows");
		$UI_ERROR = 'Please, contact administrator (too many rows)';
	}

	if (count($data) == 0) {
		$AUTHORIZED = false;
		telegram_log("Session not found");
		logout();
		redirect();
		exit;
	}
}

function get_person() {
	global $db, $AUTHORIZED, $person, $NOW;

	if ($AUTHORIZED) {
		try {
			$data = $db->getAll('SELECT * FROM `users` WHERE MD5(CONCAT(`id`, `username`, `password`)) = ?s LIMIT ?i', $_COOKIE['session'], 2);
		} catch (Exception $e) {
			// print_r($e);
			$AUTHORIZED = false;
			telegram_log("Database request failed\n\n" . $e->getMessage());
			$UI_ERROR = 'Database request failed (auth)';
			return;
		}

		if (count($data) > 1) {
			$AUTHORIZED = false;
			telegram_log('Too many rows');
			$UI_ERROR = 'Please, contact administrator (too many rows)';
			return;
		}

		if (count($data) == 0) {
			$AUTHORIZED = false;
			telegram_log('Session not found');
			logout();
			redirect();
			exit;
		}

		$person = $data[0];

		// Set last visit time
		try {
			$db->query('UPDATE `users` SET `last_visit` = ?s WHERE `id` = ?i', $NOW->format('Y-m-d H:i:s'), $person['id']);
		} catch (Exception $e) {
			// print_r($e);
			telegram_log("Database request failed\n\n" . $e->getMessage());
		}
	}
}
?>
