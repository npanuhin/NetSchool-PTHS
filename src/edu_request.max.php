
<?php

include_once __DIR__ . '/lib/simple_html_dom.php';

// –≠—В–Њ —В–Њ, –Ї–∞–Ї –і–Њ–ї–∂–љ—Л –њ—А–Є—Е–Њ–і–Є—В—М –і–∞–љ–љ—Л–µ:
//echo iconv('utf-8', 'MacCyrillic', '11а');
//$_POST['class'] = '11а';
//$_POST['day'] = '09.03.2021';

//mb_convert_encoding
//jan feb mar apr may jun jul aug sep oct nov dec
$_monthsList = array("Jan" => "января", "Feb" => "февраля", 
"Mar" => "марта", "Apr" => "апреля", "May" => "мая", "Jun" => "июня", 
"Jul" => "июля", "Aug" => "августа", "Sep" => "сентября",
"Oct" => "октября", "Nov" => "ноября", "Dec" => "декабря");


//echo mb_convert_encoding($_POST['class'], 'utf-8', 'koi8-r');
// –°–Њ–Њ—В–≤–µ—В–≤–µ–љ–љ–Њ –љ—Г–∂–љ–Њ —А–∞—Б–њ–∞—А—Б–Є—В—М —Н—В–Њ $_POST['day'] –Є —Б–≥–µ–љ–µ—А–Є—А–Њ–≤–∞—В—М class (—Г–∂–µ –≥–Њ—В–Њ–≤–Њ) –Є nweek:
//echo urlencode("11а");
//echo "11%C1";
//echo urlencode(mb_convert_encoding( $_POST['class'], 'koi8-r', 'utf-8'));
$class = urlencode(mb_convert_encoding( $_POST['class'], 'koi8-r', 'utf-8'));

//mb_convert_encoding($_POST['class'], 'koi8-r', 'utf-8');

$nweek = floor(date_create($_POST['day'])->diff(date_create("2020-03-30"))->format('%a')/7) + 1; // TODO from $_POST['day']
//echo $nweek;
//echo "\n";
$dayText = date_create($_POST['day'])->Format('j') .' '. $_monthsList[date_create($_POST['day'])->Format('M')];
//echo $dayText;

$result = mb_convert_encoding(file_get_contents('http://edu.school.ioffe.ru/tt_student.php', false, stream_context_create(array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => "nweek={$nweek}&nclass={$class}&sub_asc=–°–Љ–Њ—В—А–µ—В—М&nteacher=-"
    )
))), 'utf-8', 'koi8-r');

//print_r($result);

$dom = str_get_html($result)->find('table')[0]->find('tbody')[0];
if (!$dom){
exit();
}
echo "[";
// print_r($dom);
$out = false;
$flag = false;
foreach ($dom->find('tr') as $line) {
	if ($flag){
		echo ",";
	}
	$flag = true;
	$tds = $line->find('td');
	//echo count($tds);
	if (count($tds) == 5)
	{

		//!== , it's important. Don't change.
		$out = strpos($tds[0]->plaintext, $dayText) !== false;
	}
	//echo $tds[0]->plaintext;
	//echo $_POST['day'];
	
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
		
		// –Э–Х –Э–Р–Ф–Ю preg_replace –Я–Ю–Ц–Р–Ы–£–Щ–°–¢–Р
		// echo preg_replace('#\</?td\>#', "", $arr[count($arr) - 4]);
		// echo preg_replace('#\</?td\>#', "", $arr[count($arr) - 3]);
		// echo preg_replace('#\</?td\>#', "", $arr[count($arr) - 2]);
		// echo preg_replace('#\</?td\>#', "", $arr[count($arr) - 1]);

		echo '"}';
	}
}
?>
]