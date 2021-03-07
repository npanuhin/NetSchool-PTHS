<?php
include_once('simplehtmldom\simple_html_dom.php');
$url = "http://edu.school.ioffe.ru/tt_student.php";
$letters = array(
	'а' => "%C1",
	'б' => "%C2",
	'в' => "%C3"
);
$params = array(
	'nweek' => 49,
	'nclass' => "11{$letters['а']}",//"{$_POST['class']}{$letters[$_POST['letter']}",
	'sub_asc' => '‘мотреть',
	'nteacher' => '-'
);

$query = "nweek={$params['nweek']}&nclass={$params['nclass']}&sub_asc=%D0%A1%D0%BC%D0%BE%D1%82%D1%80%D0%B5%D1%82%D1%8C&nteacher=-";

//echo $query;
$result = file_get_contents($url, false, stream_context_create(array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $query
    )
)));

echo $result;
$dom=str_get_html($result)->find('tbody')[0];
foreach($dom->find('tr') as $element){
	echo "<\br>";
    $arr = $element -> find('td');
	echo preg_replace('#\</?td\>#', "", $arr[count($arr)-4]);
	echo ' С ';
	echo preg_replace('#\</?td\>#', "", $arr[count($arr)-3]);
	echo ' С ';
	echo preg_replace('#\</?td\>#', "", $arr[count($arr)-2]);
	echo ' С ';
	echo preg_replace('#\</?td\>#', "", $arr[count($arr)-1]);
}
?>