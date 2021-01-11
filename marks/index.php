<?php
require_once '../src/config.php';

if (!isset($_SESSION['user_id']) || !verifySession()) {
	logout();
	redirect('/login/');
	exit;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="build/marks.min.css">
	<title>NetSchool PTHS | Оценки</title>
</head>
<body>

	<?php require_once '../src/header.php' ?>

	<main>

		<div class="marks">
			<h3>Оценки</h3>

			<table>
				
			</table>
		</div>

	</main>

	<script type="text/javascript" src="/src/event.js"></script>
	<script type="text/javascript" src="/src/ajax.js"></script>
	<script type="text/javascript" src="build/marks.min.js"></script>
</body>

</html>