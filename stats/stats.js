Chart.defaults.global.defaultFontFamily = 'Manrope';
Chart.defaults.global.defaultFontStyle  = "bold";

months =          ['Январь', 'Февраль', 'Март',  'Апрель', 'Май', 'Июнь', 'Июль', 'Август',  'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
months_genetive = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];

function random_color() {
	return chart_colors[Math.floor(Math.random() * chart_colors.length)];
}

function random_setted_color(tone=null) {
	let brightness = 50,
		saturation = (html.classList.contains("dark") ? 90 : 70);

	if (tone === null) tone = Math.random() * 360;

	return 'hsl(' + tone + ', '+ saturation + '%, '+ brightness + '%)';
}

function getRatio(window_width) {
	if (window_width < 700)  return 1 / (-0.8 + 800 / window_width);
	if (window_width > 1000) return 2;
	return 1.4;
}

function generate_dynamics_chart(chart_data, min_day) {
	let canvas = document.getElementById("dynamics_canvas"),
		window_width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	
	if (window.graph){
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
						let lesson_number = tooltipItem[0].datasetIndex;
						//let point_number = tooltipItem[0].index;
						let lesson_name = data.datasets[lesson_number].label;
						
						let marks_for_the_day = source_marks[lesson_name].filter(element => element[1].getTime() == date.getTime());
						marks_for_the_day = marks_for_the_day.map(mark => mark[0]);
						
						/* code to check all the marks
						for (lesson in data.datasets){
							let lesson_name = data.datasets[lesson].label;
							console.log(lesson_name);
							marks_for_the_day = marks_for_the_day.concat(source_marks[lesson_name].filter(element => element[1].getTime() == date.getTime()));
						}
						*/
						
						let generated_date = date.getDate() + " " + months_genetive[date.getMonth()];
						
						let generated_marks = ""
						if(marks_for_the_day.length == 1){
							generated_marks = ": " + marks_for_the_day[0];
						}
						else if (marks_for_the_day.length > 1){
							generated_marks = ": " + marks_for_the_day;
						}
						
						
						return generated_date + generated_marks;
					}
				}
			},
			legend: {
				display: true,
				labels: {
					fontSize: 10,
					fontFamily: "Manrope",
					fontColor: (html.classList.contains("dark") ? text_color_dark : text_color)
				},
				
				onClick: function(e, legendItem) {
					let index = legendItem.datasetIndex;
					let ci = this.chart;
					let select_one = !document.getElementById("Select_one").checked;
					
					if(select_one){
						//select only one
						//this is code I just took from some strange place…
						let alreadyHidden = (ci.getDatasetMeta(index).hidden === null) ? false : ci.getDatasetMeta(index).hidden;

						ci.data.datasets.forEach(function(e, i) {
							let meta = ci.getDatasetMeta(i);

							if (i !== index) {
							  if (!alreadyHidden) {
								meta.hidden = meta.hidden === null ? !meta.hidden : null;
							  }
							  else if (meta.hidden === null) {
								meta.hidden = true;
							  }
							}
							else if (i === index) {
							  meta.hidden = null;
							}
					  });

					  ci.update();
					}
					else{
						//standart behaviour
						ci.getDatasetMeta(index).hidden = !ci.getDatasetMeta(index).hidden;
						ci.update();
					}
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
						color: '#ccc',
						//zeroLineColor: '#ccc'
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
						max: new Date(),
						min: min_day,
						fontFamily: "Manrope",
						fontColor: (html.classList.contains("dark") ? text_color_dark : text_color)
					},
					gridLines: {
						color: '#ccc',
						zeroLineColor: '#ccc'
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
	//to hide/show smth special:
	//window.graph.getDatasetMeta(1).hidden=true;
	//window.graph.update();
}



let today = new Date();
today = new Date(today.getFullYear() + "-" + (today.getMonth() + 1).toString().padStart(2, '0') + "-" + today.getDate().toString().padStart(2, '0')) //just to make date equal to others in terms of hours, minutes e.t.c.; ISO format; padStart is vital

let html_data = JSON.parse(document.getElementById("data").innerText)
let main_data = html_data[0];
let source_marks = html_data[1];

for(lesson in source_marks){
	for(point in source_marks[lesson]){
		source_marks[lesson][point][1] = new Date(source_marks[lesson][point][1]);
	}
}

let min_day = new Date();
let chart_data = {datasets: []};

let lessons = Object.keys(main_data).sort();
let count = 0;

for (let i = 0; i < lessons.length; ++i) {
	var lesson_data = [];
	let lesson = lessons[i],
		r_color = random_setted_color(360.0 * count / lessons.length);

	if (main_data[lesson].length === 0) continue;  // That means that it's parsed as array and has zero length → no marks, should be ignored
	
	++count;
	
	for (let day in main_data[lesson]) {
		var last_y = main_data[lesson][day].toFixed(2);
		var last_x = new Date(day);
		if (last_x < min_day) min_day = last_x;
		lesson_data.push({x: last_x, y : last_y})
	}
	if(today > last_x){
		lesson_data.push({x: today, y : last_y})
	}
	
	chart_data["datasets"].push({
		label: lesson,  // + r_color,
		borderColor: r_color,
		backgroundColor: r_color,
		data: lesson_data,
		fill: false
	});
}

// setTimeout(() => {generate_dynamics_chart(chart_data, min_day)}, 1)
generate_dynamics_chart(chart_data, min_day);

var saved_width = document.documentElement.clientWidth;

Event.add(window, "resize", () => {
	if (document.documentElement.clientWidth == saved_width){ 
	//on mobiles adress line disappears
	//and it thinks it is resize
		return;
	}
	saved_width = document.documentElement.clientWidth;
	generate_dynamics_chart(chart_data, min_day);
});

Event.add(html, "themeChange", () => {generate_dynamics_chart(chart_data, min_day)})
