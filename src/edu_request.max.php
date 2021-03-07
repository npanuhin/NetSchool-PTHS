<?php
include_once('simplehtmldom\simple_html_dom.php');
$url = "http://edu.school.ioffe.ru/tt_student.php";
$params = array(
	'nweek' => 49,
	'nclass' => "11%C1",
	'sub_asc' => 'Смотреть',
	'nteacher' => '-'
);

$query = "nweek={$params['nweek']}&nclass={$params['nclass']}&sub_asc=%D0%A1%D0%BC%D0%BE%D1%82%D1%80%D0%B5%D1%82%D1%8C&nteacher=-";

echo $query;
$result = file_get_contents($url, false, stream_context_create(array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $query
    )
)));

echo $result;
$dom=str_get_html($result);
foreach($dom->find('tr') as $element)
		echo '\n';
       echo $element;
?>