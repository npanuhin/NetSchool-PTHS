var
	html = document.getElementsByTagName("html")[0],
	body = document.getElementsByTagName("body")[0],
	main = document.getElementsByTagName("main")[0],
	
	// main_bottom_margin = 100,

	menu_button = document.getElementsByClassName("menu_icon_wrapper")[0],
	dark_mode_button = document.getElementsByClassName("moon_icon_wrapper")[0],
	logout_button = document.getElementsByClassName("exit_icon")[0],
	
	menu = document.getElementsByClassName("menu")[0],

	message_alerts = document.getElementsByClassName("message_alert"),

	ui_alert_box = document.getElementsByClassName("ui_alert")[0],
	ui_alert_box_timout,

	dark_mode_transition_timeout = 600;


function ui_alert(text) {
	ui_alert_box.innerHTML = text;
	ui_alert_box.classList.add("shown");
	clearTimeout(ui_alert_box_timout);
	ui_alert_box_timout = setTimeout(() => {
		ui_alert_box.classList.remove("shown")
	}, 5000);
}

Event.add(window, "load", () => {

	html.classList.add("loaded");

	main.style.minHeight = menu.clientHeight + "px";

	Event.add(menu_button, "mousedown", () => {
		html.classList.add("interacted");

		menu.classList.toggle("shown");
		menu_button.classList.toggle("active", menu.classList.contains("shown"));
	});

	Event.add(dark_mode_button, "mousedown", () => {
		html.classList.add("interacted");
		html.classList.add("wait");

		ajax(
			"POST",
			"/src/toggle_dark_mode.php",
			{},
			(req) => {
				html.classList.add("dark_mode_transition");
				html.classList.remove("wait");

				if (req.responseText == "1") {
					html.classList.add("dark");

				} else if (req.responseText == "0"){
					html.classList.remove("dark");

				} else {
					alert("Error");
					alert(req.responseText);
				}

				setTimeout(() => {html.classList.remove("dark_mode_transition")}, dark_mode_transition_timeout);
			},
			(req) => {
				html.classList.remove("wait");
				alert("Error");
				alert(req.responseText);
			}
		);
	});

	Event.add(logout_button, "click", () => {
		html.classList.add("interacted");
		html.classList.add("wait");
		
		ajax(
			"POST",
			"/src/logout.php",
			{},
			(req) => {
				if (req.responseText == "success") {
					window.location = "/login/";
				} else {
					html.classList.remove("wait");
					alert("Error");
					alert(req.responseText);
				}
			},
			(req) => {
				html.classList.remove("wait");
				alert("Error");
				alert(req.responseText);
			}
		);
	});

	for (let message_alert of message_alerts) {
		if (message_alert.getElementsByClassName("cross-icon") !== undefined) {

			Event.add(message_alert.getElementsByClassName("cross-icon")[0], "click", () => {
				html.classList.add("interacted");
				html.classList.add("wait");
				
				ajax(
					"POST",
					"/src/message_alert_close.php",
					{
						"name": message_alert.id.slice(14)
					},
					(req) => {
						html.classList.remove("wait");

						if (req.responseText == "success") {
							message_alert.classList.add("hidden");
							setTimeout(() => {
								message_alert.remove();
							}, 650);
						} else {
							alert("Error");
							alert(req.responseText);
						}
					},
					(req) => {
						html.classList.remove("wait");
						alert("Error");
						alert(req.responseText);
					}
				);
			});
		}
	}
});