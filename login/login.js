var
	html = document.getElementsByTagName("html")[0],
	body = document.getElementsByTagName("body")[0],
	form = document.getElementById("login_form"),
	username_input = document.getElementById("username"),
	password_input = document.getElementById("password"),
	message = document.getElementsByClassName("message")[0],

	log_try_delay = 5000,
	log_tries = 60;


Event.add(form, "submit", (e) => {
	e.preventDefault();
	html.classList.add("wait");

	username_input.classList.remove("invalid");
	password_input.classList.remove("invalid");

	let username = username_input.value.trim(),
		password = password_input.value.trim()

		cur_log_try_delay = log_try_delay;

	message.classList.add("shown");

	(function log_in() {
		ajax(
			"POST",
			"/src/login.php", {
				"username": username,
				"password": password
			},
			(req) => {
				// console.log(req.responseText);
				if (req.responseText == "success") {
					window.location = "../";
				
				} else {
					r = JSON.parse(req.responseText);
					// console.log(r); 
					html.classList.remove("wait");

					if (r[0] == "username") {
						username_input.classList.add("invalid");
						username_input.placeholder = r[1];
						username_input.value = "";
						message.classList.remove("shown");

					} else if (r[0] == "password") {
						password_input.classList.add("invalid");
						password_input.placeholder = r[1];
						password_input.value = "";
						message.classList.remove("shown");

					} else if (r[0] == "message") {
						message.classList.add("small");
						message.getElementsByTagName("p")[0].innerHTML = r[1];

					} else {
						message.classList.remove("shown");
						alert("Error");
						alert(req.responseText);
					}
				}
			},
			(req) => {
				message.classList.remove("shown");
				html.classList.remove("wait");
				alert("Error");
				alert(req.responseText);
			}
		);

		--cur_log_try_delay;
		if (cur_log_try_delay <= 0) clearInterval(log_interval);

		setTimeout(log_in, log_try_delay);
	})();
});
