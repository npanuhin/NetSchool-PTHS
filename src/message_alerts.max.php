<?php

/*
expires: once-seen — disappears when user just open it

expires: limited-time — disappears after the specified time is reached
+ expires_at: the date PHP understands (prob. ISO)

Ex.:
{
	"id": 3,
    "expires": "limited-time",
    "msg_text": "Это сообщение должно исчезнуть в 22:46",
    "expires_at": "2021-04-24 22:46"
}

expires: limited-seen — disappears after it is LOADED specified number of times. In fact, once-seen is limited-seen with expires_at = 1
+ expires_at: just a number of suggested looks. Ex. — look at standart announcements.

any other — needs manual closing. Not recommended because users are too lazy…

Ex.:

{
	"id": 4,
	"msg_text": "This one should be stable"
}

id is needed (simultaneously to be responsible for closing) to be easier to manage the messages. For ex., you can create message for all the users, than, when it will become redundant, remove all with this id. That means flexebility in terms of expiring and text for different users.
*/

// Insert if not exists

$db->query('
	UPDATE netschool.messages
	SET msg_data = ?s
	WHERE (`user_id` = ?i) AND (`msg_data` IS NULL) 
	', 
	'[
	    {
	        "id": 1,
	        "expires": "limited-seen",
	        "expires_at": 10,
	        "msg_text": "<p title=\"Да, например, вот так\">ProTip!</p> Наведите на элемент, чтобы увидеть дополнительную информацию."
	    },
	    {
	        "id": 2,
	        "expires": "limited-seen",
	        "expires_at": 10,
	        "msg_text": "<p>Обратите внимание!</p> Вся информация, предоставленная на этом сайте, <p>НЕ ЯВЛЯЕТСЯ ОФИЦИАЛЬНОЙ</p>."
	    },
	    {
	        "id": 3,
	        "expires": "limited-seen",
	        "expires_at": 10,
	        "msg_text": "Вы можете использовать <kbd>Ctrl -/+</kbd> или <kbd>Ctrl + <div class=\"mouse\"></div></kbd> для изменения масштаба"
	    }
	]', $person['id']);

$messages = json_decode($db -> getRow('SELECT msg_data FROM `messages` WHERE `user_id` = ?i', $person['id'])['msg_data']);


foreach ($messages as $index=>$message) {
		if (($message -> expires == 'limited-time') and (new DateTime($message -> expires_at) < new DateTime('NOW'))){
			unset($messages[$index]);
			continue; // if msg is expired this way, we should hide it as soon as possible
		}
		?>

		<div class="message_alert" id="message_alert_<?php echo $message->id?>">
			<?php

			
			echo nl2br($message->msg_text);

			if($message->expires == "once-seen"){
				unset($messages[$index]);
			}
			else if ($message->expires == "limited-seen"){
				--$message->expires_at;

				if ($message->expires_at <= 0){
					unset($messages[$index]);
				}
			}

			
			?>

			<svg class="cross-icon" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
				<title>Закрыть</title>
				<path d="M13.3 10.0006L19.4873 3.81337C20.1709 3.12974 20.1709 2.02141 19.4873 1.33885L18.6624 0.514004C17.9786 -0.169839 16.8703 -0.169839 16.1877 0.514004L10.0006 6.70106L3.81337 0.512722C3.12974 -0.170907 2.02141 -0.170907 1.33885 0.512722L0.512722 1.33756C-0.170907 2.02141 -0.170907 3.12974 0.512722 3.8123L6.70106 10.0006L0.514004 16.1877C-0.169839 16.8715 -0.169839 17.9799 0.514004 18.6624L1.33885 19.4873C2.02247 20.1709 3.13081 20.1709 3.81337 19.4873L10.0006 13.3L16.1877 19.4873C16.8715 20.1709 17.9799 20.1709 18.6624 19.4873L19.4873 18.6624C20.1709 17.9786 20.1709 16.8703 19.4873 16.1877L13.3 10.0006Z"/>
			</svg>
		</div>

		<?php
}

//this is needed to create back from "crippled" array a normal one.
$messages = array_values($messages);

$db->query('
		UPDATE netschool.messages
		SET msg_data = ?s
		WHERE `user_id` = ?i',
		json_encode($messages), 
		$person['id']);

?>
