var
	form = document.getElementById("login_form"),
	username_input = document.getElementById("username"),
	password_input = document.getElementById("password"),
	statusbar = document.getElementsByClassName("statusbar")[0],
	loading = document.getElementsByClassName("loading")[0];

Event.add(window, "load", () => {
	Event.add(form, "submit", (e) => {
		e.preventDefault();

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
					statusbar.innerHTML = req.responseText;
					console.log(statusbar, req.responseText);
					// alert(req.responseText);
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