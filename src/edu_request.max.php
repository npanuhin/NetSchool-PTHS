<?php

include_once __DIR__ . '/lib/simple_html_dom.php';

//jan feb mar apr may jun jul aug sep oct nov dec
$_monthsList = array("Jan" => "января", "Feb" => "февраля", 
"Mar" => "марта", "Apr" => "апреля", "May" => "мая", "Jun" => "июня", 
"Jul" => "июля", "Aug" => "августа", "Sep" => "сентября",
"Oct" => "октября", "Nov" => "ноября", "Dec" => "декабря");


$class = urlencode(mb_convert_encoding( $_POST['class'], 'koi8-r', 'utf-8'));


$nweek = floor(date_create($_POST['day'])->diff(date_create("2020-03-30"))->format('%a')/7) + 1;

$dayText = date_create($_POST['day'])->Format('j') .' '. $_monthsList[date_create($_POST['day'])->Format('M')];


$result = mb_convert_encoding(file_get_contents('http://edu.school.ioffe.ru/tt_student.php', false, stream_context_create(array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => "nweek={$nweek}&nclass={$class}&sub_asc=–Смотреть&nteacher=-"
    )
))), 'utf-8', 'koi8-r');


$dom = str_get_html($result)->find('table')[0]->find('tbody')[0];

echo "[";

$out = false;
foreach ($dom->find('tr') as $line) {
	if ($out){
		echo ",";
	}
	$tds = $line->find('td');

	if (count($tds) == 5)
	{

		//!== , it's important. Don't change.
		$out = strpos($tds[0]->plaintext, $dayText) !== false;
	}

	if ($out)
	{
		echo '{"time":"';
		echo $tds[count($tds)-4]->plaintext;
		echo '","name":"';
		echo $tds[count($tds)-3]->plaintext;
		echo '","teacher":"';
		echo $tds[count($tds)-2]->plaintext;
		echo '","href":"';
		echo $tds[count($tds)-1]->find('a')[0]->href;

		echo '"}';
	}
}
?>
]