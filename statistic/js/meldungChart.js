function drawMeldungChart() {
	
	var datasets = [];
	var backgroundColor = [];
	var bgcolors = [];
	
    var ctx = document.getElementById("meldungChart").getContext('2d');

	bgcolors[0] = '58, 79, 232';
    bgcolors[1] = '124, 58, 232';
	bgcolors[2] = '211, 58, 232';
	bgcolors[3] = '232, 58, 166';
	bgcolors[4] = '232, 58, 79';
	
    $.getJSON( "statistic/Marktlauf_Statistik.php?ajaxFunc=getMeldungen", function( data ) {
    	
    	$.each( data[1], function( key, val ) {

			datasets.push ({
    	        label: data[1][key],
                data: data[2][val],
                backgroundColor: getBgColor(key, 0.8),
                borderColor: getBgColor(key, 1),
                borderWidth: 1
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
                }
            }
        });

    	
    });
        
	function getBgColor(key, opacity) {
		return 'rgba(' + bgcolors[key] + ', ' + opacity + ')';
	}
}
