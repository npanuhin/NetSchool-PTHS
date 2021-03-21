<?php

function trigger_ui_error($message) {
	?>

	<div id="error">
		<style type="text/css">
			#error {
				position: fixed;
				width: 100vw;
				height: 100vh;
				z-index: 1000;
				background: rgba(0, 0, 0, 0.7);
				visibility: visible;
			}
			#error .message {
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				font-size: 1.5em;
				text-align: center;
			}
			#error * {
				color: #fff;
			}
			#error .message a {
				color: #00f;
			}
			#error .message a:hover {
				text-decoration: underline;
			}
			html.error, html.error body {
				overflow: hidden;
			}
		</style>

		<div class="message"><?php echo addslashes($message) ?></div>

		<script type="text/javascript">
			document.getElementsByTagName("html")[0].classList.add("error");
		</script>
	</div>

	<?php
	exit;
}

if (!is_null($UI_ERROR)) trigger_ui_error($UI_ERROR);

?>
