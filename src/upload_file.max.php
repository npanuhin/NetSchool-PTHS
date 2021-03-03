<?php
require_once __DIR__ . '/config.php';

if ( $_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST) || !isset($_POST['file_upload_key']) ||
    json_decode(file_get_contents('config/config.json'), true)['file_upload_key'] != $_POST['password'] ) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    redirect();
    exit;
}

// Не передали путь для сохранения
if (!isset($_POST['path'])) exit('Operation not allowed');

// Существует ли вообще файл
if (!isset($_FILES) || !isset($_FILES['file'])) exit('No files');

// Проверка errors
if ($_FILES['file']['error'] != 0) exit('Upload error');

//Переданный массив сохраняем в переменной
$file = $_FILES['file'];

// Проверяем размер файла
if ($file['size'] > 31457280) exit('File is too large');

if ($file['name'] == '.htaccess') exit('Invalid file');

// Загрузка:
if (move_uploaded_file($file['tmp_name'], '../doc/' . $file['name'])) {
    exit('success');
}
exit('Error');
?>