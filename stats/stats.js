window.onload = function() {
	var drawingCanvas = document.getElementById("dynamics_canvas");
    if(drawingCanvas && drawingCanvas.getContext) {
		var context = drawingCanvas.getContext('2d');
		var data = JSON.parse(document.getElementById("data").innerText);
		console.log(data);
		var myChart = new Chart(context, {
			type: 'line', 
			data: {
				labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
				datasets: [{
					label: '% of Votes',
					borderColor: "blueviolet",
					data: [12, 19, 3, 5, 2, 3],
					fill : false
				},
				{
					borderColor: "moccasin",
					label: '% of People',
					data: [3, 2, 6, 7, 4, 9],
					fill : false
				}
				]
			},
			
			options: {
				
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero: true
						}
					}]
				}
			}
		});
		 
	}
}