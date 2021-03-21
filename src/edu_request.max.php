<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/session.php';
include_once __DIR__ . '/lib/simple_html_dom.php';

if (!$AUTHORIZED) {
	logout();
	redirect('/login/');
	exit;
}
get_person();

if (
	$_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST)       ||
	!isset($_POST['day'])                || !trim($_POST['day']) ||
	!isset($_POST['courses'])            || (trim($_POST['courses']) != 0 && trim($_POST['courses']) != 1)
) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	redirect();
	exit;
}

$class = urlencode(mb_convert_encoding($person['class'], 'koi8-r', 'utf-8'));

$day = new DateTime($_POST['day']);
$nweek = floor($day->diff(new DateTime('2020-03-30'))->format('%a') / 7) + 1;
$day_text = $day->Format('j') . ' ' . mb_strtolower($months_genetive[$day->Format('m') - 1]);

$courses = trim($_POST['courses']);

if ($courses) {
	$href = 'http://edu.school.ioffe.ru/tt_special.php';
} else {
	$href = 'http://edu.school.ioffe.ru/tt_student.php';
}

$result = mb_convert_encoding(file_get_contents($href, false, stream_context_create(array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => "nweek={$nweek}&nclass={$class}&nteacher=-" 
    )
))), 'utf-8', 'koi8-r');


$dom = str_get_html($result)->find('table')[0]->find('tbody')[0];
$result = [];
$out = false;
foreach ($dom->find('tr') as $line) {
	$tds = $line->find('td');

	if (count($tds) == 5 + $courses) {
		$out = strpos($tds[0]->plaintext, $day_text) !== false;
	}

	if ($out) {
		array_push($result, array(
			'time'    => $tds[count($tds) - 4 - $courses]->plaintext,
			'name'    => $tds[count($tds) - 3]->plaintext,
			'teacher' => $tds[count($tds) - 2]->plaintext,
			'href'    => $tds[count($tds) - 1]->find('a')[0]->href,
		));
	}
}

echo json_encode($result);
?>