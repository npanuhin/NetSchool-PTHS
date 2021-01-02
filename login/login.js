var
	form = document.getElementById("login_form"),
	username_input = document.getElementById("username"),
	password_input = document.getElementById("password"),
	loading = document.getElementsByClassName("loading")[0];

Event.add(window, "load", () => {
	Event.add(form, "submit", (e) => {
		e.preventDefault();

		username_input.classList.remove("invalid");
		password_input.classList.remove("invalid");

		let username = username_input.value.trim(),
			password = password_input.value.trim();

		loading.classList.add("shown");

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
					loading.classList.remove("shown");

					r = JSON.parse(req.responseText);

					if (r[0] == "username") {
						username_input.classList.add("invalid");
						username_input.placeholder = r[1];
						username_input.value = "";

					} else if (r[0] == "password") {
						password_input.classList.add("invalid");
						password_input.placeholder = r[1];
						password_input.value = "";

					} else {
						alert(req.responseText);
					}
					// console.log(req.response);
				}
			},
			(req) => {
				loading.classList.remove("shown");
				alert("Error");
				alert(req.responseText);
			}
		);
	});
});