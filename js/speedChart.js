function drawSpeedChart() {
	
	var datasets = [];
	var backgroundColor = [];
	var bgcolors = [];
    
    var ctx = document.getElementById("speedChart").getContext('2d');

	bgcolors[0] = '52, 255, 74';
	bgcolors[1] = '255, 175, 52';
	bgcolors[2] = '52, 132, 255';
	bgcolors[3] = '255, 52, 234';
	
    $.getJSON( "statistic/Marktlauf_Statistik.php?ajaxFunc=getSpeedData", function( data ) {
    	
    	$.each( data[1], function( key, val ) {

			datasets.push ({
    	        label: data[1][key],
                data: data[2][key],
                backgroundColor: getBgColor(key, 1),
                borderColor: getBgColor(key, 1),
                borderWidth: 0
			});

    	});
    	
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data[0],
                datasets: datasets
            },
            options: {
                scales: {
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero:true
                        }
                    }],
            		xAxes: [{
                		stacked: true
            		}]
                },
                plugins: {
                    stacked100: { enable: true }
                }
            }
        });

    	
    });
        
	function getBgColor(key, opacity) {
		return 'rgba(' + bgcolors[key] + ', ' + opacity + ')';
	}
}
