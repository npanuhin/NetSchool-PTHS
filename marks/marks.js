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
	table_unlocked = false;


// function onResize() {
// 	// left_column.style.height = weeks[cur_week].offsetHeight + "px";
// 	for (let i = 0; i < table_tr.length; ++i) {
// 		left_column_li[i].style.paddingTop = (40 - 28) / 2 + "px";
// 		left_column_li[i].style.paddingBottom = (40 - 28) / 2 + "px";
// 	}
// }

function show_details(task_element) {
	let
		name = task_element.dataset.name,
		tasktype = task_element.dataset.tasktype,
		mark_rate = task_element.dataset.mark_rate,
		task_expired = task_element.dataset.task_expired,
		details = [];

	details_block.innerHTML = "";

	if (name) details_block.innerHTML += "<h5>" + name + "</h5>";
	// if (task_expired !== undefined) details_block.innerHTML += "<h6>Задание просрочено</h6>";

	if (tasktype) details.push("Тип: " + tasktype);
	if (mark_rate) details.push("Вес: " + mark_rate);
	details_block.innerHTML += details.join('<br>');

	details_block.classList.toggle("expired", task_expired !== undefined)
	details_block.classList.add("shown");
}

function locate_details(pageX, pageY) {
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
	details_block.classList.remove("shown");
}

Event.add(window, "load", () => {
	// Event.add(window, "resize", onResize);
	// onResize();

	let location_hash = decodeURIComponent(window.location.hash);

	if (location_hash) {
		let task = document.getElementById(location_hash.slice(1)),
			day = task.parentElement.parentElement;

		day.classList.add("selected");

		show_details(task);

		(function scroll_table() {
			if (table_unlocked) return;
			
			table.scrollLeft = day.offsetLeft - table.offsetWidth / 2 + day.offsetWidth / 2;
			locate_details(
				task.getBoundingClientRect().left + html.scrollLeft + 20,
				task.getBoundingClientRect().top + html.scrollTop + 10
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

	// function update_shadows() {
	// 	left_column.classList.toggle("shadow", table.scrollLeft > 0);
	// 	right_column.classList.toggle("shadow", table.scrollLeft + table.offsetWidth < table.scrollWidth);
	// }
	// update_shadows();

	setTimeout(() => {
		table_unlocked = true;
		// Event.add(table, "scroll", update_shadows);
	}, 150);

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
	}
});