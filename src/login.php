<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST)) {
	header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
    redirect();
    exit;
}

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) exit(json_encode(array('message', 'Вы уже вошли')));

$username = trim($_POST['username']);
$password = trim($_POST['password']);

if (strlen($username) < 6) exit(json_encode(array('username', 'Логин слишком короткий')));
if (strlen($password) < 4) exit(json_encode(array('password', 'Пароль слишком короткий')));

if (!$mysqli) {
    // echo 'Ошибка: Невозможно установить соединение с MySQL.' . PHP_EOL;
    // echo 'Код ошибки errno: ' . mysqli_connect_errno() . PHP_EOL;
    // echo 'Текст ошибки error: ' . mysqli_connect_error() . PHP_EOL;
    exit(json_encode(array('message', 'Database connection failed')));
}

$query = mysqli_query($mysqli, 'SELECT `id`, `password`, `first_name`, `last_name`, `last_update` FROM `users` WHERE `username` = "' . $username . '" LIMIT 2');

if (mysqli_num_rows($query) > 1) exit(json_encode(array('message', 'Please, contact administrator (too many rows)')));

if (mysqli_num_rows($query) == 0) {

	$query = mysqli_query($mysqli, 'INSERT INTO `users` (`username`, `password`) VALUES ("' . $username . '", "' . $password . '")');

	if (!$query) {
		exit(json_encode(array('message', 'Database request failed')));
	}

	exit(json_encode(array('message', 'Указанный логин не был найден, но был добавлен в очередь на обработку.<br>Если вы указали верные данные, вы сможете войти в систему через несколько минут.<br>В противном случае, ваши данные будут автоматически удалены.')));
}

$row = mysqli_fetch_assoc($query);

if (is_null($row['last_update'])) {
	exit(json_encode(array('message', 'Ваша учётная запись ещё не готова.<br>Пожалуйста, подождите.')));
}

if ($row['password'] != $password) exit(json_encode(array('password', 'Неверный пароль')));

$_SESSION['user_id'] = $row['id'];
$_SESSION['username'] = $username;
$_SESSION['first_name'] = $row['first_name'];
$_SESSION['last_name'] = $row['last_name'];

mysqli_close($mysqli);

echo 'success';
?>