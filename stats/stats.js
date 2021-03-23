chart_colors = ["aliceblue", "aqua", "aquamarine", "azure", "beige", "blue", "blueviolet", "brown", "burlywood", "cadetblue", "chartreuse", "chocolate", "coral", "crimson", "cyan", "darkblue", "darkcyan", "darkgoldenrod", "darkgray", "darkgreen", "darkgrey", "darkkhaki", "darkmagenta", "darkolivegreen", "darkorange", "darkorchid", "darkred", "darksalmon", "darkseagreen", "darkslateblue", "darkslategray", "darkslategrey", "darkturquoise", "darkviolet", "deeppink", "deepskyblue", "dimgray", "dimgrey", "dodgerblue", "firebrick", "forestgreen", "fuchsia", "gainsboro", "gold", "goldenrod", "gray", "green", "greenyellow", "grey", "hotpink", "indianred", "indigo", "khaki", "lavender", "lavenderblush", "lawngreen", "lemonchiffon", "lime", "limegreen", "linen", "magenta", "maroon", "mediumaquamarine", "mediumblue", "mediumorchid", "mediumpurple", "mediumseagreen", "mediumslateblue", "mediumspringgreen", "mediumturquoise", "mediumvioletred", "midnightblue", "moccasin", "navy", "oldlace", "olive", "olivedrab", "orange", "orangered", "orchid", "palegoldenrod", "palegreen", "paleturquoise", "palevioletred", "peachpuff", "peru", "pink", "plum", "powderblue", "purple", "rebeccapurple", "red", "rosybrown", "royalblue", "saddlebrown", "salmon", "sandybrown", "seagreen", "sienna", "silver", "skyblue", "slateblue", "slategray", "slategrey", "snow", "springgreen", "steelblue", "tan", "teal", "thistle", "tomato", "turquoise", "violet", "wheat", "yellow", "yellowgreen"]

months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

function random_color(){
	return chart_colors[Math.floor(Math.random() * chart_colors.length)];
}

function random_setted_color(){
	let brightness = 40 + Math.random()*20;
	let saturation = 85;
	let tone = Math.random()*360;
	return 'hsla(' + tone + ', '+ saturation + '%, '+ brightness + '%)'
}

function daysOfYearFromDate(date){
    return (Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()) - Date.UTC(date.getFullYear(), 0, 0)) / 24 / 60 / 60 / 1000;
}
function daysOfYearToDate(day){
	let date = new Date();
	date.setMonth(0);
	date.setDate(day);
	return date;
}

window.onload = function() {
	today = new Date();
	
	var drawingCanvas = document.getElementById("dynamics_canvas");
    if(drawingCanvas && drawingCanvas.getContext) {
		var context = drawingCanvas.getContext('2d');
		var main_data = JSON.parse(document.getElementById("data").innerText);
		console.log(main_data);
		
		var min_day = 366;
		var chart_data = {datasets: []};
		
		var lessons = Object.keys(main_data).sort();
		
		for(let i = 0; i<lessons.length;i++){
			let lesson = lessons[i];
			var lesson_data = [];
			let r_color = random_setted_color;//random_color()
			//console.log(main_data[lesson]);
			
			for(let day in main_data[lesson]){
				var last_y = main_data[lesson][day].toFixed(2);
				let last_x = daysOfYearToDate(Number(day))
				min_day = Math.min(min_day, day)
				lesson_data.push({x: last_x, y : last_y})
			}
			lesson_data.push({x: today, y : last_y})
			chart_data["datasets"].push(
							{label: lesson,// + r_color,
							borderColor : r_color,
							data: lesson_data,
							fill : false}
							)
		}
		console.log(chart_data); 
		let min_date = daysOfYearToDate(min_day);
		min_date.setDate(0);
		console.log(min_day, min_date);
		var myChart = new Chart(context, {
			type: 'line', 
			data: chart_data,
			
			options: {
				responsive: true,
				scales: {
					yAxes: [{
						type: 'linear',
						ticks: {
							//beginAtZero: true
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
							max:  today,
							min: min_date,
							beginAtZero: true,
							
						},
						gridLines: {
							color: 'grey'
						}
					}]
				}
			}
		});
		 
	}
}