<?php
require_once __DIR__ . '/../src/config.php'; if (!isset($_SESSION['user_id']) || !verifySession()) { logout(); redirect('/login/'); exit; } ?> <!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="build/marks.min.css"> <?php include_once __DIR__ . '/../src/favicon.php' ?> <title>NetSchool PTHS | Оценки</title></head><body> <?php require_once __DIR__ . '/../src/header.php' ?> <main> <?php
 include_once __DIR__ . '/../src/message_alerts.php'; require_once __DIR__ . '/../src/menu.php'; ?> <div class="marks"><h3>Оценки</h3> <?php
 $diary = $person['diary']; if (is_null($diary)) { ?> <p>Оценки не загружены</p> <?php
 } else { print_r($diary); ?> <table> <?php
 ?> </table> <?php
 } ?> </div></main><script type="text/javascript" src="/src/event.js" defer="defer"></script><script type="text/javascript" src="/src/ajax.js" defer="defer"></script><script type="text/javascript" src="/src/build/common.min.js" defer="defer"></script></body></html>