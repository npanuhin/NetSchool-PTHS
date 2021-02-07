var
	marks = document.getElementsByClassName("marks")[0],
	table = marks.getElementsByTagName("table")[0],
	spans = table.querySelectorAll("tr:not(:nth-child(1)):not(:nth-child(2)) td span"),
	table_tr = table.getElementsByTagName("tr"),
	left_column = document.querySelector(".marks > div > ul:first-child"),
	left_column_li = left_column.getElementsByTagName("li"),
	right_column = document.querySelector(".marks > div > ul:last-child"),
	right_column_li = right_column.getElementsByTagName("li"),
	details_block = marks.getElementsByClassName("details")[0],
	table_unlocked = false,
	details_lock = false;


// function onResize() {
// 	// left_column.style.height = weeks[cur_week].offsetHeight + "px";
// 	for (let i = 0; i < table_tr.length; ++i) {
// 		left_column_li[i].style.paddingTop = (40 - 28) / 2 + "px";
// 		left_column_li[i].style.paddingBottom = (40 - 28) / 2 + "px";
// 	}
// }

function show_details(task_element) {
	if (details_lock) return;

	let
		name = task_element.dataset.name,
		task = task_element.dataset.task,
		tasktype = task_element.dataset.tasktype,
		mark_rate = task_element.dataset.mark_rate,
		task_expired = task_element.dataset.task_expired,
		details = [];

	// task_element.append(details_block);

	details_block.innerHTML = "";

	if (name) details_block.innerHTML += "<h5>" + name + "</h5>";
	// if (task_expired !== undefined) details_block.innerHTML += "<h6>Задание просрочено</h6>";

	// if (task) details.push(task);
	if (tasktype) details.push("Тип: " + tasktype);
	if (mark_rate) details.push("Вес: " + mark_rate);

	details_block.innerHTML += details.join('<br>');

	for (let i = 0; ; ++i) {
		let key = task_element.getAttribute('data-extdata_key' + i),
			value = task_element.getAttribute('data-extdata_value' + i);

		if (!key || !value) break;

		if (i == 0) details_block.innerHTML += "<br>";

		details_block.innerHTML += key + ":<br><p>" + value + "</p>";
	}

	details_block.classList.toggle("expired", task_expired !== undefined)
	details_block.classList.add("shown");

	details_lock = false;
}

function locate_details(pageX, pageY) {
	if (details_lock) return;

	details_block.style.top = Math.min(
		
		(window.pageYOffset || document.scrollTop || 0) - (document.clientTop || 0) + document.documentElement.clientHeight - details_block.offsetHeight - 20,
		
		pageY
	) - html.scrollTop + 20 + "px";

	details_block.style.left = Math.min(
		
		(window.pageXOffset || document.scrollLeft || 0) - (document.clientLeft || 0) + document.documentElement.clientWidth - details_block.offsetWidth - 20,
		
		pageX
	) + 20 + "px";
}

function hide_details() {
	if (details_lock) return;

	details_block.classList.remove("shown");
}

function toggle_details_lock(event) {
	let empty_click = true;
	for (let item of spans) {
		if (item == event.target) {
			details_lock = false;
			show_details(item);
			locate_details(event.pageX, event.pageY);

			details_lock = true;
			empty_click = false;
		}
	}

	if (empty_click && !details_block.contains(event.target)) {
		details_lock = false;
		hide_details();
	}
}

function onhashchange() {
	let url_hash = decodeURIComponent(window.location.hash);

	if (url_hash) {
		let task_element = document.getElementById(url_hash.slice(1)),
			day = task_element.parentElement.parentElement;

		day.classList.add("selected");

		details_lock = false;
		show_details(task_element);

		(function scroll_table() {
			if (table_unlocked) {
				details_lock = true;
				return;
			}
			
			table.scrollLeft = day.offsetLeft - table.offsetWidth / 2 + day.offsetWidth / 2;
			locate_details(
				task_element.getBoundingClientRect().left + html.scrollLeft + 20,
				task_element.getBoundingClientRect().top + html.scrollTop + 10
			);

			requestAnimationFrame(scroll_table);
		})();

	} else {
		(function scroll_table() {
			if (table_unlocked) return;
			table.scrollLeft = table.scrollWidth;
			requestAnimationFrame(scroll_table);
		})();
	}
}

Event.add(window, "load", () => {
	// Event.add(window, "resize", onResize);
	// onResize();

	let url_hash = decodeURIComponent(window.location.hash);

	if (decodeURIComponent(window.location.hash)) {
		onhashchange();

	} else {
		(function scroll_table() {
			if (table_unlocked) return;
			table.scrollLeft = table.scrollWidth;
			requestAnimationFrame(scroll_table);
		})();
	}

	Event.add(window, "hashchange", onhashchange);

	// function update_shadows() {
	// 	left_column.classList.toggle("shadow", table.scrollLeft > 0);
	// 	right_column.classList.toggle("shadow", table.scrollLeft + table.offsetWidth < table.scrollWidth);
	// }
	// update_shadows();

	for (let item of spans) {
		
		Event.add(item, "mouseenter", () => {
			show_details(item);
		});

		Event.add(item, "mouseleave", () => {
			hide_details();
		});

		Event.add(item, "mousemove", (e) => {
			locate_details(e.pageX, e.pageY);
		});

		// Event.add(item, "click", () => {toggle_details_lock(item)});
	}
	Event.add(window, "click", toggle_details_lock);

	setTimeout(() => {
		table_unlocked = true;
		// Event.add(table, "scroll", update_shadows);
	}, 150);
});