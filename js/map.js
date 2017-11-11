

function drawMap() {

	$(document).ready(function(){	
		
		$('#loadMapNav').on('shown.bs.tab', function (e) {
			map.invalidateSize(true);
		});
		
	});

	var map = L.map('map').setView([47.88228, 11.70044], 10);
	
	
	L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png?{foo}', {foo: 'bar'}).addTo(map);
	
	/*
	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
	  maxZoom: 18,
	  attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
	  '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
	  'Imagery © <a href="http://mapbox.com">Mapbox</a>',
	  id: 'mapbox.streets'
	}).addTo(mymap);
	*/
	
	/*
	L.marker([51.5, -0.09]).addTo(mymap)
	.bindPopup("<b>Hello world!</b><br />I am a popup.").openPopup();
	*/
	
	$.getJSON( "statistic/Marktlauf_Statistik.php?ajaxFunc=getMapData", function( data ) {
		  var items = [];
		  $.each( data, function( key, val ) {
		    
	        L.circle([val['lon'], val['lat']], getCircleSize(val['count']), { 
	        	color: 'red',
	            fillColor: '#f03',
	            fillOpacity: 0.5,
	            opacity: 0.5
	            }).addTo(map).bindPopup(val['ort'] + ": " + val['count']);
	
	      });
	});
	
	var popup = L.popup();
	
	function onMapClick(e) {
		popup
		.setLatLng(e.latlng)
		.setContent("You clicked the map at " + e.latlng.toString())
		.openOn(map);
	}
	
	map.on('click', onMapClick);
	
	function getCircleSize(count) {
	    if(count < 3) { return 1000 }
	    if(count < 10) { return 1500; }
	    if(count < 20) { return 2000; }
	    if(count < 50) { return 2500; }
	    if(count > 50) { return 3500; }
	}

}