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

	lessons = document.querySelectorAll(".timetable > div > div ul li"),

	// today = timetable.getElementsByClassName("today")[0],
	// today_date = Array.prototype.slice.call(today.classList).find((value) => {
	// 	return value != "day" && value != "today"
	// }),

	goto_today_buttons = timetable.getElementsByTagName("button"),

	details_block = document.getElementsByClassName("details")[0],
	details_lock = false;


function onResize() {
	timetable.style.height = weeks[cur_week].offsetHeight + "px";
}

// ===========================================================================================================

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

// ===========================================================================================================

function show_details(pageX, pageY, element) {
	if (details_lock) return;

	locate_details(pageX, pageY);

	// details_block.style.display = "";

	// element.append(details_block);

	details_block.innerHTML = element.getElementsByTagName("div")[0].innerHTML;
	details_block.classList.add("shown");
}

function locate_details(pageX, pageY) {
	if (details_lock) return;

	details_block.style.top = Math.min(
		document.documentElement.clientHeight - details_block.offsetHeight,
		pageY - html.scrollTop + 20
	) + "px";

	details_block.style.left = Math.min(
		document.documentElement.clientWidth - details_block.offsetWidth,
		pageX - html.scrollLeft + 20
	) + "px";
}

function hide_details() {
	if (details_lock) return;

	details_block.classList.remove("shown");

	// setTimeout(() => {
	// 	if (!details_block.classList.contains("shown")) details_block.style.display = "none";
	// }, 200);
}

function toggle_details_lock(event) {
	let empty_click = true;
	for (let lesson of lessons) {
		if (lesson.contains(event.target)) {
			details_lock = false;
			show_details(event.pageX, event.pageY, lesson);

			details_lock = true;
			empty_click = false;
			break;
		}
	}

	if (empty_click && !details_block.contains(event.target)) {
		details_lock = false;
		hide_details();
	}
}

// ===========================================================================================================

Event.add(window, "load", () => {

	Event.add(window, "resize", onResize);
	setTimeout(onResize());

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

	body.append(details_block);
	for (let lesson of lessons) {
		let details = lesson.getElementsByTagName("div")[0];

		if (details !== undefined) {

			Event.add(lesson, "mouseenter", (e) => {
				show_details(e.pageX, e.pageY, lesson);
			});
			Event.add(lesson, "mouseleave", (e) => {
				if (!details_block.contains(e.relatedTarget)) hide_details();
			});
			Event.add(lesson, "mousemove", (e) => {
				locate_details(e.pageX, e.pageY);
			});
		}
	}
	Event.add(window, "mousedown", toggle_details_lock);
	Event.add(details_block, "mouseleave", hide_details);
});