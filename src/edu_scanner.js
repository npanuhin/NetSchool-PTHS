function tablify(json){
	s = "<li><table>";
	s += "<tr><th>Время</th><th>Учитель</th><th>Занятие</th><th>Ссылка</th></tr>"
	for (let i in json){
		//TODO: rewrite to table or smth like this
		let lesson = json[i];
		s += '<tr><td>'
		s += lesson["time"] + ""
		s += '</td><td>'
		s += lesson["teacher"] + ""
		s += '</td><td>'
		s += lesson["name"] + ""
		s += '</td><td>'
		if (lesson["href"]){
			s += '<a href = "' + lesson["href"] + '"> Zoom </a>'
		}
		s += '</td></tr>'
		//console.log(lesson);
	}
	s += '</table></li>'
	return s;
}
function zoom_main(r){
	console.log(r.responseText);
	var json = JSON.parse(r.responseText);
	if (json.length == 0){
		return;
	}
	document.getElementsByClassName('Zoom_main_calls')[0].innerHTML = tablify(json);
}
function zoom_cources(r){
	console.log(r.responseText);
	var json = JSON.parse(r.responseText);
	if (json.length == 0){
		return;
	}
	document.getElementsByClassName("Zoom_cources")[0].innerHTML = tablify(json);
}
function update(){
	ajax('POST', "../src/edu_request.php", data = data_m, success = zoom_main, error = console.log, complete = console.log);
	ajax('POST', "../src/edu_request.php", data = data_c, success = zoom_cources, error = console.log, complete = console.log); 
}
//ugly, but found nothing more beautiful
let currentDate = new Date()
let d = currentDate.getDate()
let m = currentDate.getMonth() + 1
let y = currentDate.getFullYear()
var today = d + '.' + m + '.' + y;

let data_m = {'day': today,
			'courses': 0};
let data_c = {'day': today,
			'courses': 1};
update();			
window.setInterval(update, 10_000);




