var currentDate = new Date(),
	today = currentDate.getDate() + '.' + (currentDate.getMonth() + 1) + '.' + currentDate.getFullYear(),

	zoom_lessons = document.getElementsByClassName("zoom_lessons")[0],
	zoom_courses = document.getElementsByClassName("zoom_courses")[0];


function tablify_edu(json){
	s = `<tr>
			<th>Время</th>
			<th>Учитель</th>
			<th>Занятие</th>
			<th>Ссылка</th>
		</tr>`;
		
	for (let i in json){
		s += "<tr>";

		let lesson = json[i];
		s += "<td>" + lesson["time"]    + "</td>";
		s += "<td>" + lesson["teacher"] + "</td>";
		s += "<td>" + lesson["name"]    + "</td>";
		
		s += "<td class=\"center\">";
		if (lesson["href"]) {
			s += "<a href=\"" + lesson["href"] + "\" target=\"_blank\">Zoom</a>";
		}
		s += "</td>";

		s += "</tr>";
		// console.log(lesson);
	}
	return s;
}

function update_edu(){
	ajax(
		'POST',
		"../src/edu_request.php",
		{
			'day': today,
			'courses': 0
		},
		(req) => {
			// console.log(req.responseText);
			if (!req.responseText.trim()) return;

			let json = JSON.parse(req.responseText),
				new_table = document.createElement("table");
				
			if (json.length) {
				new_table.innerHTML = tablify_edu(json);

				zoom_lessons.replaceChild(new_table, zoom_lessons.getElementsByTagName("table")[0]);
				
				zoom_lessons.classList.add("shown");
			}
		},
		(req) => {
			console.log(req);
		}
	);

	ajax(
		'POST',
		"../src/edu_request.php",
		{
			'day': today,
			'courses': 1
		},
		(req) => {
			// console.log(req.responseText);
			if (!req.responseText.trim()) return;

			let json = JSON.parse(req.responseText),
				new_table = document.createElement("table");
			if (json.length) {
				new_table.innerHTML = tablify_edu(json);

				zoom_courses.replaceChild(new_table, zoom_courses.getElementsByTagName("table")[0]);
				zoom_courses.classList.add("shown");
			}
			
		},
		(req) => {
			console.log(req);
		}
	);
}

update_edu();

Event.add(window, "load", () => {
	setInterval(update_edu, 10_000);
});
