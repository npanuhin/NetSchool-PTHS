var
	marks = document.getElementsByClassName("marks")[0],
	table_tr = marks.getElementsByTagName("table")[0].getElementsByTagName("tr"),
	left_column_li = document.querySelectorAll(".marks > div > ul:first-child > li"),
	right_column_li = document.querySelectorAll(".marks > div > ul:last-child > li");


function onResize() {
	// left_column.style.height = weeks[cur_week].offsetHeight + "px";
	for (let i = 0; i < table_tr.length; ++i) {
		left_column_li[i].style.paddingTop = (40 - 28) / 2 + "px";
		left_column_li[i].style.paddingBottom = (40 - 28) / 2 + "px";
	}
}

Event.add(window, "load", () => {
	Event.add(window, "resize", onResize);
	onResize();
});