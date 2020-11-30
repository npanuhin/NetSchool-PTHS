<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) exit('Already logged in');

$username = trim($_POST['username']);
$password = trim($_POST['password']);

if (strlen($username) < 6) exit('Username is too small');
if (strlen($password) < 4) exit('Password is too small');

$mysqli = dbConnect();

if (!$mysqli) {
    // echo 'Ошибка: Невозможно установить соединение с MySQL.' . PHP_EOL;
    // echo 'Код ошибки errno: ' . mysqli_connect_errno() . PHP_EOL;
    // echo 'Текст ошибки error: ' . mysqli_connect_error() . PHP_EOL;
    exit('Database connection failed');
}

$query = mysqli_query($mysqli, 'SELECT `id`, `password`, `first_name`, `middle_name`, `last_name` FROM `users` WHERE `username` = "' . $username . '" LIMIT 2');

if (!$query) exit('0');

if (mysqli_num_rows($query) > 1) exit('Please, contact administrator (too many rows)');

if (mysqli_num_rows($query) == 0) exit('Login not found');

$row = mysqli_fetch_assoc($query);

if ($row['password'] != $password) exit('Wrong password');

$_SESSION['user_id'] = $row['id'];
$_SESSION['username'] = $username;
$_SESSION['first_name'] = $row['first_name'];
$_SESSION['middle_name'] = $row['middle_name'];
$_SESSION['last_name'] = $row['last_name'];

mysqli_close($mysqli);

echo 'success';
?>