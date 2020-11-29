var
	logout_button = document.getElementById("logout");

Event.add(logout_button, "click", () => {
	ajax(
		"POST",
		"/src/logout.php",
		{},
		(req) => {
			if (req.responseText == "success") {
				window.location = "/";
			} else {
				alert("Error");
				alert(req.responseText);
			}
		},
		(req) => {
			alert("Error");
			alert(req.responseText);
		}
	);
});