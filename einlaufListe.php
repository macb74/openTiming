<?php

function einlaufListe() {
	global $func;
	$html="";
		
	$html = einlaufListeForm($html);
	$html .= "<div id='data_div'></div>";
	return table("Einlauf Liste", $html);
}

function einlaufListeForm($html) {

	global $func;
	
	# Display Rennen
	//$html = "";
	$veranstaltung = $_SESSION['vID'];
	$sql = "select * from lauf where vID = $veranstaltung order by start asc, titel;";
	$result = dbRequest($sql, 'SELECT');
	
	if(!isset($_SESSION['rennenIDs']) || $_SESSION['rennenIDs'] == '') {
		$rennenIDs = array();
	} else {
		$rennenIDs = explode( ",", $_SESSION['rennenIDs']);
	}

	$html2 = "";
	$i=1;
	foreach ($result[0] as $row) {
		if($i%2 == 0) { $html2 .= "<tr class=\"even\">\n"; } else { $html2 .= "<tr class=\"odd\">\n"; }

		$sql = "select count(ID) as anz from teilnehmer where platz <> 0 and vID = $veranstaltung and lID = ".$row['ID'];
		$resultCount = dbRequest($sql, 'SELECT');
		
		foreach ($resultCount[0] as $rowCount) {
			$anzTeilnehmer = $rowCount['anz'];
		}
		
		$subtitle = "";
		if ($row['untertitel'] != "") { $subtitle = "<i>- ".$row['untertitel']."</i>"; }
		$html2 .= "<td width=\"30\" align=\"left\">".$row['ID']."</td>\n";
		$html2 .= "<td width=\"250\" align=\"left\">".$row['titel']." $subtitle ($anzTeilnehmer)</td>\n";
		//$html2 .= "<td align=\"left\">".$row['untertitel']."</td>\n";
		$html2 .= "<td width=\"150\" align=\"left\">".$row['start']."</td>\n";
		$html2 .= "<td width=\"200\" align=\"left\">".$row['aktualisierung']."</td>\n";
		if(in_array($row['ID'], $rennenIDs) === true) { $c = "checked"; } else { $c = ""; }
		$html2 .= "<td align=\"left\"><input $c class=\"chkboxtable\" type=\"checkbox\" name=\"".$row['ID']."\" value=\"jqRequest&func=showEinlaufListe&lid=".$row['ID']."\" id=\"".$row['ID']."\"></td>\n";
		$html2 .= "</tr>\n";
		$i++;
	}

	$columns = array('ID', 'Titel', 'Start', 'Aktualisierung', 'show');
	$html .= tableList($columns, $html2, "common meetings");
	
	return $html;
}

function showEinlaufListe($rennen, $action) {
	
	?>
	<script type="text/javascript" src="js/einlaufListe.js"></script>
	<?php
	
	// der anzuzeigenden rennen werden in der Variable $_SESSION['rennenIDs'] gespeichert
	if(!isset($_SESSION['rennenIDs']) || $_SESSION['rennenIDs'] == '') { 
		$rennenIDs = array(); 
	} else {
		$rennenIDs = explode( ",", $_SESSION['rennenIDs']);		
	}

	if($action == 'add') {
		if(in_array($rennen, $rennenIDs) === false) {
			array_push( $rennenIDs , $rennen );
		}
	} else {
		if(in_array($rennen, $rennenIDs) === true) {
			$id = array_search($rennen, $rennenIDs);
			unset($rennenIDs[$id]);
		}
	}
	
	$_SESSION['rennenIDs'] = implode(",", $rennenIDs);
	//echo $_SESSION['rennenIDs'];
			
	$html = "<br>";
	$html = "<p><a href=\"#\" onClick=\"clearDiv()\">clear</a></p>";
	
	
	if($_SESSION['rennenIDs'] != "") {
		$i = 0;
		$rIDs = explode( ",", $_SESSION['rennenIDs']);
		$tmptable = "tmp_".rand(100, 999);
		
		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $tmptable (
		`ID` bigint(20) NOT NULL DEFAULT '0',
		`stnr` int(11) NOT NULL,
		`vorname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`nachname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`verein` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`klasse` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
		`lid` int(11) NOT NULL,
		`lname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		`usemantime` int(1) NOT NULL DEFAULT '0',
		`manzeit` time NOT NULL DEFAULT '00:00:00',
		`zielzeit` datetime,
		`millisecond` int(3),
		`startzeit` datetime,
		PRIMARY KEY (`ID`)
		) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		
		$result = dbRequest($sql, 'INSERT');
		
		foreach ($rIDs as $rid) {
			$rd = getRennenData($rid);
			$startZeit = $rd['startZeit'];
			if($rd['use_lID'] == 1) { $sql_lID = "and lid = $rid "; } else { $sql_lID = ""; }
			
			$sql = "INSERT into $tmptable (SELECT t.ID, t.stnr, t.vorname, t.nachname, t.verein, t.klasse, t.lid, '".$rd['titel']."' as lname, t.usemantime, t.manzeit, min(z.zeit) as zielzeit, z.millisecond, '".$startZeit."' as startzeit FROM `teilnehmer` as t 
				LEFT JOIN (select nummer, zeit, millisecond from zeit where zeit > '".$startZeit."' and vID = ".$_SESSION["vID"]." $sql_lID) as z ON t.stnr = z.nummer 
				where t.vid = ".$_SESSION["vID"]." and t.lid = ".$rid." and disq = 0 and del = 0 group by t.stnr);";
			
			//echo $sql;
			$result = dbRequest($sql, 'INSERT');
			$i++;
		}
		
		// Zielzeit für manuell gesetzte Laufzeit berechnen und eintragen
		$sql = "select * from $tmptable where usemantime = 1";
		$result = dbRequest($sql, 'SELECT');
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				$zielzeit = getSeconds($row['startzeit']) + getSeconds('1970-01-01 '.$row['manzeit']);
				date_default_timezone_set("UTC");
				$zielzeit = date("Y-m-d H:i:s", $zielzeit);
				date_default_timezone_set("Europe/Berlin");
				$sql = "update $tmptable set zielzeit = '".$zielzeit."' where ".$row['ID']." = ID";
				$result = dbRequest($sql, 'UPDATE');
			}
		}

 		// um richtig sortieren zu können werden die Zeiten in der spalte manzeit zusammengefasst.
 		$sql = "update $tmptable set manzeit = zielzeit where zielzeit is not NULL and usemantime <> 2";
 		$result = dbRequest($sql, 'UPDATE');
		
		$sql = "SELECT * from $tmptable where zielzeit is not NULL order by manzeit, millisecond asc;";
		$result = dbRequest($sql, 'SELECT');
		
		$html2 = "";
		$i=1;
		$dataSetBefore['zeit'] = 'none';
		$dataSetBefore['klasse'] = 'none';
		
		$sameTimeAsBefore ='';
	
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				$laufzeit = getRealTime($row['startzeit'], $_SESSION['vDatum']." ".$row['manzeit']);
				if($row['usemantime'] == 1 ) { $umt = '*'; } else { $umt = ''; }
				if($i%2 == 0) { $html2 .= "<tr class=\"even highlight\">\n"; } else { $html2 .= "<tr class=\"odd highlight\">\n"; }
				$html2 .= "<td align=\"left\"><a href=\"".$_SERVER["SCRIPT_NAME"]."?func=teilnehmer.edit&ID=".$row['ID']."&nextUrl=".base64_encode($_SERVER["SCRIPT_NAME"]."?func=einlaufListe")."\">".$row['nachname'].", ".$row['vorname']."</a></td>\n";
				$html2 .= "<td align=\"left\">".$row['verein']."</td>\n";
				$html2 .= "<td align=\"left\">".$row['klasse']."</td>\n";
				$html2 .= "<td align=\"left\">".$row['lname']."</td>\n";
				if (($dataSetBefore['zeit'] == $laufzeit) && ($dataSetBefore['klasse'] == $row['klasse'])) { $sameTimeAsBefore = 'style="font-weight:bold"'; } else { $sameTimeAsBefore = ''; }
				$html2 .= "<td align=\"left\" $sameTimeAsBefore >".$laufzeit.$umt."</td>\n";
				$html2 .= "<td align=\"left\">".$row['stnr']."</td>\n";
				$html2 .= "<td align=\"left\"><input id=\"zeit_".$row['ID']."\" class=\"inputZielzeit\" value=\"".$row['manzeit']."\">";
				$html2 .= "&nbsp;&nbsp;<span><a class=\"setmanzeit\" id=\"".$row['ID']."\" href=\"jqRequest&func=saveManZielzeit&id=".$row['ID']."&action=save\"><i class=\"fa fa-floppy-o fa-lg\"></i></a>";
				if ($row['usemantime'] == 2 ) {
				$html2 .= "&nbsp;&nbsp;|&nbsp;&nbsp;<a class=\"setmanzeit\" id=\"".$row['ID']."\" href=\"jqRequest&func=saveManZielzeit&id=".$row['ID']."&action=del\"><i class=\"fa fa-times fa-lg\"></i></a></span></td>\n";
				}
				$dataSetBefore['zeit'] = $laufzeit;
				$dataSetBefore['klasse'] = $row['klasse'];
				
				$html2 .= "</tr>\n";
				$i++;
			}
		}
	
		
		$sql = "SELECT * from $tmptable where zielzeit is NULL";
		
		$result = dbRequest($sql, 'SELECT');
		
		$dataSetBefore['zeit'] = 'none';
		$dataSetBefore['klasse'] = 'none';
		
		$sameTimeAsBefore ='';
		
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				$laufzeit = '00:00:00';
				if($row['usemantime'] == 1 ) { $umt = '*'; $laufzeit = $row['manzeit']; } else { $umt = ''; }
				if($i%2 == 0) { $html2 .= "<tr class=\"even highlight\">\n"; } else { $html2 .= "<tr class=\"odd highlight\">\n"; }
				$html2 .= "<td align=\"left\"><a href=\"".$_SERVER["SCRIPT_NAME"]."?func=teilnehmer.edit&ID=".$row['ID']."&nextUrl=".base64_encode($_SERVER["SCRIPT_NAME"]."?func=einlaufListe")."\">".$row['nachname'].", ".$row['vorname']."</a></td>\n";
				$html2 .= "<td align=\"left\">".$row['verein']."</td>\n";
				$html2 .= "<td align=\"left\">".$row['klasse']."</td>\n";
				$html2 .= "<td align=\"left\">".$row['lname']."</td>\n";
				$html2 .= "<td align=\"left\">".$laufzeit.$umt."</td>\n";
				$html2 .= "<td align=\"left\">".$row['stnr']."</td>\n";
				$html2 .= "<td align=\"left\"><input id=\"zeit_".$row['ID']."\" class=\"inputZielzeit\" value=\"00:00:00\">";
				$html2 .= "&nbsp;&nbsp;<span><a class=\"manzeit\" id=\"".$row['ID']."\" href=\"jqRequest&func=saveManZielzeit&id=".$row['ID']."&action=save\"><i class=\"fa fa-floppy-o fa-lg\"></i></a>";
									
				$dataSetBefore['zeit'] = $laufzeit;
				$dataSetBefore['klasse'] = $row['klasse'];
					
				$html2 .= "</tr>\n";
				$i++;
			}
		}
		
		
		
		$columns = array('Name', 'Verein', 'Klasse', 'Rennen', 'Laufzeit', 'Stnr', 'Zielzeit');
		$html .= tableList($columns, $html2, "common einlaufListe");
	
	}
	return $html;
}

function saveManZielzeit($id, $action, $time) {
	if ($action == 'save') {
		$sql = "update teilnehmer set usemantime = 2, manzeit = '".base64_decode($time)."' where id = $id";
	} elseif ($action == 'del') {
		$sql = "update teilnehmer set usemantime = 0, manzeit = '' where id = $id";
	}
	$result = dbRequest($sql, 'UPDATE');
	return "";
}

