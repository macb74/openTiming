<?php

function ergebnis() {
	$xajax = new xajax();
	$xajax->register(XAJAX_FUNCTION, "showResult");
	$xajax->register(XAJAX_FUNCTION, "showResultM");
	$xajax->register(XAJAX_FUNCTION, "clearDiv");
	$xajax->processRequest();
	$xajax->printJavascript();
	
	global $func;
	$html="";
		
	$html = ergebninsForm($html);
	$html .= "<div id='data_div'></div>";
	return table("Ergebninsse", $html);
}

function ergebninsForm($html) {

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

		$sql = "select count(ID) as anz from teilnehmer where platz <> 0 and vID = $veranstaltung and lID = ".$row['ID'];
		$resultCount = mysql_query($sql);
		while ($rowCount = mysql_fetch_array($resultCount, MYSQL_ASSOC)) {
			$anzTeilnehmer = $rowCount['anz'];
		}
		
		$subtitle = "";
		if ($row['untertitel'] != "") { $subtitle = "<i>- ".$row['untertitel']."</i>"; }
		$html2 .= "<td width=\"30\" align\"left\">".$row['ID']."</td>\n";
		$html2 .= "<td align\"left\">".$row['titel']." $subtitle ($anzTeilnehmer)</td>\n";
		//$html2 .= "<td align\"left\">".$row['untertitel']."</td>\n";
		$html2 .= "<td align\"left\">".$row['start']."</td>\n";
		$html2 .= "<td align\"left\">".$row['aktualisierung']."</td>\n";
		$html2 .= "<td align\"center\">" .
				"<a href=\"#\" onClick=\"xajax_showResult(".$row['ID']."); return false;\">Ergebnins anzeigen</a>" .
				"&nbsp;&nbsp; | &nbsp;&nbsp;" .
				"<a href=\"exportPDF.php?action=ergebnisGesamt&id=".$row['ID']."\" target=\"_new\">PDF</a>" .
				"&nbsp;&nbsp; | &nbsp;&nbsp;" .
				"<a href=\"exportPDF.php?action=ergebnisKlasse&id=".$row['ID']."\" target=\"_new\">PDF Klasse</a>" .
				"&nbsp;&nbsp; | &nbsp;&nbsp;" .
				"<a href=\"exportXLS.php?action=ergebnis&id=".$row['ID']."\">Excel</a>";
				if($row['team_anz'] > 0) {
		$html2 .= "&nbsp;&nbsp; | &nbsp;&nbsp;" .
				"<a href=\"#\" onClick=\"xajax_showResultM(".$row['ID']."); return false;\">Mannschaft</a>" .
				"&nbsp;&nbsp; | &nbsp;&nbsp;" .
				"<a href=\"exportPDF.php?action=ergebninsMannschaft&id=".$row['ID']."\" target=\"_new\">PDF Mannschaft</a>" ;
				}
				if($row['rundenrennen'] != 0) {
		$html2 .= "&nbsp;&nbsp; | &nbsp;&nbsp;" .
				"<a href=\"exportRundenzeiten.php?id=".$row['ID']."\" target=\"_new\">Rundenzeiten</a>";
				}
		$html2 .= "</td>\n";
		$html2 .= "</tr>\n";
		$i++;
	}

	$columns = array('ID', 'Titel', 'Start', 'Aktualisierung', 'Aktion');
	$html .= tableList($columns, $html2, "common meetings");
	
	mysql_close($link);
			
	return $html;
}

function showResult($rennen) {
	$objResponse = new xajaxResponse();

	$link = connectDB();

	$rd = getRennenData($rennen);
	$sqlAddOn = "";
	if ($rd['rundenrennen'] == 1) { $sqlAddOn = "runden desc, "; }
	
	$html = "<br>";
	$html = "<p><a href=\"#\" onClick=\"xajax_clearDiv()\">clear</a></p>";
	$sql = "SELECT t.*, l.titel FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
		"where t.vID = ".$_SESSION['vID']." ".
			"and t.lid = $rennen and del= 0 and disq = 0 and platz > 0 ".
			"order by $sqlAddOn zeit, platz asc;";
	$result = mysql_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}	
	$html2 = "";
	$i=1;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if($row['useManTime'] == 1 ) { $umt = '*'; } else { $umt = ''; }
		if($row['man_runden'] != 0 ) { $mr = '*'; } else { $mr = ''; }
		if($i%2 == 0) { $html2 .= "<tr class=\"even\">\n"; } else { $html2 .= "<tr class=\"odd\">\n"; }
		$html2 .= "<td align\"left\">".$i."</td>\n";
		$html2 .= "<td align\"left\">".$row['stnr']."</td>\n";
		$html2 .= "<td align\"left\"><a href=\"".$_SERVER["SCRIPT_NAME"]."?func=teilnehmer.edit&ID=".$row['ID']."&nextUrl=".base64_encode($_SERVER["SCRIPT_NAME"]."?func=ergebnis")."\">".$row['nachname'].", ".$row['vorname']."</a></td>\n";
		$html2 .= "<td align\"left\">".$row['verein']."</td>\n";
		if ($rd['rundenrennen'] == 0) { $html2 .= "<td align\"left\">".$row['jahrgang']."</td>\n"; }
		if ($rd['rundenrennen'] == 0) { $html2 .= "<td align\"left\">".$row['geschlecht']."</td>\n"; }
		$html2 .= "<td align\"left\">".$row['klasse']."</td>\n";
		$html2 .= "<td align\"left\">".$row['titel']."</td>\n";
		if ($rd['rundenrennen'] != 0) { $html2 .= "<td align\"left\">".$row['runden'].$mr."</td>\n"; }
		$html2 .= "<td align\"left\">".$row['zeit'].$umt."</td>\n";
		$html2 .= "<td align\"left\">".$row['platz']."</td>\n";
		$html2 .= "<td align\"left\">".$row['akplatz']."</td>\n";
		$html2 .= "<td align\"left\"><a href=\"urkundenPDF.php?action=einzel&tid=".$row['ID']."\" target=\"_new\">Urkunde</a></td>\n";
		
		$html2 .= "</tr>\n";
		$i++;
	}
		
	if( $rd['rundenrennen'] != 0 )  {
		$columns = array('Rng.', 'Stnr', 'Name', 'Verein', 'Klasse', 'Rennen', 'Runden', 'Zeit', 'Platz', 'AK', 'Urkunde');
	} else {
		$columns = array('Rng.', 'Stnr', 'Name', 'Verein', 'JG', 'G', 'Klasse', 'Rennen', 'Zeit', 'Platz', 'AK', 'Urkunde');
	}
	$html .= tableList($columns, $html2, "common");
	
	mysql_close($link);
	
	$objResponse->assign('data_div', 'innerHTML', $html);
	return $objResponse;
}

function showResultM($rennen) {
	$objResponse = new xajaxResponse();

	$link = connectDB();
	
	$html = "<br>";
	$html = "<p><a href=\"#\" onClick=\"xajax_clearDiv()\">clear</a></p>";
	$sql = "SELECT t.verein, t.vnummer, t.vtime, t.vplatz, t.vklasse FROM `teilnehmer` as t ".
		"where t.vID = ".$_SESSION['vID']." ".
			"and t.lid = $rennen and del= 0 and disq = 0 and vplatz > 0 ".
			"group by vnummer order by vtime asc, vnummer";

	$result = mysql_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}	
	$html2 = "";
	$i=1;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

		if($i%2 == 0) { $html2 .= "<tr class=\"even\">\n"; } else { $html2 .= "<tr class=\"odd\">\n"; }

		$vnummer = $row['vnummer'];
		$sql2 = "SELECT nachname, vorname, zeit from teilnehmer " .
				"where lid = $rennen and del= 0 and disq = 0 and vnummer = '$vnummer' order by zeit";
		$res2 = mysql_query($sql2);
		if (!$res2) { die('Invalid query: ' . mysql_error()); }
		
		$html2 .= "<td align\"left\">".$i."</td>\n";
		$html2 .= "<td align\"left\">".$row['verein']."</td>\n";
		$html2 .= "<td align\"left\">".$row['vtime']."</td>\n";
		$html2 .= "<td align\"left\"><table border='0' cellspacing='0' >";
		while ($row2 = mysql_fetch_array($res2, MYSQL_ASSOC)) {
			$html2 .= "<tr><td width='200'>".$row2['nachname'].", ".$row2['vorname']."</td><td>".$row2['zeit']."</td></tr>";
		}
		$html2 .= "</table></td>\n";
		$html2 .= "<td align\"left\">".$row['vklasse']."</td>\n";
		$html2 .= "<td align\"left\">".$row['vplatz']."</td>\n";
		$html2 .= "</tr>\n";

		$i++;
	}

	$columns = array('Platz', 'Verein', 'Zeit', 'Name', 'Klasse', 'AK');
	$html .= tableList($columns, $html2, "common");
	
	mysql_close($link);
	
	$objResponse->assign('data_div', 'innerHTML', $html);
	return $objResponse;
}
