<div id="error">
	<style type="text/css">
		#error {
			position: fixed;
			width: 100vw;
			height: 100vh;
			z-index: 1000;
			background-color: rgba(0, 0, 0, 0.7);
			visibility: hidden;
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
		html.error #error {
			visibility: visible;
		}
		html.error, html.error body {
			overflow: hidden;
		}
	</style>

	<div class="message"></div>

	<script type="text/javascript">
		function error(message) {
			document.getElementById("error").getElementsByClassName("message")[0].innerHTML = message;
			document.getElementsByTagName("html")[0].classList.add("error");
		}
	</script>
</div>

<?php

function error($message) {
	?>
	<script type="text/javascript">error("<?php echo $message ?>")</script>
	<?php
	exit;
}

?>