<?php
require_once __DIR__ . '/../src/config.php'; if (isset($_SESSION['user_id'])) { redirect(); exit(); } ?> <!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="build/login.min.css"> <?php include_once __DIR__ . '/../src/favicon.php' ?> <title>NetSchool PTHS | Вход</title></head><body><div class="container"><div class="title">NetSchool PTHS</div><form id="login_form"><input id="username" type="text" placeholder="Логин" title="Ваш логин NetSchool"><br><input id="password" type="password" placeholder="Пароль" title="Ваш пароль NetSchool"><br><input type="submit" value="Войти" title="Войти в систему NetSchool PTHS"></form><div class="message"><p>Загрузка<span>.</span><span>.</span><span>.</span></p></div></div><script type="text/javascript" src="/src/event.js" defer="defer"></script><script type="text/javascript" src="/src/ajax.js" defer="defer"></script><script type="text/javascript" src="build/login.min.js" defer="defer"></script></body></html>