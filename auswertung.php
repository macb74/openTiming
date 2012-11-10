<?php

function auswertung() {
	$result = "";
	
	global $func;
	$html="";
 	
	if(isset($_GET['ID'])) {
		$result = doAuswertung($_GET['ID']);
	}
		
	$html = auswertungForm($html);
	$html .= "<div id='data_div'>$result</div>";
	return table("Auswertung", $html);
}

function doAuswertung($rennen) {
	$link = connectDB();
	$anzTeilnehmer = 0;
	$anzTeams = 0;
	$veranstaltung = $_SESSION['vID'];
	$rInfo = getRennenData($rennen);
	cleanAll($veranstaltung, $rennen);
	
	// $startZeit = $rInfo['startZeit'];
	if($rInfo['rundenrennen'] == 0 || $rInfo['rundenrennen'] == 2) {
		setKlasse($veranstaltung,$rennen);
		updateZeit($veranstaltung, $rennen, $rInfo);
		$anzTeilnehmer = updatePlatzierung($veranstaltung, $rennen, $rInfo);
		$teamAnz = $rInfo['teamAnz'];
		$anzTeams = updateTeam($veranstaltung, $rennen, $teamAnz);
	} elseif ($rInfo['rundenrennen'] == 1) {
		setKlasse($veranstaltung,$rennen);
		updateAnzRunden($veranstaltung, $rennen, $rInfo);
		updateZeit($veranstaltung, $rennen, $rInfo);
		$anzTeilnehmer = updatePlatzierung($veranstaltung, $rennen, $rInfo);
	}
	updateStatus($veranstaltung, $rennen);
	
	mysql_close($link);
	return "<p>Es wurden <b>$anzTeilnehmer Teilnehmer</b> und <b>$anzTeams Teams ausgewertet</b></p>";
}

function getSeconds($s) {
	$s = explode(":", $s);
	$sec = $s[0] * 3600 + $s[1] * 60 + $s[2];
	return $sec;
}
	
function getRealTime($startZeit, $zeit) {
	$zielSec = getSeconds($zeit);
	$startSec = getSeconds($startZeit);
	$zeit = $zielSec - $startSec;
	
	$zeit = sec2Time($zeit);
	return $zeit;
}

function sec2Time($sec){
  if(is_numeric($sec)){
    if($sec >= 3600){
      	$value["hours"] = floor($sec/3600);
      	if($value["hours"] < 10) { $value["hours"] = "0".$value["hours"]; }
      	$sec = ($sec%3600);
    } else {
    	$value["hours"] = "00";
    }
    if($sec >= 60){
      	$value["minutes"] = floor($sec/60);
      	if($value["minutes"] < 10) { $value["minutes"] = "0".$value["minutes"]; }
      	$sec = ($sec%60);
    } else {
		$value["minutes"] = "00";
    }
    $value["seconds"] = floor($sec);
    if($value["seconds"] < 10) { $value["seconds"] = "0".$value["seconds"]; }
    
    $time = $value["hours"].":".$value["minutes"].":".$value["seconds"];
    return $time;
  }
}

function cleanAll($veranstaltung, $rennen) {
	$query = "update teilnehmer set zeit='00:00:00', platz = 0, akplatz = 0, vplatz = 0, vnummer = '', mplatz = 0, vtime = '00:00:00' where vid = $veranstaltung and lid = $rennen";
	$result = mysql_query($query) or die("Anfrage fehlgeschlagen: " . mysql_error());
}

function updateZeit($veranstaltung, $rennen, $rInfo) {
        global $config;

	if($rInfo['use_lID'] == 1) { $sql_lID = "and z.lid = $rennen "; } else { $sql_lID = ""; }

	switch($rInfo["rundenrennen"]) {
		case 1:  $zeit = "max(z.zeit)"; break;   # Bei Rennen auf Zeit: Ende letzte Runde
		case 2:  $zeit = "z.zeit"; break;        # Bei Renden auf x Runden: alle Runden, letzte zaehlt wenn gleich Vorgabe
                default: $zeit = "min(z.zeit)";          # Bei normalen Rennen: erster Zieldurchlauf zaehlt
	} 

        # Test auf Zeitumstellungslauf - rennen IDs muessen in Config gesetzt sein
        if(isset($config['zeitsprungLIDs'])) {
          if(array_search($rennen, $config['zeitsprungLIDs']) !== false) define('ZEITSPRUNG',true);
        }
        if(defined('ZEITSPRUNG')) $zeit="if(HOUR($zeit)>2 AND HOUR($zeit)<12,timediff($zeit,'01:00:00'),$zeit)";

	if($rInfo["rundenrennen"] != 2) {
	# ohne Rundenvorgabe oder kein Rundenrennen:
		$sql = "select t.id, t.stnr as stnr, $zeit as zeit ".
			"from teilnehmer as t left join zeit as z on t.stnr = z.nummer ".
			"where t.vid = $veranstaltung and z.vid = $veranstaltung and t.lid = $rennen ".$sql_lID.
			"and z.zeit > '".$rInfo['startZeit']."' ".
			"group by t.stnr";

		$result = mysql_query($sql);
		if (!$result) { die('Invalid query: ' . mysql_error()); }
		//echo $sql;
			
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$realTime = getRealTime($rInfo['startZeit'], $row['zeit']);
			$sql = "update teilnehmer set Zeit = '$realTime' where id = ".$row['id'];
			$res = mysql_query($sql);
			if (!$res) { die('Invalid query: ' . mysql_error()); }
		}
	} else {
        # Rennen auf x Runden:
		$sql = "select t.id, t.stnr as stnr, $zeit as zeit ".
			"from teilnehmer as t left join zeit as z on t.stnr = z.nummer ".
			"where t.vid = $veranstaltung and z.vid = $veranstaltung and t.lid = $rennen ".$sql_lID.
			"and z.zeit > '".$rInfo['startZeit']."' order by stnr, zeit asc";
		//echo $sql;
	
		$result = mysql_query($sql);
		if (!$result) { die('Invalid query: ' . mysql_error()); }
	
		$i=1;
		$oldStnr = 0;
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			if($oldStnr == $row['stnr']) { $i++; } else { $i=1; }
			if($i == $rInfo['rdVorgabe']) {
				//echo $i."-";
				$realTime = getRealTime($rInfo['startZeit'], $row['zeit']);
				$sql = "update teilnehmer set Zeit = '$realTime' where id = ".$row['id'];
				$res = mysql_query($sql);
				if (!$res) { die('Invalid query: ' . mysql_error()); }			
			}
			$oldStnr = $row['stnr'];
		}
	}	

	$query = "update teilnehmer set zeit=manzeit where manzeit <> '00:00:00' and useManTime = 1 and vid = $veranstaltung and lid = $rennen";
	$result = mysql_query($query) or die("Anfrage fehlgeschlagen: " . mysql_error());
	
}

function updatePlatzierung($veranstaltung, $rennen, $rInfo) {

	if($rInfo["rundenrennen"] == 1) { $orderBy = "order by runden desc, zeit asc"; } else { $orderBy = "order by zeit asc"; }
	
	$sql = "select id, klasse, geschlecht from teilnehmer ".
	"where vid = $veranstaltung and lid = $rennen and zeit <> '00:00:00' ".
		"and klasse <> '' and disq = 0 and del = 0 ".$orderBy;
	
	$result = mysql_query($sql);
	if (!$result) { die('Invalid query: ' . mysql_error()); }
	
	$m = 1;
	$w = 1;
	$x = 1;
	$kl = array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$aktKl = "";
		$sql2 = "";
		$aktKl = $row['klasse'];
		if(isset($kl[$aktKl])) { $kl[$aktKl]++; } else { $kl[$aktKl] = 1; }
		
		if($row['geschlecht'] == 'M') {
			$sql2 = "update teilnehmer set platz = $m, akplatz = $kl[$aktKl] where id = $row[id]";
			$m++; 
		} elseif($row['geschlecht'] == 'W') {
			$sql2 = "update teilnehmer set platz = $w, akplatz = $kl[$aktKl] where id = $row[id]";
			$w++; 
		} else {
			$sql2 = "update teilnehmer set platz = $x, akplatz = $kl[$aktKl] where id = $row[id]";
			$x++;
		}

		$result2 = mysql_query($sql2);
		if (!$result) { die('Invalid query: ' . mysql_error()); }
	}	
	
	return mysql_num_rows($result);
}

function setKlasse($veranstaltung, $rennen) {

	$sql = "select id, lid, geschlecht, jahrgang from teilnehmer ".
	"where vid = $veranstaltung and lid = $rennen and disq = 0 and del = 0";
		
	$result = mysql_query($sql);
	if (!$result) { die('Invalid query: ' . mysql_error()); }
	
	if(mysql_num_rows($result) > 0) {
	
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$klasse = getKlasse($row['jahrgang'], $row['geschlecht'], $row['lid'], 0);
			$sql = "update teilnehmer set klasse = '$klasse[0]', vklasse = '$klasse[1]' where id = ".$row['id'];
			$res = mysql_query($sql);
			//echo $sql;
			if (!$res) { die('Invalid query: ' . mysql_error()); }
		}
		
	}
}

function updateTeam($veranstaltung, $rennen, $teamAnz) {
	
	# Platz in Verein + eindeutige Vereinsnummer
	$sql = "select ID, verein, vklasse from teilnehmer ";
	$sql .= "where vid = $veranstaltung ";
	$sql .= "and lid = $rennen ";
	$sql .= "and zeit <> '00:00:00' ";
	$sql .= "and verein <> '' ";
	$sql .= "and disq = 0 ";
	$sql .= "and del = 0 ";
	$sql .= "and vklasse <> '' ";
	$sql .= "order by verein, vklasse, zeit";
	
	$result = mysql_query($sql) or die("Anfrage fehlgeschlagen: " . mysql_error());

	$v 		= '';	# Verein des vorherigen Datensatzes
	$vnr 	= 1;	# Eindeutige Mannschaftsnummer
	$alleMannschaften = array();
	while ($row = mysql_fetch_assoc($result)) {
		if ($v != $row["verein"]."_".$row['vklasse']) { 
			$mannschaft = "";
		}

		$r = $row["ID"];
		$mannschaft[$r]["ID"] = $r;
		$mannschaft[$r]["vnr"] = $vnr;
		$mannschaft[$r]["vkl"] = $row['vklasse'];
		
		#  eine komplette Mannschaft
		if (count($mannschaft) == $teamAnz) {
			$mplatz = 1;
			foreach ($mannschaft as $m) {
				$_id 		= $m["ID"];
				$_vnr 		= $m["vnr"];
				$_vkl 		= $m["vkl"];
				$_uVnr		= $rennen."_".$_vkl."_".$_vnr;
				$q = "update teilnehmer set vnummer = '".$_uVnr."', mplatz = $mplatz where ID = $_id";
				$r = mysql_query($q) or die("Anfrage fehlgeschlagen: " . mysql_error());
				$mplatz++;
			}
			#print_r($mannschaft);
			$alleMannschaften[$vnr-1] = $_uVnr;
			$mannschaft = "";
			$vnr++;
		}
		$v = $row["verein"]."_".$row['vklasse'];		
	}

	if($alleMannschaften) {
		# Mannschaftszeiten aktualisieren
		foreach($alleMannschaften as $ms) {
			$sql = "select vnummer, verein, zeit from teilnehmer where vid = $veranstaltung and lid = $rennen and vnummer = '".$ms."'";
			$res = mysql_query($sql) or die("Anfrage fehlgeschlagen: " . mysql_error());
	
			$sec = 0;
			while ($row = mysql_fetch_assoc($res)) {
				$sec = $sec + getSeconds($row['zeit']);
			}
			$time = sec2Time($sec);
			$sql = "update teilnehmer set vtime = '".$time."' where vnummer = '".$ms."'";
			$res = mysql_query($sql) or die("Anfrage fehlgeschlagen: " . mysql_error());
		}
		
		
		# Mannschaftsplatzierungen aktualisieren
		$sql2 = "select vnummer, vtime, vklasse from teilnehmer where vid = $veranstaltung and lid = $rennen and vtime <> '00:00:00' and vnummer <> '' group by vnummer order by vtime, vnummer";
		$res2 = mysql_query($sql2) or die("Anfrage fehlgeschlagen: " . mysql_error());
		$kl = array();
		
		while ($row = mysql_fetch_array($res2, MYSQL_ASSOC)) {
			$aktKl = "";
			$sql3 = "";
			$aktKl = $row['vklasse'];
			if( isset($kl[$aktKl])) { $kl[$aktKl]++; } else { $kl[$aktKl] = 1; }
	
			$sql3 = "update teilnehmer set vplatz = $kl[$aktKl] where vnummer = '".$row['vnummer']."'";
			//echo $sql3;
			$res3 = mysql_query($sql3);
			if (!$res3) { die('Invalid query: ' . mysql_error()); }
		}	
		
	}
	return count($alleMannschaften);
}

function auswertungForm($html) {

	global $func;
	
	# Display Rennen
	//$html = "";
	$link = connectDB();
	$veranstaltung = $_SESSION['vID'];
	$sql = "select * from lauf where vID = $veranstaltung order by start asc, titel;";
	$result = mysql_query($sql);
		if (!$result) {
    		die('Invalid query: ' . mysql_error());
		}

	$html2 = "";
	$i=1;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if($i%2 == 0) { $html2 .= "<tr class=\"even\">\n"; } else { $html2 .= "<tr class=\"odd\">\n"; }

		$subtitle = "";
		if ($row['untertitel'] != "") { $subtitle = "<i>- ".$row['untertitel']."</i>"; }
		$html2 .= "<td width=\"30\" align\"left\">".$row['ID']."</td>\n";
		$html2 .= "<td align\"left\">".$row['titel']." $subtitle</td>\n";
		//$html2 .= "<td align\"left\">".$row['untertitel']."</td>\n";
		$html2 .= "<td align\"left\">".$row['start']."</td>\n";
		$html2 .= "<td align\"left\">".$row['aktualisierung']."</td>\n";
		$html2 .= "<td align\"center\">";
			if($row['lockRace'] == 0) {	
			$html2 .= 	"<a href=\"".$_SERVER["SCRIPT_NAME"]."?func=".$func[0]."&ID=".$row['ID']."\">Laufwertung starten</a>" .
						"&nbsp;&nbsp;" .
						"| ";
			} else {
			$html2 .= 	"Rennen gesperrt" .
						"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
						"| ";
			}
		$html2 .= "<a id=\"showInDiv\" href=\"jqRequest&func=showWithowtTime&lid=".$row['ID']."\">Teilnehmer ohne Zeit</a>" .
				"&nbsp;&nbsp;" .
//				"| " .
//				"&nbsp;&nbsp;" .
//				"<a href=\"".$_SERVER["SCRIPT_NAME"]."?func=auswertung.klasse&ID=".$row['ID']."\">Klassen neu zuordnen</a>" .
				"</td>\n";
		$html2 .= "</tr>\n";
		$i++;
	}

	$columns = array('ID', 'Titel', 'Start', 'Aktualisierung', 'Aktion');
	$html .= tableList($columns, $html2, "common meetings");
	
	mysql_close($link);
			
	return $html;
}

function showWithowtTime($rennen) {

	$link = connectDB();
	
	$html = "<br>";
	$html = "<p><a href=\"#\" onClick=\"clearDiv()\">clear</a></p>";
	$sql = "SELECT t.*, l.titel FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
		"where t.vID = ".$_SESSION['vID']." ".
			"and t.lid = $rennen and del= 0 and disq = 0 and zeit = '00:00:00' ".
			"order by nachname asc;";
	$result = mysql_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}	
	$html2 = "";
	$i=1;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if($i%2 == 0) { $html2 .= "<tr class=\"even\">\n"; } else { $html2 .= "<tr class=\"odd\">\n"; }
		$html2 .= "<td align\"left\">".$row['stnr']."</td>\n";
		$html2 .= "<td align\"left\"><a href=\"".$_SERVER["SCRIPT_NAME"]."?func=teilnehmer.edit&ID=".$row['ID']."&nextUrl=".base64_encode($_SERVER["SCRIPT_NAME"]."?func=auswertung")."\">".$row['nachname'].", ".$row['vorname']."</a></td>\n";		
		$html2 .= "<td align\"left\">".$row['verein']."</td>\n";
		$html2 .= "<td align\"left\">".$row['jahrgang']."</td>\n";
		$html2 .= "<td align\"left\">".$row['geschlecht']."</td>\n";
		$html2 .= "<td align\"left\">".$row['klasse']."</td>\n";
		$html2 .= "<td align\"left\">".$row['titel']."</td>\n";
		$html2 .= "<td align\"left\">".$row['zeit']."</td>\n";

		$html2 .= "</tr>\n";
		$i++;
	}

	$columns = array('Stnr', 'Name', 'Verein', 'JG', 'G', 'Klasse', 'Rennen', 'Zeit');
	$html .= tableList($columns, $html2, "common");
	
	mysql_close($link);
	
	return $html;
}


function updateAnzRunden($veranstaltung, $rennen, $rInfo) {
	$numbers = array();

	// um mit mehreren Readern arbeiten zu können werden immer nur Runden gezählt, die länger als 10 sec. dauern.
	// alles was kleiner als 10 sec ist, wird als zeit vom backup Reader interpretiert und nicht gezählt.
	
	if($rInfo['use_lID'] == 1) { $sql_lID = "and lid = $rennen "; } else { $sql_lID = ""; }
	
	$sql = "select nummer from zeit where vid = $veranstaltung $sql_lID and zeit > '".$rInfo['startZeit']."' group by nummer";
	
	$result = mysql_query($sql);
	if (!$result) { die('Invalid query: ' . mysql_error()); }
	
	$i = 0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$numbers[$i] = $row['nummer'];
		$i++;
	}
	
	foreach ($numbers as $number) {
		$result = "";
		$sql = "";
		$re2 = "";
		$sql2 = "";
		
		$sql = "select nummer, zeit from zeit where vid = $veranstaltung $sql_lID and zeit > '".$rInfo['startZeit']."' and nummer = $number order by zeit";
				
		$result = mysql_query($sql);
		if (!$result) { die('Invalid query: ' . mysql_error()); }
		
		$rowCount = 0;
		$startTime = "00:00:00";
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$dif = abs(getSeconds($row['zeit']) - getSeconds($startTime));
			
			if( $dif > 10 ) {
				$rowCount++;
			}
			$startTime = $row['zeit'];
		}

		$sql2 = "update teilnehmer set aut_runden = ".$rowCount." where stnr = ".$number." and vid = $veranstaltung and lid = $rennen";
		//echo $sql."<br>";
		$res2 = mysql_query($sql2);
		if (!$res2) { die('Invalid query: ' . mysql_error()); }
		
	}

	$sql = "update teilnehmer set runden = aut_runden + man_runden where vid = $veranstaltung and lid = $rennen";
	$res = mysql_query($sql);
	if (!$res) { die('Invalid query: ' . mysql_error()); }		
	
	
/*
	if($rInfo['use_lID'] == 1) { $sql_lID = "and lid = $rennen "; } else { $sql_lID = ""; }
	
	$sql = "select nummer, count(nummer) as Runden from zeit where vid = $veranstaltung $sql_lID and zeit > '".$rInfo['startZeit']."' group by nummer";
	
	$result = mysql_query($sql);
	if (!$result) { die('Invalid query: ' . mysql_error()); }
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$sql = "update teilnehmer set aut_runden = ".$row['Runden']." where stnr = ".$row['nummer']." and vid = $veranstaltung and lid = $rennen";
		//echo $sql."<br>";
		$res = mysql_query($sql);
		if (!$res) { die('Invalid query: ' . mysql_error()); }
	}
	
	$sql = "update teilnehmer set runden = aut_runden + man_runden where vid = $veranstaltung and lid = $rennen";
	$res = mysql_query($sql);
	if (!$res) { die('Invalid query: ' . mysql_error()); }
*/
}

function updateStatus($veranstaltung, $rennen) {
	$timestamp = date("YmdHis", time());
	$sql = "update lauf set aktualisierung = $timestamp where vid = $veranstaltung and id = $rennen";
	$res = mysql_query($sql) or die("Anfrage fehlgeschlagen: " . mysql_error());
}
