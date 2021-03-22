window.onload = function() {
	var drawingCanvas = document.getElementById("dynamics_canvas");
    if(drawingCanvas && drawingCanvas.getContext) {
		var context = drawingCanvas.getContext('2d');
		var main_data = JSON.parse(document.getElementById("data").innerText);
		console.log(main_data);
		
		chart_data = {datasets: []};
		for(lesson in main_data){
			lesson_data = [];
			//console.log(main_data[lesson]);
			for(day in main_data[lesson]){
				lesson_data.push({x: Number(day), y : main_data[lesson][day].toFixed(2)})
			}
			
			chart_data["datasets"].push(
							{label: lesson,
							borderColor : 'violet',
							data: lesson_data,
							fill : false}
							)
			//data[labels].push(lesson);
		}
		console.log(chart_data); 
		
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
						}
					}],
					xAxes: [{
						type: 'linear',
						ticks: {
							//beginAtZero: true
						}
					}]
				}
			}
		});
		 
	}
}