<?php
include_once __DIR__ . '/lib/simple_html_dom.php';

// Это то, как должны приходить данные:
$_POST['class'] = '11а';
$_POST['day'] = '07.03.2021';

// Соответвенно нужно распарсить это $_POST['day'] и сгенерировать class (уже готово) и nweek:
$class = mb_convert_encoding($_POST['class'], 'koi8-r', 'utf-8');
$nweek = 49; // TODO from $_POST['day']


$result = mb_convert_encoding(file_get_contents('http://edu.school.ioffe.ru/tt_student.php', false, stream_context_create(array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => "nweek={$nweek}&nclass={$class}&sub_asc=Смотреть&nteacher=-"
    )
))), 'utf-8', 'koi8-r');

// print_r($result);

$dom = str_get_html($result)->find('table')[0]->find('tbody')[0];

// print_r($dom);

foreach ($dom->find('tr') as $line) {
    $tds = $line->find('td');

    echo $tds[2]->plaintext;

    // НЕ НАДО preg_replace ПОЖАЛУЙСТА
	// echo preg_replace('#\</?td\>#', "", $arr[count($arr) - 4]);
	// echo preg_replace('#\</?td\>#', "", $arr[count($arr) - 3]);
	// echo preg_replace('#\</?td\>#', "", $arr[count($arr) - 2]);
	// echo preg_replace('#\</?td\>#', "", $arr[count($arr) - 1]);

	echo '<br>';
}
?>