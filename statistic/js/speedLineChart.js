function drawSpeedLineChart() {
	
	var datasets = [];
	var backgroundColor = [];
	var bgcolors = [];
	
    var ctx = document.getElementById("speedLineChart").getContext('2d');

    bgcolors[0] = '#332288';
    bgcolors[1] = '#88CCEE';
    bgcolors[2] = '#44AA99';
    bgcolors[3] = '#117733';
    bgcolors[4] = '#999933';
    bgcolors[5] = '#DDCC77';
    bgcolors[6] = '#CC6677';
    bgcolors[7] = '#882255';
    bgcolors[8] = '#AA4499';
    
	var dateFormat = 'YYYY-MM-DD HH:mm:ss';
	var date = moment('1900-01-01 00:30:00', dateFormat);
	var labels = [date];
	while (labels.length < 60) {
		date = date.add(1, 'm');
		labels.push(date.format(dateFormat));
	}
		
    $.getJSON( "statistic/Marktlauf_Statistik.php?ajaxFunc=getSpeedLines", function( data ) {
    	
    	$.each( data[0], function( key, val ) {
    		    		
			datasets.push ({
    	        label: data[0][key],
                data: data[2][val],
                backgroundColor: bgcolors[key],
                borderColor: bgcolors[key],
                borderWidth: 2,
                fill: false,
                pointRadius: 1,
                pointHoverRadius: 2,
                showLine: true
			});
			
    	});
    	    	
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                scales: {
                    yAxes: [{
						scaleLabel: {
							display: true,
							labelString: 'Teilnehmer im Ziel'
						}
					}],
            		xAxes: [{
						type: 'time',
						time: {
		                    displayFormats: {
		                        'minute': 'HH:mm'
		                    },
							tooltipFormat: 'HH:mm'
						},
						scaleLabel: {
							display: true,
							labelString: 'Einlaufzeit'
						},
						distribution: 'linear',
						ticks: {
							source: 'labels'
						}
					}]
                }
            }
        });

    	
    });
        
	function getBgColor(key, opacity) {
		return 'rgba(' + bgcolors[key] + ', ' + opacity + ')';
	}
}
