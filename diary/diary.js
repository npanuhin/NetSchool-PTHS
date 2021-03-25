var
	diary = document.getElementsByClassName("diary")[0],
	table = diary.getElementsByTagName("table")[0];

if (table !== undefined) {

var
	scroll_table = document.querySelector(".diary > div > div"),

	tasks = table.querySelectorAll("tr:not(:nth-child(1)):not(:nth-child(2)) td span"),
	// table_tr = table.getElementsByTagName("tr"),

	left_column = document.querySelector(".diary > div > ul:first-child"),
	// left_column_li = left_column.getElementsByTagName("li"),
	
	right_column = document.querySelector(".diary > div > ul:last-child"),
	right_column_li = right_column.getElementsByTagName("li"),

	scroll_left_button = diary.getElementsByClassName("scroll_left")[0],
	scroll_right_button = diary.getElementsByClassName("scroll_right")[0],

	period_start_input = diary.getElementsByClassName("period_start")[0],
	period_end_input = diary.getElementsByClassName("period_end")[0],

	period_hidden_left = diary.getElementsByClassName("period_hidden_left")[0],
	period_hidden_right = diary.getElementsByClassName("period_hidden_right")[0],

	table_unlocked = false,

	details_block = document.getElementsByClassName("details")[0],
	basic_details_content = details_block.innerHTML,
	details_block_link_icon = details_block.getElementsByClassName("link-icon")[0],
	details_lock = false,
	details_distance = 20,

	current_task = null,
	mark_count = [],
	graph_colors = {
		1: "hsla(0, 50%, 70%)", 
		2: "hsla(35, 50%, 70%)", 
		3: "hsla(60, 50%, 70%)",
		4: "hsla(180, 50%, 70%)",
		5: "hsla(120, 50%, 70%)"
	};


// =======================================================================

function set_current_task_url(task) {
	let url = new URL(window.location.href);
	url.hash = task.id;
	set_url(url.href);
}

function copy_to_clipboard(text) {
	if (navigator.clipboard === undefined) {
		ui_alert("Мы не можем вставить ссылку в буфер обмена.<br>Пожалуйста, скопируйте URL самостоятельно");

	} else {
		navigator.clipboard.writeText(text)
		.then(() => {
			console.log("Copied to clipboard:", text);
			ui_alert("Ссылка скопирована в буфер обмена");
		})
		.catch(err => {
			console.log(err);
			ui_alert("Мы не можем вставить ссылку в буфер обмена.<br>Пожалуйста, скопируйте URL самостоятельно");
		});
	}
}

function copy_url() {
	copy_to_clipboard(decodeURI(window.location.href));
}

// =======================================================================


function show_details(windowX, windowY, task) {
	if (details_lock) return;

	set_current_task_url(task);

	Event.remove(details_block_link_icon, "click", copy_url);

	details_block.innerHTML = basic_details_content + task.getElementsByTagName("div")[0].innerHTML;

	details_block_link_icon = details_block.getElementsByClassName("link-icon")[0];
	Event.add(details_block_link_icon, "click", copy_url);

	details_block.classList.toggle("expired", task.classList.contains("expired"));
	details_block.classList.remove("graph");

	locate_details(windowX, windowY);
	details_block.classList.add("shown");
}

function show_graph_details(windowX, windowY, line_index) {
	if (details_lock) return;
	Event.remove(details_block_link_icon, "click", copy_url);

	details_block.classList.remove("expired");
	details_block.classList.add("graph");
	
	let marks = mark_count[line_index - 2];
	if (!marks) return;
	
	for (let mark in marks) {
		if (marks[mark] === 0) delete marks[mark];
	}
	let keys = Object.keys(marks),
		values = Object.values(marks);
	
	if (keys.length === 0) return; // no marks — no graph
	
	let dataset = {
		labels: keys,
		datasets: [{
			data: values,
			backgroundColor: (context) => {return graph_colors[Number(keys[context.dataIndex])]},
			// borderColor: "hsla(0, 0%, 40%)",
			borderWidth: 1
		}]
	};
	
	// details_block.innerHTML = '<canvas id="mark_dispersion_canvas"></canvas>'
	// let canvas = document.getElementById("mark_dispersion_canvas");

	details_block.innerHTML = "";

	let canvas = document.createElement("canvas");
	canvas.id = "mark_dispersion_canvas";
	details_block.append(canvas);
	
	window.graph = new Chart(canvas, {
		type: 'bar', 
		data: dataset,
		options: {
			responsive: true,
			legend: {
				display: false
			},
			animation: {
				easing: "easeInOutQuad",
				duration: 500
			},
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true,
						precision: 0,
						maxTicksLimit: 4,

						fontFamily: "Manrope",
						fontColor: (html.classList.contains("dark") ? text_color_dark : text_color),
						// fontStyle: "bold"
					},
					gridLines: {
						color: '#ccc'
					}
				}],
				xAxes: [{
					ticks: {
						fontColor: (html.classList.contains("dark") ? text_color_dark : text_color)
					},
					gridLines: {
						color: 'transparent'
					}
				}],
			}
		}
	});

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

function hide_details(force=false) {
	if (!force && details_lock) return;
	details_lock = false;

	clear_url_hash();
	details_block.classList.remove("shown");
	Event.remove(details_block_link_icon, "click", copy_url);
}

function toggle_details_lock(event) {
	let empty_click = true;
	
	for (let task of tasks) {
		if (task.contains(event.target)) {
			details_lock = false;
			show_details(event.pageX - html.scrollLeft, event.pageY - html.scrollTop, task);
			set_current_task_url(task);
			details_lock = true;

			empty_click = false;
			break;
		}
	}

	if (empty_click) {
		for (let line_index = 0; line_index < right_column_li.length; ++line_index){
			let avg_mark_tile = right_column_li[line_index];
			if (avg_mark_tile.contains(event.target)) {
				details_lock = false;
				show_graph_details(event.pageX - html.scrollLeft, event.pageY - html.scrollTop, line_index);
				details_lock = true;

				empty_click = false;
				break;
			}
		}
	}

	if (empty_click && !details_block.contains(event.target)) {
		details_lock = false;
		hide_details();
	}
}

// =======================================================================


function onhashchange() {
	let url_hash = decodeURIComponent(window.location.hash);

	if (url_hash) {
		let task_element = document.getElementById(url_hash.slice(1)),
			day = task_element.parentElement.parentElement;

		day.classList.add("selected");

		details_lock = false;
		show_details(
			(task_element.getBoundingClientRect().left + task_element.getBoundingClientRect().right) / 2,
			(task_element.getBoundingClientRect().bottom + task_element.getBoundingClientRect().top) / 2,
			task_element
		);

		(function initial_scroll_table() {
			if (table_unlocked) {
				details_lock = true;
				return;
			}
			
			scroll_table.scrollLeft = day.offsetLeft - scroll_table.offsetWidth / 2 + day.offsetWidth / 2;
			locate_details(
				(task_element.getBoundingClientRect().left + task_element.getBoundingClientRect().right) / 2,
				(task_element.getBoundingClientRect().bottom + task_element.getBoundingClientRect().top) / 2
			);

			requestAnimationFrame(initial_scroll_table);
		})();

	} else {
		(function initial_scroll_table() {
			if (table_unlocked) return;
			scroll_table.scrollLeft = scroll_table.scrollWidth;
			requestAnimationFrame(initial_scroll_table);
		})();
	}
}

// =======================================================================


function ScrollParametricBlend(t) {
	return t * t / (1.85 * t * (t - 1) + 1.0);
}

function scroll_table_to(target) {
	let steps_count = 45,
		step_length = 1 / steps_count,
		length = target - scroll_table.scrollLeft;

	(function scrollStep() {
		scroll_table.scrollLeft = target - ScrollParametricBlend(steps_count * step_length) * length;
		if (steps_count <= 0) {
			scroll_animation = false;
			return;
		}
		--steps_count;
		requestAnimationFrame(scrollStep);
	})()
}

function scroll_table_by(distance) {scroll_table_to(Math.min(scroll_table.scrollWidth - scroll_table.offsetWidth, Math.max(0, scroll_table.scrollLeft + distance)))}

// =======================================================================


function on_table_scroll() {
	scroll_left_button.classList.toggle("hidden", scroll_table.scrollLeft <= 2);
	scroll_right_button.classList.toggle("hidden", scroll_table.scrollLeft >= scroll_table.scrollWidth - scroll_table.offsetWidth - 2);

	left_column.classList.toggle("shadow", scroll_table.scrollLeft > 2);
	right_column.classList.toggle("shadow", scroll_table.scrollLeft < scroll_table.scrollWidth - scroll_table.offsetWidth - 2);
}

// =======================================================================

function apply_period(save=false) {
	if (!period_start_input.validity.valid || !period_end_input.validity.valid) return;

	let period_start = period_start_input.value,
		period_end = period_end_input.value,

		left_border = scroll_table.scrollWidth, right_border = 0;

	if (!period_start) {
		period_start = period_start_input.dataset.default;
		period_start_input.value = period_start;
	}
	if (!period_end) {
		period_end = period_end_input.dataset.default;
		period_end_input.value = period_end;
	}

	period_start = new Date(period_start);
	period_end = new Date(period_end);

	for (let tr_index = 2; tr_index < table.getElementsByTagName("tr").length; ++tr_index) {
		mark_count.push({1: 0, 2: 0, 3: 0, 4: 0, 5: 0})
		let line = table.getElementsByTagName("tr")[tr_index],
			average_mark = 0, rate_summ = 0;

		for (let day of line.getElementsByTagName("td")) {

			let date;

			for (let obj_class of day.classList) {
				date = new Date(obj_class);
				if (date instanceof Date && !isNaN(date)) break;
			}

			if (period_start <= date && date <= period_end) {
				left_border = Math.min(left_border, day.offsetLeft);
				right_border = Math.max(right_border, day.offsetLeft + day.offsetWidth);

				for (let task of day.getElementsByTagName("span")) {
					if (task.dataset.mark && task.dataset.rate) {
						mark_count[mark_count.length - 1][Number(task.dataset.mark)]++;
						average_mark += Number(task.dataset.mark) * Number(task.dataset.rate);
						rate_summ += Number(task.dataset.rate);
					}
				}
			}
		}

		if (rate_summ && average_mark) {
			average_mark = Math.round((average_mark / rate_summ - 0.001) * 100) / 100;
			right_column_li[tr_index].innerHTML = average_mark;
		} else {
			right_column_li[tr_index].innerHTML = "-";
		}

		for (let day of line.getElementsByTagName("td")) {
			// if (!day.classList.contains("filled") && !day.classList.contains("today")) continue;

			let date;

			for (let obj_class of day.classList) {
				date = new Date(obj_class);
				if (date instanceof Date && !isNaN(date)) break;
			}

			if (period_start <= date && date <= period_end) {
				for (let task of day.getElementsByTagName("span")) {
					task.classList.toggle("high", task.dataset.mark && task.dataset.mark > average_mark);
				}
			} else {
				for (let task of day.getElementsByTagName("span")) {
					task.classList.remove("high");
				}
			}
		}
	}

	period_hidden_left.style.width = left_border + "px";
	period_hidden_right.style.width = scroll_table.scrollWidth - right_border - 1 + "px";
	period_hidden_right.style.left = right_border + "px";

	if (save) {
		ajax(
			"POST",
			"/src/set_diary_period.php",
			{
				"period_start": period_start.getFullYear() + '-' + (period_start.getMonth() + 1) + '-' + period_start.getDate(),
				"period_end": period_end.getFullYear() + '-' + (period_end.getMonth() + 1) + '-' + period_end.getDate()
			},
			(req) => {
				if (req.responseText == "success") {
					console.log("Period saved");

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
	}
}


// =======================================================================

Event.add(window, "resize", () => {apply_period});
// onResize();

for (let task of tasks) {
	Event.add(task, "mouseenter", (e) => {
		show_details(e.pageX - html.scrollLeft, e.pageY - html.scrollTop, task);
	});
	Event.add(task, "mouseleave", (e) => {
		if (!details_block.contains(e.relatedTarget)) hide_details();
	});
	Event.add(task, "mousemove", (e) => {
		locate_details(e.pageX - html.scrollLeft, e.pageY - html.scrollTop);
	});
}
for (let line_index = 0; line_index < right_column_li.length; ++line_index){
	let avg_mark_tile = right_column_li[line_index];
	Event.add(avg_mark_tile, "mouseenter", (e) => {
		show_graph_details(e.pageX - html.scrollLeft, e.pageY - html.scrollTop, line_index);
	});
	Event.add(avg_mark_tile, "mouseleave", (e) => {
		if (!details_block.contains(e.relatedTarget)) hide_details();
	});
	Event.add(avg_mark_tile, "mousemove", (e) => {
		locate_details(e.pageX - html.scrollLeft, e.pageY - html.scrollTop);
	});
}
Event.add(window, "mousedown", toggle_details_lock);
Event.add(details_block, "mouseleave", hide_details);
Event.add(window, "scroll", () => {hide_details(true)});
Event.add(scroll_table, "scroll", () => {hide_details(true)});


// Table scroll controls
Event.add(scroll_left_button, "mousedown", () => {
	scroll_table_by(-Math.round(0.8 * scroll_table.offsetWidth));
});
Event.add(scroll_right_button, "mousedown", () => {
	scroll_table_by(Math.round(0.8 * scroll_table.offsetWidth));
});

// Period
Event.add(period_start_input, "change", () => {
	apply_period(true);
});
Event.add(period_end_input, "change", () => {
	apply_period(true);
});
// setTimeout(apply_period);
setTimeout(apply_period, 100);
// setTimeout(apply_period, 200);
setTimeout(apply_period, 300);
setTimeout(apply_period, 500);

// Table scroll handler
setTimeout(on_table_scroll);
setTimeout(() => {
	table_unlocked = true;
	Event.add(scroll_table, "scroll", on_table_scroll);
	setTimeout(apply_period);
}, 150);

// Hash
Event.add(window, "hashchange", onhashchange);

if (decodeURIComponent(window.location.hash)) {
	setTimeout(onhashchange);

} else {
	function initial_table_scroll() {
		if (table_unlocked) return;
		scroll_table.scrollLeft = scroll_table.scrollWidth;
		on_table_scroll();
		requestAnimationFrame(initial_table_scroll);
	}
	setTimeout(initial_table_scroll);
}

}
