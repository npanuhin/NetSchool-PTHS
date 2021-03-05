var
	marks = document.getElementsByClassName("marks")[0],
	table = marks.getElementsByTagName("table")[0];

if (table !== undefined) {

let
	scroll_table = document.querySelector(".marks > div > div"),

	spans = table.querySelectorAll("tr:not(:nth-child(1)):not(:nth-child(2)) td span"),
	table_tr = table.getElementsByTagName("tr"),

	left_column = document.querySelector(".marks > div > ul:first-child"),
	// left_column_li = left_column.getElementsByTagName("li"),
	
	right_column = document.querySelector(".marks > div > ul:last-child"),
	right_column_li = right_column.getElementsByTagName("li"),

	scroll_left_button = marks.getElementsByClassName("scroll_left")[0],
	scroll_right_button = marks.getElementsByClassName("scroll_right")[0],

	period_start_input = marks.getElementsByClassName("period_start")[0],
	period_end_input = marks.getElementsByClassName("period_end")[0],

	period_hidden_left = marks.getElementsByClassName("period_hidden_left")[0],
	period_hidden_right = marks.getElementsByClassName("period_hidden_right")[0],

	table_unlocked = false,

	details_block = document.getElementsByClassName("details")[0],
	basic_details_content = details_block.innerHTML,
	details_block_link_icon = details_block.getElementsByClassName("link-icon")[0],
	details_lock = false,

	current_task = null;


// function onResize() {
// 	// left_column.style.height = weeks[cur_week].offsetHeight + "px";
// 	for (let i = 0; i < table_tr.length; ++i) {
// 		left_column_li[i].style.paddingTop = (40 - 28) / 2 + "px";
// 		left_column_li[i].style.paddingBottom = (40 - 28) / 2 + "px";
// 	}
// }

// =======================================================================


function set_url(url) {
	window.history.replaceState({"Title": document.title, "Url": url}, document.title, url);
}

function set_current_task_url(task) {
	let url = new URL(window.location.href);
	url.hash = task.id;
	set_url(url.href);
}

function copy_to_clipboard(text) {
	if (navigator.clipboard === undefined) {
		alert("Мы не можем вставить ссылку в буфер обмена.\nПожалуйста, скопируйте URL самостоятельно");
	} else {
		navigator.clipboard.writeText(text)
		.then(() => {
			console.log("Copied to clipboard:", text);
		})
		.catch(err => {
			// console.log(err);
			alert("Мы не можем вставить ссылку в буфер обмена.\nПожалуйста, скопируйте URL самостоятельно");
  		});
	}
}

function copy_url() {
	copy_to_clipboard(window.location.href);
}

function show_details(pageX, pageY, task) {
	if (details_lock) return;

	set_current_task_url(task);

	locate_details(pageX, pageY);

	// details_block.style.display = "";
	// task.append(details_block);

	Event.remove(details_block_link_icon, "click", copy_url);

	details_block.innerHTML = basic_details_content + task.getElementsByTagName("div")[0].innerHTML;

	details_block_link_icon = details_block.getElementsByClassName("link-icon")[0];
	Event.add(details_block_link_icon, "click", copy_url);

	details_block.classList.toggle("expired", task.classList.contains("expired"));
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
	for (let span of spans) {
		if (span.contains(event.target)) {
			details_lock = false;
			show_details(event.pageX, event.pageY, span);
			set_current_task_url(span);

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

// =======================================================================


function onhashchange() {
	let url_hash = decodeURIComponent(window.location.hash);

	if (url_hash) {
		let task_element = document.getElementById(url_hash.slice(1)),
			day = task_element.parentElement.parentElement;

		day.classList.add("selected");

		details_lock = false;
		show_details(
			task_element.getBoundingClientRect().left + html.scrollLeft + 20,
			task_element.getBoundingClientRect().top + html.scrollTop + 10,
			task_element
		);

		(function initial_scroll_table() {
			if (table_unlocked) {
				details_lock = true;
				return;
			}
			
			scroll_table.scrollLeft = day.offsetLeft - scroll_table.offsetWidth / 2 + day.offsetWidth / 2;
			locate_details(
				task_element.getBoundingClientRect().left + html.scrollLeft + 20,
				task_element.getBoundingClientRect().top + html.scrollTop + 10
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


function apply_period(safe=false) {
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
		let line = table.getElementsByTagName("tr")[tr_index],
			average_mark = 0, rate_summ = 0;

		for (let day of line.getElementsByTagName("td")) {
			if (!day.classList.contains("filled")) continue;

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
			if (!day.classList.contains("filled")) continue;

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

	if (safe) {
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


Event.add(window, "load", () => {
	// Event.add(window, "resize", onResize);
	// onResize();

	Event.add(window, "hashchange", onhashchange);

	let url_hash = decodeURIComponent(window.location.hash);
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

	// body.append(details_block);
	for (let item of spans) {
		Event.add(item, "mouseenter", (e) => {
			show_details(e.pageX, e.pageY, item);
		});
		Event.add(item, "mouseleave", (e) => {
			if (!details_block.contains(e.relatedTarget)) hide_details();
		});
		Event.add(item, "mousemove", (e) => {
			locate_details(e.pageX, e.pageY);
		});
	}
	Event.add(window, "mousedown", toggle_details_lock);
	Event.add(details_block, "mouseleave", hide_details);


	Event.add(scroll_left_button, "mousedown", () => {
		scroll_table_by(-Math.round(0.8 * scroll_table.offsetWidth));
	});
	Event.add(scroll_right_button, "mousedown", () => {
		scroll_table_by(Math.round(0.8 * scroll_table.offsetWidth));
	});

	Event.add(period_start_input, "change", () => {
		apply_period(true);
	});
	Event.add(period_end_input, "change", () => {
		apply_period(true);
	});
	setTimeout(apply_period);

	setTimeout(on_table_scroll);
	setTimeout(() => {
		table_unlocked = true;
		Event.add(scroll_table, "scroll", on_table_scroll);
		setTimeout(apply_period);
	}, 150);
});

}