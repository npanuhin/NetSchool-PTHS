<?php
require_once __DIR__ . '/config.php';

// if ($_SERVER['REQUEST_METHOD'] != 'POST') {
// 	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
// 	redirect();
// 	exit;
// }

logout();

echo 'success';
?>