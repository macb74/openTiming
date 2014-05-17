<?php

function urkunden() {
	global $func;
	$html="";

	$html = urkundenForm($html);
	$html .= "<div id='data_div'></div>";
	return table("Urkunden", $html);
}

function urkundenForm($html) {

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
			if($i%2 == 0) { $html2 .= "<tr class=\"even\">\n"; } else { $html2 .= "<tr class=\"odd\">\n"; }
	
			$html2 .= "<td width=\"30\" align\"left\">".$row['ID']."</td>\n";
			$html2 .= "<td align\"left\">".$row['titel']."</td>\n";
			//$html2 .= "<td align\"left\">".$row['untertitel']."</td>\n";
			$html2 .= "<td align\"left\">".$row['start']."</td>\n";
			$html2 .= "<td align\"left\">".$row['aktualisierung']."</td>\n";
			$html2 .= "<td align\"center\">" .
			//				"<a href=\"exportPDF.php?aktion=ergebnisKlasse&id=".$row['ID']."\">PDF nach Klassen</a>" .
			//				"&nbsp;&nbsp; | &nbsp;&nbsp;" .
					"<a href=\"urkundenPDF.php?action=gesamt&num=6&id=".$row['ID']."\" target=\"_new\">Gesamt (6)</a>" .
					"&nbsp;&nbsp; | &nbsp;&nbsp;" .
					"<a href=\"urkundenPDF.php?action=klasse&num=3&id=".$row['ID']."\" target=\"_new\">Klasse (3)</a>" .
					"&nbsp;&nbsp; | &nbsp;&nbsp;" .
					"<a href=\"urkundenPDF.php?action=klasse&num=6&id=".$row['ID']."\" target=\"_new\">Klasse (6)</a>" .
					"&nbsp;&nbsp; | &nbsp;&nbsp;" .
					"<a href=\"urkundenPDF.php?action=klasse&num=10000&id=".$row['ID']."\" target=\"_new\">Klasse (alle)</a>" .
					"&nbsp;&nbsp; | &nbsp;&nbsp;" .
					"<a href=\"urkundenPDF.php?action=team&num=10000&id=".$row['ID']."\" target=\"_new\">Team (alle)</a>" .
			
			//				"&nbsp;&nbsp; | &nbsp;&nbsp;" .
							"</td>\n";
			$html2 .= "</tr>\n";
			$i++;
		}
	}

	$columns = array('ID', 'Titel', 'Start', 'Aktualisierung', 'Aktion');
	$html .= tableList($columns, $html2, "common meetings");
		
	return $html;
}

