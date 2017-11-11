<?php

if(isset($_GET['ajaxFunc'])) {
    session_start();
    include "../config.php";
    include "../function.php";
    $link = connectDB();
    $_GET = filterParameters($_GET);
    $_POST = filterParameters($_POST);
    
    if($_GET['ajaxFunc'] == 'getMapData')	           { marktlaufStatistik_getMapData(); }
    if($_GET['ajaxFunc'] == 'getSpeedData')	           { marktlaufStatistik_getSpeedData(); }
    if($_GET['ajaxFunc'] == 'getTeilnehmerCount')	   { marktlaufStatistik_getTeilnehmerCount(); }
    
    
}

function Marktlauf_Statistik() {
	
?>

<div>
	<link rel="stylesheet" href="js/leaflet/leaflet.css"></link>
	<script src="js/leaflet/leaflet.js"></script>
	<script src="js/chart/Chart.min.js"></script>
	<script src="js/chart/stacked100.js"></script>
	<script src="js/speedChart.js"></script>
	<script src="js/teilnehmerChart.js"></script>


  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#teilnehmer" aria-controls="teilnehmer" role="tab" data-toggle="tab">Teilnehmer</a></li>
    <li role="presentation"><a href="#speed" aria-controls="speed" role="tab" data-toggle="tab">Einlaufzeiten</a></li>
    <li role="presentation"><a href="#map" id="loadMapNav" aria-controls="map" role="tab" data-toggle="tab">Map</a></li>
    <li role="presentation"><a href="#meldungen" aria-controls="meldungen" role="tab" data-toggle="tab">Meldungen</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane fade in active" id="teilnehmer">
    
       	<canvas id="teilnehmerChart" width="400" height="150"></canvas>
    
        <script>
        	drawTeilnehmerChart();
        </script>
    
    </div>
    
    <div role="tabpanel" class="tab-pane fade" id="speed">
    
    	<canvas id="speedChart" width="400" height="150"></canvas>
    
        <script>
        	drawSpeedChart();
        </script>
    
    </div>
    <div role="tabpanel" class="tab-pane fade" id="map">
    
		<div id="map" style="width: 100%; height: 75%;"></div>
    	<script src="js/map.js"></script>
    	
    	<script>
    		drawMap();
    	</script>
    	
    </div>
    
    <div role="tabpanel" class="tab-pane fade" id="meldungen">1..</div>
    
  </div>

</div>



<?php
}


function marktlaufStatistik_getMapData() {
	$sql = "SELECT count(o.ort) count, o.ort ort, o.lon lon, o.lat lat FROM teilnehmer t ".
				"LEFT JOIN verein_ort vo on t.verein = vo.verein ".
                "LEFT JOIN ort o on vo.ort = o.ort ".
                "where t.vid = ".$_SESSION['vID']." and o.ort is not null ".
                "group by o.ort ".
                "order by count desc;";

    $res = dbRequest($sql, "SELECT");
    
    if($res[1] > 0) {
    	echo json_encode($res[0]);
    }
}

function marktlaufStatistik_getSpeedData() {
    
    $mlJson = getConfig("HML");
    $mlArray = json_decode($mlJson['HML'], true);
    $mlId = marktlaufStatistik_getLaufId($mlArray, '10K');
    
    $sql = "SELECT t.vid, COUNT(t.id) count, DATE_FORMAT(l.start, '%Y') year, ".
           "CASE WHEN zeit < '00:40:00' THEN 0 ".
           "WHEN zeit < '00:50:00' THEN 1 ".
           "WHEN zeit < '01:00:00' THEN 2 ".
           "WHEN zeit >= '01:00:00' THEN 3 ".
           "END as segment ".
           "FROM teilnehmer t ".
           "LEFT JOIN lauf l on t.lid = l.id ".
           "WHERE platz > 0 and lid in ($mlId) ".
           "GROUP BY vid, segment ".
           "ORDER BY segment, year";
    
    $res = dbRequest($sql, "SELECT");
    
    $labelsY = array();
    $labelsT = array("< 40", "< 50", "< 60", "> 60");
    
    $data = array();
    
    if($res[1] > 0) {
        foreach($res[0] as $row) {
            
            if(in_array($row['year'], $labelsY) === false) { array_push($labelsY, $row['year']); }
            
            if(!isset($data[$row['segment']])) { $data[$row['segment']] = array(); }
            array_push($data[$row['segment']], $row['count']);

        }
    }
    
    $out[0] = $labelsY;
    $out[1] = $labelsT;
    $out[2] = $data;
    
    if($res[1] > 0) {
        echo json_encode($out);
    }
}

function marktlaufStatistik_getTeilnehmerCount() {
    $mlJson = getConfig("HML");
    $mlArray = json_decode($mlJson['HML'], true);
    
    $races = marktlaufStatistik_getRaces($mlArray);
    $years = marktlaufStatistik_getYears($mlArray);
    
    sort($races);
    
    /* array mit 0-Werten aufbauen, damit alle werte belegt sind */
    foreach ($races as $race) {
        foreach($years as $year) {
            $data[$race][$year] = 0;
        }
    }
    
    foreach ($races as $race) {
        $mlId = marktlaufStatistik_getLaufId($mlArray, $race);
        
        $sql = "SELECT COUNT(t.id) count, DATE_FORMAT(l.start, '%Y') year ".
            "FROM teilnehmer t ".
            "LEFT JOIN lauf l on t.lid = l.id ".
            "WHERE platz > 0 and t.lid in ($mlId) ".
            "GROUP BY year ".
            "ORDER BY year asc;";
        
        $res = dbRequest($sql, "SELECT");

        if($res[1] > 0) {
            foreach($res[0] as $row) {
                $data[$race][$row['year']] = $row['count'];
            }
        }
    }
    
    /* die keys werden entfernt, weil sie beim chart nicht benötigt werden */
    foreach($races as $race) {
        $data[$race] = array_values($data[$race]);
    }
    
    $out[0] = $years;
    $out[1] = $races;
    $out[2] = $data;
    echo json_encode($out);
}

function marktlaufStatistik_getLaufId($mlArray, $lauf) {
            
    $ids = "";
    foreach($mlArray as $year) {
        if(isset($year['lauf'][$lauf])) {
            $ids = $ids.",".$year['lauf'][$lauf];
        }
    }
    return substr($ids, 1);
}

function marktlaufStatistik_getRaces($mlArray) {
    
    $races = array();
    foreach($mlArray as $year) {
        $keys = array_keys($year['lauf']);
        foreach($keys as $key) {
            if(in_array($key, $races) === false) { array_push($races, $key); }
        }
    }
    return $races;
}

function marktlaufStatistik_getYears($mlArray) {
    
    $years = array();
    $keys = array_keys($mlArray);
    foreach($keys as $key) {
        if(in_array($key, $years) === false) { array_push($years, $key); }
    }
    
    sort($years);
    
    return $years;
}

?>
