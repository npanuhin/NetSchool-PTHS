var
	html = document.getElementsByTagName("html")[0],
	main = document.getElementsByTagName("main")[0],

	timetable = document.getElementsByClassName("timetable")[0],
	timetable_previous = document.getElementById("timetable_previous"),
	timetable_next = document.getElementById("timetable_next"),

	weeks = document.querySelectorAll(".timetable > div"),
	today_week = Array.prototype.slice.call(weeks).indexOf(
		timetable.getElementsByClassName("cur_week")[0]
	),
	cur_week = Array.prototype.slice.call(weeks).indexOf(
		timetable.getElementsByClassName("shown")[0]
	),
	days = document.querySelectorAll(".timetable > div > div"),

	// today = timetable.getElementsByClassName("today")[0],
	// today_date = Array.prototype.slice.call(today.classList).find((value) => {
	// 	return value != "day" && value != "today"
	// }),

	goto_today_buttons = timetable.getElementsByTagName("button");



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

	Event.add(window, "resize", onResize);
	onResize();

	Event.add(timetable_previous, "mousedown", () => {
		html.classList.add("loaded");

		if (!timetable_previous.classList.contains("hidden")) {
			for (let week of weeks) week.classList.remove("shown");
			weeks[--cur_week].classList.add("shown");

			timetable_previous.classList.toggle("hidden", cur_week <= 0);
			timetable_next.classList.toggle("hidden", cur_week >= weeks.length - 1);

			timetable.style.height = weeks[cur_week].offsetHeight + "px";
		}
	});

	Event.add(timetable_next, "mousedown", () => {
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
		Event.add(goto_today_button, "mousedown", () => {
			goto_week(today_week);
		});
	}

	for (let day of days) {
		for (let item of day.getElementsByTagName("li")) {
			let details = item.getElementsByTagName("div")[0];

			if (details !== undefined) {
				Event.add(item, "mousemove", (e) => {

					details.style.top = Math.min(
						document.documentElement.clientHeight - details.offsetHeight,
						e.pageY - html.scrollTop + 20
					) + "px";

					details.style.left = Math.min(
						document.documentElement.clientWidth - details.offsetWidth,
						e.pageX - html.scrollLeft + 20
					) + "px";

				});
			}
		}
	}
});