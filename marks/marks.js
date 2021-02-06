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

Event.add(window, "load", () => {
	// Event.add(window, "resize", onResize);
	// onResize();

	(function scroll_table() {
		if (table_unlocked) return;
		table.scrollLeft = table.scrollWidth;
		requestAnimationFrame(scroll_table);
	})();

	// function update_shadows() {
	// 	left_column.classList.toggle("shadow", table.scrollLeft > 0);
	// 	right_column.classList.toggle("shadow", table.scrollLeft + table.offsetWidth < table.scrollWidth);
	// }
	// update_shadows();

	setTimeout(() => {
		table_unlocked = true;
		// Event.add(table, "scroll", update_shadows);
	}, 100);

	for (let item of spans) {
		
		Event.add(item, "mouseenter", (e) => {
			let
				name = item.dataset.name,
				tasktype = item.dataset.tasktype,
				mark_rate = item.dataset.mark_rate,
				details = [];

			details_block.innerHTML = "";

			if (name) details_block.innerHTML += "<h5>" + name + "</h5>";

			if (tasktype) details.push("Тип: " + tasktype);
			if (mark_rate) details.push("Вес: " + mark_rate);
			details_block.innerHTML += details.join('<br>');

			details_block.classList.add("shown");
		});

		Event.add(item, "mouseleave", (e) => {
			details_block.classList.remove("shown");
		});

		Event.add(item, "mousemove", (e) => {

			details_block.style.top = Math.min(
				
				(window.pageYOffset || document.scrollTop || 0) - (document.clientTop || 0) + document.documentElement.clientHeight - details_block.offsetHeight - 20,
				
				e.pageY
			) - html.scrollTop + 20 + "px";

			details_block.style.left = Math.min(
				
				(window.pageXOffset || document.scrollLeft || 0) - (document.clientLeft || 0) + document.documentElement.clientWidth - details_block.offsetWidth - 20,
				
				e.pageX
			) + 20 + "px";

		});
	}
});