<?php

function startliste() {
	global $func;
	$html="";

	$html = startlisteForm($html);
	$html .= "<div id='data_div'></div>";
	return table("Startliste", $html);
}

function startlisteForm($html) {

	global $func;

	# Display Rennen
	//$html = "";
	$veranstaltung = $_SESSION['vID'];
	$sql = "select * from lauf where vID = $veranstaltung order by start asc, titel;";
	$result = dbRequest($sql, 'SELECT');

	$html2 = "";
	$i=1;
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			$sql = "select count(ID) as anz from teilnehmer where del = 0 and vID = $veranstaltung and lID = ".$row['ID'];
			$resultCount = dbRequest($sql, 'SELECT');
			foreach ($resultCount[0] as $rowCount) {
				$anzTeilnehmer = $rowCount['anz'];
			}
			
			if($i%2 == 0) { $html2 .= "<tr class=\"even\">\n"; } else { $html2 .= "<tr class=\"odd\">\n"; }
	
			$subtitle = "";
			if ($row['untertitel'] != "") { $subtitle = "<i>- ".$row['untertitel']."</i>"; }
			$html2 .= "<td width=\"30\" align\"left\">".$row['ID']."</td>\n";
			$html2 .= "<td align\"left\">".$row['titel']." $subtitle ($anzTeilnehmer)</td>\n";
			//$html2 .= "<td align\"left\">".$row['untertitel']."</td>\n";
			$html2 .= "<td align\"left\">".$row['start']."</td>\n";
			$html2 .= "<td align\"center\">" .
					"<a id=\"showInDiv\" href=\"jqRequest&func=showStartList&lid=".$row['ID']."\" >Startliste</a>" .
					"&nbsp;&nbsp; | &nbsp;&nbsp;" .
					"<a id=\"showInDiv\" href=\"jqRequest&func=showStartWithoutKl&lid=".$row['ID']."\" >Teilnehmer ohne Klasse</a>" .
					"&nbsp;&nbsp; | &nbsp;&nbsp;" .
			//				"<a href=\"exportPDF.php?aktion=ergebnisKlasse&id=".$row['ID']."\">PDF nach Klassen</a>" .
			//				"&nbsp;&nbsp; | &nbsp;&nbsp;" .
					"<a href=\"exportPDF.php?action=startliste&id=".$row['ID']."&sort=stnr\" target=\"_new\">PDF (StNr.)</a>" .
					"&nbsp;&nbsp; | &nbsp;&nbsp;" .
					"<a href=\"exportPDF.php?action=startliste&id=".$row['ID']."&sort=nachname\" target=\"_new\">PDF (Name)</a>" .
					"&nbsp;&nbsp; | &nbsp;&nbsp;" .
					"<a href=\"exportXLS.php?action=startliste\">Excel (Gesamt)</a>" .		
					"</td>\n";
			$html2 .= "</tr>\n";
			$i++;
		}
	}

	$columns = array('ID', 'Titel', 'Start', 'Aktion');
	$html .= tableList($columns, $html2, "common meetings");
		
	return $html;
}

function showStartResult($rennen) {
	
	$html = "<br>";
	$html = "<p><a href=\"#\" onClick=\"clearDiv(); return false;\">clear</a></p>";
	$sql = "SELECT t.*, l.titel FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
		"where t.vID = ".$_SESSION['vID']." ".
			"and t.lid = $rennen and del= 0 and disq = 0 ".
			"order by stnr asc;";
	$result = dbRequest($sql, 'SELECT');

	$html2 = "";
	$i=1;
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			if($i%2 == 0) { $html2 .= "<tr class=\"even\">\n"; } else { $html2 .= "<tr class=\"odd\">\n"; }
			$html2 .= "<td align\"left\">".$row['stnr']."</td>\n";
			$html2 .= "<td align\"left\"><a href=\"".$_SERVER["SCRIPT_NAME"]."?func=teilnehmer.edit&ID=".$row['ID']."&nextUrl=".base64_encode($_SERVER["SCRIPT_NAME"]."?func=startliste")."\">".$row['nachname'].", ".$row['vorname']."</a></td>\n";
			$html2 .= "<td align\"left\">".$row['verein']."</td>\n";
			$html2 .= "<td align\"left\">".$row['jahrgang']."</td>\n";
			$html2 .= "<td align\"left\">".$row['geschlecht']."</td>\n";
			$html2 .= "<td align\"left\">".$row['klasse']."</td>\n";
			$html2 .= "<td align\"left\">".$row['att']."</td>\n";
			$html2 .= "<td align\"left\">".$row['titel']."</td>\n";
			$html2 .= "</tr>\n";
			$i++;
		}
	}

	$columns = array('Stnr', 'Name', 'Verein', 'JG', 'G', 'Klasse', 'Att', 'Rennen');
	$html .= tableList($columns, $html2, "common");

	return $html;
}

function showStartWithoutKl($rennen) {

	$html = "<br>";
	$html = "<p><a href=\"#\" onClick=\"clearDiv(); return false;\">clear</a></p>";
	$sql = "SELECT t.*, l.titel FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
		"where t.vID = ".$_SESSION['vID']." ".
			"and t.lid = $rennen and t.del= 0 and t.disq = 0 and t.klasse = '' ".
			"order by stnr asc;";
	$result = dbRequest($sql, 'SELECT');

	$html2 = "";
	$i=1;
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			if($i%2 == 0) { $html2 .= "<tr class=\"even\">\n"; } else { $html2 .= "<tr class=\"odd\">\n"; }
			$html2 .= "<td align\"left\">".$row['stnr']."</td>\n";
			$html2 .= "<td align\"left\"><a href=\"".$_SERVER["SCRIPT_NAME"]."?func=teilnehmer.edit&ID=".$row['ID']."&nextUrl=".base64_encode($_SERVER["SCRIPT_NAME"]."?func=startliste")."\">".$row['nachname'].", ".$row['vorname']."</a></td>\n";
			$html2 .= "<td align\"left\">".$row['verein']."</td>\n";
			$html2 .= "<td align\"left\">".$row['jahrgang']."</td>\n";
			$html2 .= "<td align\"left\">".$row['geschlecht']."</td>\n";
			$html2 .= "<td align\"left\">".$row['klasse']."</td>\n";
			$html2 .= "<td align\"left\">".$row['titel']."</td>\n";
			$html2 .= "</tr>\n";
			$i++;
		}
	}

	$columns = array('Stnr', 'Name', 'Verein', 'JG', 'G', 'Klasse', 'Rennen');
	$html .= tableList($columns, $html2, "common");

	return $html;
}
