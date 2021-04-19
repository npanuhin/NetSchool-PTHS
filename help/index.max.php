<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/session.php';

if (!$AUTHORIZED) {
	logout();
	redirect('/login/');
	exit;
}
get_person();
?>

<!DOCTYPE html>
<html lang="ru"<?php if (isset($_COOKIE['dark']) && $_COOKIE['dark']) echo ' class="dark"'?>>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="build/help.min.css">
	<?php include_once __DIR__ . '/../src/favicon.html' ?>
	<title>NetSchool PTHS | Помощь</title>
</head>

<body>
	<?php require_once __DIR__ . '/../src/header.php' ?>

	<main>
		<?php
		include_once __DIR__ . '/../src/message_alerts.php';
		require_once __DIR__ . '/../src/menu.html';
		?>

		<div class="help">

			<h1 title="Ответы на часто задаваемые вопросы">F.A.Q.</h1>
			<p class="desc">
				Это сайт-зеркало с открытым исходным кодом для удобного просмотра информации из Netschool. Сайт разработан для учеников и их родителей (учителям нельзя здесь регистрироватся, пожалуйста, не пытайтесь) и призван сделать повседневную жизнь более комфортной.
			</p>

<!-------------------------------------------------------------------------------------------------------------------------------------------->
			<h4>Общее</h4>

			<div class="question" id="question-conn">— Я нашел баги, придумал интересную фичу. Как мне донести свою мысль в поддержку?</div>
			— Просто напишите в поддержку по одной из ссылок ниже. Но для начала крайне рекомендуется ознакомится со всем этим разделом (с неочевидными фичами и тем, что сейчас находится в разработке).

			<div class="question" id="question-safety">— Насколько это безопасно? Храните ли Вы мои пароли?</div>
			— Мы получаем данные через <a href="https://ru.wikipedia.org/wiki/Веб-скрейпинг" target="_blank">парсинг</a> обычного нетскула, и нам нужны «чистые» логин и пароль для того, чтобы получать доступ. Но мы их не используем (и никому не передаем), кроме случаев, когда нам нужно срочно что-то починить.

			<div class="question" id="question-better">— Чем этот Netschool лучше стандартного?</div>
			— Вам не требуется авторизация каждый раз, когда Вы заходите на сайт. У нас более удобные отображения расписания, оценок и домашних заданий. Здесь же Вы удобно представлены ссылки на дистанционные уроки и спецкурсы. Можно сразу слушать музыку, чтобы придти в себя после увиденного в дневнике...
			<br>
			У нас адекватная мобильная версия сайта, темная тема, и еще куча прикольных плюшек, которые еще делаются. Ну и, наконец, стабильность. Этот нетскул не напрямую зависит от основного, и смотреть на дневник, домашние задания и расписание можно даже если основной сайт лежит (увы, такое часто бывает...).

			<div class="question" id="question-use-always">— Значит ли это, что я могу всегда пользоваться только этим нетскулом?</div>
			— Де-факто — да. Но если Вы не хотите себе внезапных неприятностей, то лучше изредка заглядывать в основной нетскул, чтобы, как минимум, проверить почту.

			<div class="question" id="question-help">— Я хочу чем-нибудь помочь в разработке!</div>
			— Это было бы круто. Мы "доживаем" последнюю четверть в ФТШ, и пока у нас нет никого, кому бы можно было бы поручить продолжить наше дело, однако "быстрый вход" не гарантируем. Но, если у Вас есть желание что-нибудь сделать, даже если нет опыта работы с PHP, SCSS или JS, то присоединяйтесь, не проблема, разберемся, всем рады.

			<div class="question" id="question-delete">— Мне не понравилось, я не буду этим пользоваться / я не хочу доверять Вам мой пароль. </div>
			— Вы можете всегда удалить свой аккаунт. С этого момента мы не будет обновлять Ваши данные. Также это удалит Ваши логин и пароль из базы данных (здесь Вам придется поверить в нашу честность). Если Вы захотите вернуться, Вам придется заново пройти регистрацию. Удалить страницу можно <a title="Не хотите написать в техподдержку о том, что не понравилось?" href="/delete/" target="_blank">по ссылке</a>.

<!-------------------------------------------------------------------------------------------------------------------------------------------->
			<h4>Почему?..</h4>

			<div class="question" id="question-marks">— Почему некоторые оценки подсвечиваются?</div>
			— Подсвечиваются оценки, которые выше среднего балла на текущий момент. Иными словами — то, что светится, его поднимает.

			<div class="question" id="question-faces">— Почему в объявлениях лицо отображается только у Анны Анатольевны?</div>
			— Это совпадает с функционалом обычного нетскула (но у нас также каждый учитель имеет свой индивидуальный цвет), и смысла добавлять других учителей достаточно мало. Если это кому-нибудь действительно нужно — пишите, добавим.

			<div class="question" id="question-faces">— Почему радио резко останавливается/запускается?</div>
			— Радио останавливается при переходе на другую страницу. К сожалению, из-за соображений удобства и безопасности, браузеры не позволяют нам сразу продолжить вопроизведение, как только вы зашли на страницу. Поэтому мы ждём, пока Вы щёлкните куда-нибудь и сразу включаем музыку.


<!-------------------------------------------------------------------------------------------------------------------------------------------->
			<h4>Известные <span class="del" title="&#34;Не баг, а фича&#34;">баги</span> фичи и планы на будущее</h4>

			<p title="А вот и подсказка :)">
				Общий совет — наведите на неочевидное место (если Вы с компьютера), и там выплывет подсказка (да, этот же совет появлялся и при первом заходе на сайт, но мало ли что, кто их читает). Баги могут время от времени появляться из-за тестирования на сервере, проблем синхронизации, чей-то неосторожности...</p><p> Так что поломки время от времени — нормально, но они, как правило, не долгие (если у Вас длительные проблемы, то рекомендуется сообщить в поддержку).
			</p>

			<div class="question" id="question-clock">— Часы в правом верхнем углу отстают!</div>
			— Это время последнего обновления Ваших данных из нетскула. Оно всегда будет отставать от реального времени, и крайне не рекомендуется на них ориентироваться.
			
			<div class="question" id="question-bug-switch">— При переключениями между секциями возникает «мелькание», экран на короткое время вспыхивает белым.</div>
			— Эта проблема была исправлена уже давно, но если вы с ней столкнулись, пожалуйста, напишите в поддержку.

			<div class="question" id="question-mail">— Вы будете добавлять почту?</div>
			— Достаточно сложно сделать полноценную систему, которая, во-первых, правильно считывает извращенные методы отображения почты нетскулом, а, во-вторых, которая сама способна это все дело нормально отображать и быть удобной. Так что, видимо, пока не появятся дополнительные люди с горящими глазами и прямыми руками... PS{разработчик #2}: вообще-то оно в планах есть...

			<a class="github_link" href="https://github.com/npanuhin/NetSchool-PTHS" target="_blank" title="Репозиторий вебсайта на GitHub">
				<?php include_once __DIR__ . '/../files/icons/github.svg' ?>
			</a>

			<a class="vk_link" href="https://vk.com/netschool_pths" target="_blank" title="Группа ВКонтакте">
				<?php include_once __DIR__ . '/../files/icons/vk.svg' ?>
			</a>

			<!-- © Никита Панюхин, Ева Пальчикова, Андрей Ситников, Марк Ипатов, 2021 -->
		</div>

	</main>

	<?php include_once __DIR__ . '/../src/online_media/online_media.php' ?>

	<script type="text/javascript" src="/src/lib/event.js" defer></script>
	<script type="text/javascript" src="/src/build/ajax.min.js" defer></script>
	<script type="text/javascript" src="/src/build/common.min.js" defer></script>
	<!-- <script type="text/javascript" src="build/help.min.js" defer></script> -->
	<script type="text/javascript" src="/src/online_media/build/online_media.min.js" defer></script>
</body>

</html>
