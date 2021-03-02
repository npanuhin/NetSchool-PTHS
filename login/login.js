var
	html = document.getElementsByTagName("html")[0],
	body = document.getElementsByTagName("body")[0],
	form = document.getElementById("login_form"),
	username_input = document.getElementById("username"),
	password_input = document.getElementById("password"),
	message = document.getElementsByClassName("message")[0];

Event.add(window, "load", () => {
	html.classList.add("loaded");

	Event.add(form, "submit", (e) => {
		e.preventDefault();
		html.classList.add("wait");

		username_input.classList.remove("invalid");
		password_input.classList.remove("invalid");

		let username = username_input.value.trim(),
			password = password_input.value.trim();

		message.classList.add("shown");

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
						// console.log(r);
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
	});
});