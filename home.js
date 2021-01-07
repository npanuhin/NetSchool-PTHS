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
	days = timetable.getElementsByClassName("day"),

	today = timetable.getElementsByClassName("today")[0],
	today_date = Array.prototype.slice.call(today.classList).find((value) => {
		return value != "day" && value != "today"
	}),

	goto_today_buttons = timetable.getElementsByClassName("goto_today");


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

	for (let goto_today_button of goto_today_buttons) {
		Event.add(goto_today_button, "click", () => {
			goto_day(today_date);
		});
	}

	for (let day of days) {
		for (let item of day.getElementsByTagName("li")) {
			Event.add(item, "mousemove", (e) => {
				console.log(html.scrollTop);
				item.getElementsByClassName("details")[0].style.top = e.pageY - (html.scrollTop + item.getBoundingClientRect().top) + 20 + "px";

				item.getElementsByClassName("details")[0].style.left = Math.min(
					e.pageX - item.getBoundingClientRect().left + 20,
					item.getBoundingClientRect().right
				) + "px";
			});
		}
	}
});