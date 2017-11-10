<?php

if(isset($_GET['ajaxFunc'])) {
    session_start();
    include "../config.php";
    include "../function.php";
    $link = connectDB();
    $_GET = filterParameters($_GET);
    $_POST = filterParameters($_POST);
    
    if($_GET['ajaxFunc'] == 'getMapData')	   { marktlaufStatistik_getMapData(); }

}

function Marktlauf_Statistik() {
	
?>
	<link rel="stylesheet" href="js/leaflet/leaflet.css"></link>
    <script src="js/leaflet/leaflet.js"></script>
    
    <div id="mapid" style="width: 1000px; height: 700px;"></div>
	<script>
	
	var mymap = L.map('mapid').setView([47.88228, 11.70044], 10);
	
	/* L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png?{foo}', {foo: 'bar'}).addTo(mymap); */
	
	
	 L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
	 maxZoom: 18,
	 attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
	 '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
	 'Imagery © <a href="http://mapbox.com">Mapbox</a>',
	 id: 'mapbox.streets'
	 }).addTo(mymap);
	 
	/*
	L.marker([51.5, -0.09]).addTo(mymap)
	.bindPopup("<b>Hello world!</b><br />I am a popup.").openPopup();
	*/

	$.getJSON( "http://localhost/openTiming/statistic/Marktlauf_Statistik.php?ajaxFunc=getMapData", function( data ) {
		  var items = [];
		  $.each( data, function( key, val ) {
		    //items.push( "<li id='" + key + "'>" + val + "</li>" );

            console.log(val['count']);
		    
            L.circle([val['lon'], val['lat']], getCircleSize(val['count']), { 
            	color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5,
                opacity: 0.5
                }).addTo(mymap).bindPopup(val['ort'] + ": " + val['count']);

          });
	});

	var popup = L.popup();
	
	function onMapClick(e) {
		popup
		.setLatLng(e.latlng)
		.setContent("You clicked the map at " + e.latlng.toString())
		.openOn(mymap);
	}
	
	mymap.on('click', onMapClick);

	function getCircleSize(count) {
        if(count < 3) { return 1000 }
        if(count < 10) { return 1500; }
        if(count < 20) { return 2000; }
        if(count < 50) { return 2500; }
        if(count > 50) { return 3500; }
	}
	
	</script>

<?php
}


function marktlaufStatistik_getMapData() {
	$sql = "SELECT count(o.ort) count, o.ort ort, o.lon lon, o.lat lat FROM teilnehmer t ".
				"LEFT JOIN verein_ort vo on t.verein = vo.verein ".
                "LEFT JOIN ort o on vo.ort = o.ort ".
                "where t.vid = ".$_SESSION['vID']." and o.ort is not null ".
                "group by o.ort;";

    $res = dbRequest($sql, "SELECT");
    
    if($res[1] > 0) {
    	echo json_encode($res[0]);
    }
}

?>
