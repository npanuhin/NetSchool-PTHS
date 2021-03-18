var
	html = document.documentElement,
	body = document.body,
	main = document.getElementsByTagName("main")[0],

	interacted = false,
	
	// main_bottom_margin = 100,

	menu_button = document.getElementsByClassName("menu_icon_wrapper")[0],
	dark_mode_button = document.getElementsByClassName("moon_icon_wrapper")[0],
	logout_button = document.getElementsByClassName("exit_icon")[0],
	
	menu = document.getElementsByClassName("menu")[0],
	menu_links = menu.getElementsByTagName("a"),

	message_alerts = document.getElementsByClassName("message_alert"),

	ui_alert_box = document.getElementsByClassName("ui_alert")[0],
	ui_alert_box_timout,

	dark_mode_transition_timeout = 800;


// ======================================== Tools ========================================
function trigger_event(target, name) {
	var event;
	if (document.createEvent){
		event = document.createEvent("HTMLEvents");
		event.initEvent(name, true, true);
		event.eventName = name;
		target.dispatchEvent(event);
	} else {
		event = document.createEventObject();
		event.eventName = name;
		event.eventType = name;
		target.fireEvent("on" + event.eventType, event);
	}
}

function set_url(url) {
	window.history.replaceState({"Title": document.title, "Url": url}, document.title, url);
}

function clear_url_hash() {
	let url = new URL(window.location.href);
	url.hash = "";
	set_url(url.href);
}

// =======================================================================================


function ui_alert(text) {
	ui_alert_box.innerHTML = text;
	ui_alert_box.classList.add("shown");
	clearTimeout(ui_alert_box_timout);
	ui_alert_box_timout = setTimeout(() => {
		ui_alert_box.classList.remove("shown")
	}, 5000);
}

Event.add(window, "load", () => {
	Event.add(window, "mousedown", () => {
		if (!interacted) {
			html.classList.add("interacted");
			trigger_event(html, "interacted");
			interacted = true;
		}
	});
	setTimeout(() => {html.classList.add("loaded")}, 50);

	Event.add(menu_button, "mousedown", () => {
		menu.classList.toggle("shown");
		menu_button.classList.toggle("active", menu.classList.contains("shown"));
	});

	for (let menu_link of menu_links) {
		Event.add(menu_link, "click", () => {
			html.classList.remove("dark_mode_transition");
			html.classList.add("wait");
			html.classList.remove("loaded");
		});
	}

	Event.add(dark_mode_button, "mousedown", () => {
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

				// setTimeout(() => {html.classList.remove("dark_mode_transition")}, dark_mode_transition_timeout);
			},
			(req) => {
				html.classList.remove("wait");
				alert("Error");
				alert(req.responseText);
			}
		);
	});

	Event.add(logout_button, "click", () => {
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