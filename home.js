var
	html = document.getElementsByTagName("html")[0],
	logout_button = document.getElementsByClassName("exit_icon")[0],
	timetable = document.getElementsByClassName("timetable")[0],
	timetable_previous = document.getElementById("timetable_previous"),
	timetable_next = document.getElementById("timetable_next"),
	weeks = timetable.getElementsByClassName("week"),
	cur_week = Array.prototype.slice.call(weeks).indexOf(
		timetable.getElementsByClassName("shown")[0]
	),
	goto_today = timetable.getElementsByClassName("goto_today");

Event.add(window, "load", () => {

	Event.add(window, "resize", () => {
		timetable.style.height = weeks[cur_week].offsetHeight + "px";
	});
	timetable.style.height = weeks[cur_week].offsetHeight + "px";

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
});