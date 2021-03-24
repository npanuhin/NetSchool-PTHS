months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

function random_color(){
	return chart_colors[Math.floor(Math.random() * chart_colors.length)];
}

function random_setted_color(tone = null){
	let brightness = 60;
	let saturation = 90;
	
	if(tone === null){
		tone = Math.random()*360;
	}
	return 'hsla(' + tone + ', '+ saturation + '%, '+ brightness + '%)'
}

const window_width  = window.innerWidth || document.documentElement.clientWidth || 
document.body.clientWidth;

window.onload = function() {
	today = new Date();
	var drawingCanvas = document.getElementById("dynamics_canvas");
	
    if(drawingCanvas && drawingCanvas.getContext) {
		
		var context = drawingCanvas.getContext('2d');
		var main_data = JSON.parse(document.getElementById("data").innerText);

		var min_day = new Date();
		var chart_data = {datasets: []};
		
		var lessons = Object.keys(main_data).sort();
		
		for(let i = 0; i<lessons.length;i++){
			let lesson = lessons[i];
			var lesson_data = [];
			let r_color = random_setted_color(360.0*i/lessons.length);

			if(main_data[lesson].length === 0) continue;//That means that it's parsed as array and has zero length → no marks, shuld be ignored
			
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

		min_day.setDate(1);
		let ratio = 1.4;
		//console.log(window_width);
		if (window_width < 700){
			ratio = 1/(-0.8+800/window_width);
		}
		else if (window_width > 1000){
			ratio = 2;
		}
		var myChart = new Chart(context, {
			type: 'line', 
			data: chart_data,
			
			options: {
				responsive: true,
				aspectRatio: ratio,
				tooltips: {
					callbacks: {
						title: function (tooltipItem, data) {
							let date = new Date(tooltipItem[0]["xLabel"]);
							return date.toLocaleDateString()
						} 
					}
				},
				legend: {
					display: true,
					labels: {
						fontSize: 10
					}
				},
				scales: {
					yAxes: [{
						type: 'linear',
						ticks: {
							//
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
							min: min_day,
							
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