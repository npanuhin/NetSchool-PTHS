months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

months_genetive = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];

function random_color(){
	return chart_colors[Math.floor(Math.random() * chart_colors.length)];
}

function random_setted_color(tone = null){
	let brightness = 50;
	let saturation = 70;
	if(html.classList.contains("dark")){
		brightness = 50;
		saturation = 90;
	}

	if(tone === null){
		tone = Math.random()*360;
	}
	return 'hsl(' + tone + ', '+ saturation + '%, '+ brightness + '%)'
}

function getRatio(window_width){
	if (window_width < 700){
		return 1/(-0.8+800/window_width);
	}
	else if (window_width > 1000){
		return 2;
	}
	else{
		return 1.4;
	}
}

function generate_dynamics_chart(chart_data, min_day){
	let canvas = document.getElementById("dynamics_canvas");
	let window_width  = window.innerWidth || document.documentElement.clientWidth || 
document.body.clientWidth;
	if(window.graph){
		window.graph.destroy();
	}
	window.graph = new Chart(canvas, {
		type: 'line', 
		data: chart_data,
		options: {
			responsive: true,
			aspectRatio: getRatio(window_width),
			tooltips: {
				callbacks: {
					title: function (tooltipItem, data) {
						let date = new Date(tooltipItem[0]["xLabel"]);
						return date.getDay() + " " + months_genetive[date.getMonth()] + " " + date.getFullYear()
					} 
				}
			},
			legend: {
				display: true,
				labels: {
					fontSize: 10,
					fontFamily: "Manrope",
					fontColor: (html.classList.contains("dark") ? text_color_dark : text_color)
				}
			},
			scales: {
				yAxes: [{
					type: 'linear',
					ticks: {
						fontFamily: "Manrope",
						fontColor: (html.classList.contains("dark") ? text_color_dark : text_color)
					},
					gridLines: {
						color: 'grey'
					}
				}],
				xAxes: [{
					type: 'time',
					time: {
						unit: 'month',
						displayFormats: {
							month: "MM"
						}
					},
					ticks: {
						callback: function(value, index, values) {
							return months[Number(value)-1]
						},
						max:  new Date(),
						min: min_day,
						fontFamily: "Manrope",
						fontColor: (html.classList.contains("dark") ? text_color_dark : text_color)
					},
					gridLines: {
						color: 'grey'
					}
				}]
			},
			hover: {
			  mode: 'x'
			},
			animation: { 
				duration: 0
			}
		}
	});
}



let today = new Date();

let main_data = JSON.parse(document.getElementById("data").innerText);

let min_day = new Date();
let chart_data = {datasets: []};

let lessons = Object.keys(main_data).sort();
let count = 0;

for(let i = 0; i<lessons.length;i++){
	let lesson = lessons[i];
	var lesson_data = [];
	let r_color = random_setted_color(360.0*count/lessons.length);

	if(main_data[lesson].length === 0) continue;//That means that it's parsed as array and has zero length → no marks, shuld be ignored
	
	count++;
	
	for(let day in main_data[lesson]){
		var last_y = main_data[lesson][day].toFixed(2);
		let last_x = new Date(day)
		if (last_x < min_day) min_day = last_x;
		lesson_data.push({x: last_x, y : last_y})
	}
	lesson_data.push({x: today, y : last_y})
	
	chart_data["datasets"].push(
					{label: lesson,// + r_color,
					borderColor : r_color,
					backgroundColor: r_color,
					data: lesson_data,
					fill : false}
					)
}

generate_dynamics_chart(chart_data, min_day);
window.addEventListener('resize', function(event){
	generate_dynamics_chart(chart_data, min_day);
});
Event.add(html, "themeChange", ()=>{generate_dynamics_chart(chart_data, min_day)})
