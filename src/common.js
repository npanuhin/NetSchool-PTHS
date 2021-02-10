var
	html = document.getElementsByTagName("html")[0],
	body = document.getElementsByTagName("body")[0],
	main = document.getElementsByTagName("main")[0],

	main_bottom_margin = 100,

	menu_button = document.getElementsByClassName("menu_icon_wrapper")[0],
	logout_button = document.getElementsByClassName("exit_icon")[0],

	menu = document.getElementsByClassName("menu")[0],
	menu_adjustment_timout,

	tasks_block = (document.getElementsByClassName("tasks") ? document.getElementsByClassName("tasks")[0] : undefined),
	marks_block = (document.getElementsByClassName("marks") ? document.getElementsByClassName("marks")[0] : undefined);
	timetable_block = (document.getElementsByClassName("timetable") ? document.getElementsByClassName("timetable")[0] : undefined),
	announcements_block = (document.getElementsByClassName("announcements") ? document.getElementsByClassName("announcements")[0] : undefined),

	message_alerts = document.getElementsByClassName("message_alert");

function adjust_menu(argument) {
	
}

Event.add(window, "load", () => {

	main.style.minHeight = menu.clientHeight + "px";

	Event.add(menu_button, "mousedown", () => {
		html.classList.add("loaded");

		let delta_height = menu.clientHeight;

		clearTimeout(menu_adjustment_timout);

		if (menu.classList.contains("shown")) {

			menu_adjustment_timout = setTimeout(() => {
				// main.style.minHeight = delta_height + "px";
				main.style.marginBottom = main_bottom_margin + "px";
			}, 300);

		} else {

			// main.style.minHeight = main.clientHeight + delta_height + "px";
			main.style.marginBottom = main_bottom_margin + delta_height + "px";
		}

		if (tasks_block !== undefined) tasks_block.style.transform = "translateY(" + (menu.classList.contains("shown") ? 0 : delta_height) + "px)";
		if (marks_block !== undefined) marks_block.style.transform = "translateY(" + (menu.classList.contains("shown") ? 0 : delta_height) + "px)";
		if (timetable_block !== undefined) timetable_block.style.transform = "translateY(" + (menu.classList.contains("shown") ? 0 : delta_height) + "px)";
		if (announcements_block !== undefined) announcements_block.style.transform = "translateY(" + (menu.classList.contains("shown") ? 0 : delta_height) + "px)";

		menu.classList.toggle("shown");
		menu_button.classList.toggle("active", menu.classList.contains("shown"));
	});

	Event.add(logout_button, "click", () => {
		html.classList.add("loaded");
		body.style.cursor = "wait";
		
		ajax(
			"POST",
			"/src/logout.php",
			{},
			(req) => {
				if (req.responseText == "success") {
					window.location = "/login/";
				} else {
					body.style.cursor = "";
					alert("Error");
					alert(req.responseText);
				}
			},
			(req) => {
				body.style.cursor = "";
				alert("Error");
				alert(req.responseText);
			}
		);
	});

	for (let message_alert of message_alerts) {
		if (message_alert.getElementsByClassName("cross-icon") !== undefined) {

			Event.add(message_alert.getElementsByClassName("cross-icon")[0], "click", () => {
				html.classList.add("loaded");
				body.style.cursor = "wait";
				
				ajax(
					"POST",
					"/src/message_alert_close.php",
					{
						"name": message_alert.id.slice(14)
					},
					(req) => {
						body.style.cursor = "";

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
						body.style.cursor = "";
						alert("Error");
						alert(req.responseText);
					}
				);
			});
		}
	}
});