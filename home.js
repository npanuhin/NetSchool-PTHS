var
	html = document.getElementsByTagName("html")[0],
	main = document.getElementsByTagName("main")[0],

	menu_button = document.getElementsByClassName("menu_icon_wrapper")[0],
	logout_button = document.getElementsByClassName("exit_icon")[0],

	menu = document.getElementsByClassName("menu")[0],
	menu_closing_timout,

	tasks_block = document.getElementsByClassName("tasks")[0],
	
	timetable = document.getElementsByClassName("timetable")[0],
	timetable_previous = document.getElementById("timetable_previous"),
	timetable_next = document.getElementById("timetable_next"),
	
	weeks = timetable.getElementsByClassName("week"),
	today_week = Array.prototype.slice.call(weeks).indexOf(
		timetable.getElementsByClassName("shown")[0]
	),
	cur_week = today_week;
	days = timetable.getElementsByClassName("day"),

	// today = timetable.getElementsByClassName("today")[0],
	// today_date = Array.prototype.slice.call(today.classList).find((value) => {
	// 	return value != "day" && value != "today"
	// }),

	goto_today_buttons = timetable.getElementsByClassName("goto_today");



function onResize() {
	timetable.style.height = weeks[cur_week].offsetHeight + "px";
}

function goto_day(date) {
	html.classList.add("loaded");

	cur_week = Array.prototype.slice.call(weeks).indexOf(
		timetable.getElementsByClassName(date)[0].parentNode.parentNode
	);

	for (let week of weeks) week.classList.remove("shown");
	weeks[cur_week].classList.add("shown");

	timetable_previous.classList.toggle("hidden", cur_week <= 0);
	timetable_next.classList.toggle("hidden", cur_week >= weeks.length - 1);

	timetable.style.height = weeks[cur_week].offsetHeight + "px";
}

function goto_week(week) {
	html.classList.add("loaded");

	cur_week = week;

	for (let week of weeks) week.classList.remove("shown");
	weeks[cur_week].classList.add("shown");

	timetable_previous.classList.toggle("hidden", cur_week <= 0);
	timetable_next.classList.toggle("hidden", cur_week >= weeks.length - 1);

	timetable.style.height = weeks[cur_week].offsetHeight + "px";
}



Event.add(window, "load", () => {

	main.style.minHeight = menu.clientHeight + "px";

	Event.add(window, "resize", onResize);
	onResize();

	Event.add(menu_button, "click", () => {
		html.classList.add("loaded");

		if (menu.classList.contains("shown")) {
			menu_button.classList.remove("active");
			menu.classList.remove("shown");
			tasks_block.style.transform = "translateY(0px)";
			timetable.style.transform = "translateY(0px)";

			menu_closing_timout = setTimeout(() => {
				main.style.minHeight = menu.clientHeight + "px";
			}, 300);

		} else {
			menu_button.classList.add("active");
			menu.classList.add("shown");
			tasks_block.style.transform = "translateY(" + menu.clientHeight + "px)";
			timetable.style.transform = "translateY(" + menu.clientHeight + "px)";

			clearTimeout(menu_closing_timout);
			main.style.minHeight = menu.clientHeight + "px";
			main.style.minHeight = main.clientHeight + menu.clientHeight + "px";
		}
	});

	Event.add(logout_button, "click", () => {
		html.classList.add("loaded");
		
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

	Event.add(timetable_previous, "click", () => {
		html.classList.add("loaded");

		if (!timetable_previous.classList.contains("hidden")) {
			for (let week of weeks) week.classList.remove("shown");
			weeks[--cur_week].classList.add("shown");

			timetable_previous.classList.toggle("hidden", cur_week <= 0);
			timetable_next.classList.toggle("hidden", cur_week >= weeks.length - 1);

			timetable.style.height = weeks[cur_week].offsetHeight + "px";
		}
	});

	Event.add(timetable_next, "click", () => {
		html.classList.add("loaded");

		if (!timetable_next.classList.contains("hidden")) {
			for (let week of weeks) week.classList.remove("shown");
			weeks[++cur_week].classList.add("shown");

			timetable_previous.classList.toggle("hidden", cur_week <= 0);
			timetable_next.classList.toggle("hidden", cur_week >= weeks.length - 1);

			timetable.style.height = weeks[cur_week].offsetHeight + "px";
		}
	});

	for (let goto_today_button of goto_today_buttons) {
		Event.add(goto_today_button, "click", () => {
			goto_week(today_week);
		});
	}

	for (let day of days) {
		for (let item of day.getElementsByTagName("li")) {
			let details = item.getElementsByClassName("details")[0];

			if (details !== undefined) {
				Event.add(item, "mousemove", (e) => {
					details.style.top = e.pageY - (html.scrollTop + item.getBoundingClientRect().top) + 20 + "px";
					details.style.left = e.pageX - item.getBoundingClientRect().left + 20 + "px";
				});
			}
		}
	}
});