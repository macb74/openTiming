<?php
/*
 * Created on 06.11.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function menue() {
	$check = checkIfVeranstaltungIsSelected();

	
	echo "<script type=\"text/javascript\">\n";
	echo "<!--\n";
	echo "function openWindow(url) {\n";
	echo " fenster = window.open(url, \"fenster1\", \"width=300,height=600,status=no,scrollbars=yes,locationbar=no,resizable=yes\");\n";
   	echo " fenster.focus();\n";
	echo "}\n";
	echo "//-->\n";
	echo "</script>\n";
	
	
	echo "	<table cellpadding=\"0\" cellspacing=\"1\">\n";
	echo "		<tr><td class=\"smd-menu-top\">Verwaltung</td></tr>\n";
	echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=veranstaltungen\" class=\"smd-menu\" target=\"_self\" title=\"Verwaltung der Veranstaltungen\">&nbsp;&nbsp;Veranstaltungen</a></td></tr>\n";
	if ($check == 1) {
		echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=rennen\" class=\"smd-menu\" target=\"_self\" title=\"Verwaltung der Rennen\">&nbsp;&nbsp;Rennen</a></td></tr>\n";
		echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=klasse\" class=\"smd-menu\" target=\"_self\" title=\"Verwaltung der Klassen\">&nbsp;&nbsp;Klassen</a></td></tr>\n";
	}
	echo "	</table>\n";

	if ($check == 1) {
		echo "	<table cellpadding=\"0\" cellspacing=\"1\">\n";
		echo "		<tr><td class=\"smd-menu-top\">Teilnehmer</td></tr>\n";
		echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=teilnehmer\" class=\"smd-menu\" target=\"_self\" title=\"Verwaltung der Teilnehmerdaten.\">&nbsp;&nbsp;Teilnehmerliste</a></td></tr>\n";
		echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=teilnehmer.insert\" class=\"smd-menu\" target=\"_self\" title=\"Verwaltung der Teilnehmerdaten.\">&nbsp;&nbsp;Teilnehmereingabe</a></td></tr>\n";
		echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=import.teilnehmer\" class=\"smd-menu\" target=\"_self\" title=\"Die vorhanden Teilnehmerliste ersetzen oder erweitern.\">&nbsp;&nbsp;Teilnehmerliste einlesen</a></td></tr>\n";
		//		echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=unknownTag\" class=\"smd-menu\" target=\"_self\" title=\"Unbekannte TAGs anzeigen und zuordnen.\">&nbsp;&nbsp;Unbekannte TAGs</a></td></tr>\n";
		echo "	</table>\n";

		echo "	<table cellpadding=\"0\" cellspacing=\"1\">\n";
		echo "		<tr><td class=\"smd-menu-top\">Zeit</td></tr>\n";
		echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=import.zeit\" class=\"smd-menu\" target=\"_self\" title=\"Zeit einlesen\">&nbsp;&nbsp;Zeitliste einlesen</a></td></tr>\n";
		echo "		<tr><td class=\"smd-menu-item\"><a href=\"#\" onclick=\"openWindow('timeTable.php'); return false\" class=\"smd-menu\" target=\"_self\" title=\"Zeit einlesen\">&nbsp;&nbsp;Einlaufliste</a></td></tr>\n";
		echo "	</table>\n";


		echo "	<table cellpadding=\"0\" cellspacing=\"1\">\n";
		echo "		<tr><td class=\"smd-menu-top\">Auswertung</td></tr>\n";
		echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=startliste\" class=\"smd-menu\" target=\"_self\" title=\"Startliste.\">&nbsp;&nbsp;Startliste</a></td></tr>\n";
		echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=einlaufListe\" class=\"smd-menu\" target=\"_self\" title=\"Einlaufliste.\">&nbsp;&nbsp;Einlaufliste</a></td></tr>\n";
		echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=auswertung\" class=\"smd-menu\" target=\"_self\" title=\"Auswertung.\">&nbsp;&nbsp;Auswertung</a></td></tr>\n";
		echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=ergebnis\" class=\"smd-menu\" target=\"_self\" title=\"Ergebnisse\">&nbsp;&nbsp;Ergebnisse</a></td></tr>\n";
		echo "		<tr><td class=\"smd-menu-item\"><a href=\"?func=urkunden\" class=\"smd-menu\" target=\"_self\" title=\"Urkunden\">&nbsp;&nbsp;Urkunden</a></td></tr>\n";
		echo "	</table>\n";
	}

}
?>