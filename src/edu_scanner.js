//import {ajax} from "ajax.js";

//var headers = {
//'Content-Type': 'application/x-www-form-urlencoded'}
function logg(r){
	

	//console.log(r.responseText);
	var js = JSON.parse(r.responseText);
	if (js.length == 0){
		return;
	}
	s = "";
	for (let i in js){
		let lesson = js[i];
		s += '<li>'
		s += lesson["time"]
		s += '	'
		s += lesson["teacher"]
		s += '	'
		s += lesson["name"]
		if (lesson["href"]){
			s += '	'
			s += '<a href = ' + lesson["href"] + '> Zoom </a>'
		}
		s += '</li>'
		//console.log(lesson);
	}
	
	document.getElementsByClassName('Zoom')[0].innerHTML = s;
}

var currentDate = new Date()
var d = currentDate.getDate()
var m = currentDate.getMonth() + 1
var y = currentDate.getFullYear()

let data = {
	'class':"11а",
	'day': d + '.' + m + '.' + y}
	
let res = ajax('POST', "../src/edu_request.php", data = data, success = logg, error = console.log, complete = console.log);
