var
	logout_button = document.getElementsByClassName("sign-out")[0];

Event.add(window, "load", () => {
	Event.add(logout_button, "click", () => {
		ajax(
			"POST",
			"/src/logout.php",
			{},
			(req) => {
				if (req.responseText == "success") {
					window.location = "/login/";
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
});