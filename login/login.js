var
	form = document.getElementById("login_form"),
	username_input = document.getElementById("username"),
	password_input = document.getElementById("password");

Event.add(form, "submit", (e) => {
	e.preventDefault();

	let username = username_input.value.trim(),
		password = password_input.value.trim();

	ajax(
		"POST",
		"/src/login.php", {
			"username": username,
			"password": password
		},
		(req) => {
			console.log(req.responseText);
			if (req.responseText == "success") {
				window.location = "../";
			} else {
				alert(req.responseText);
			}
		},
		(req) => {
			alert("Error");
			alert(req.responseText);
		}
	);
});