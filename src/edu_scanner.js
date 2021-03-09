//import {ajax} from "ajax.js";

//var headers = {
//'Content-Type': 'application/x-www-form-urlencoded'}
function logg(r){
	document.getElementsByClassName('Zoom')[0].innerHTML = r.responseText;
	var js = JSON.parse(r.responseText);
	console.log(js);
	document.getElementsByClassName('Zoom')[0].innerHTML
}

let data = {
	'class':"11а",
	'day':'09.03.2021'}
	
let res = ajax('POST', "../src/edu_request.php", data = data, success = logg, error = console.log, complete = console.log);
