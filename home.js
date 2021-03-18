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
	details_lock = false,
	details_distance = 20;


function onResize() {
	timetable.style.height = weeks[cur_week].offsetHeight + "px";
}

// ===========================================================================================================

function goto_day(date) {
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
	cur_week = week;

	for (let week of weeks) week.classList.remove("shown");
	weeks[cur_week].classList.add("shown");

	timetable_previous.classList.toggle("hidden", cur_week <= 0);
	timetable_next.classList.toggle("hidden", cur_week >= weeks.length - 1);

	timetable.style.height = weeks[cur_week].offsetHeight + "px";
}

// ===========================================================================================================

function show_details(windowX, windowY, element) {
	if (details_lock) return;

	details_block.innerHTML = element.getElementsByTagName("div")[0].innerHTML;
	
	details_block.classList.toggle("cur_lesson", element.classList.contains("cur_lesson"));
	details_block.classList.toggle("vacation", element.classList.contains("vacation"));
	
	locate_details(windowX, windowY);
	details_block.classList.add("shown");
}

function locate_details(windowX, windowY) {
	if (details_lock) return;

	if (
		windowY + details_distance + details_block.offsetHeight > document.documentElement.clientHeight &&
		windowY - details_distance - details_block.offsetHeight >= 0
	) {
		details_block.style.top = windowY - details_distance - details_block.offsetHeight + "px";

	} else {
		details_block.style.top = Math.min(
			document.documentElement.clientHeight - details_block.offsetHeight,
			windowY + details_distance
		) + "px";
	}

	if (
		windowX + details_distance + details_block.offsetWidth > document.documentElement.clientWidth &&
		windowX - details_distance - details_block.offsetWidth >= 0
	) {
		details_block.style.left = windowX - details_distance - details_block.offsetWidth + "px";

	} else {
		details_block.style.left = Math.min(
			document.documentElement.clientWidth - details_block.offsetWidth,
			windowX + details_distance
		) + "px";
	}
}

function hide_details() {
	if (details_lock) return;

	details_block.classList.remove("shown");
}

function toggle_details_lock(event) {
	let empty_click = true;
	for (let lesson of lessons) {
		if (lesson.contains(event.target)) {
			details_lock = false;
			show_details(event.pageX - html.scrollLeft, event.pageY - html.scrollTop, lesson);

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
		cur_week = Math.max(cur_week - 1, 0);

		for (let week of weeks) week.classList.remove("shown");
		weeks[cur_week].classList.add("shown");

		timetable_previous.classList.toggle("hidden", cur_week <= 0);
		timetable_next.classList.toggle("hidden", cur_week >= weeks.length - 1);

		timetable.style.height = weeks[cur_week].offsetHeight + "px";
	});

	Event.add(timetable_next, "mousedown", () => {
		cur_week = Math.min(cur_week + 1, weeks.length - 1);

		for (let week of weeks) week.classList.remove("shown");
		weeks[cur_week].classList.add("shown");

		timetable_previous.classList.toggle("hidden", cur_week <= 0);
		timetable_next.classList.toggle("hidden", cur_week >= weeks.length - 1);

		timetable.style.height = weeks[cur_week].offsetHeight + "px";
	});

	for (let goto_today_button of goto_today_buttons) {
		Event.add(goto_today_button, "mousedown", () => {
			goto_week(today_week);
		});
	}

	// body.append(details_block);
	for (let lesson of lessons) {
		let details = lesson.getElementsByTagName("div")[0];

		if (details !== undefined) {

			Event.add(lesson, "mouseenter", (e) => {
				show_details(e.pageX - html.scrollLeft, e.pageY - html.scrollTop, lesson);
			});
			Event.add(lesson, "mouseleave", (e) => {
				if (!details_block.contains(e.relatedTarget)) hide_details();
			});
			Event.add(lesson, "mousemove", (e) => {
				locate_details(e.pageX - html.scrollLeft, e.pageY - html.scrollTop);
			});
		}
	}
	Event.add(window, "mousedown", toggle_details_lock);
	Event.add(details_block, "mouseleave", hide_details);
});