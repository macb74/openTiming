<?php
/*
 * Created on 06.11.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include "config.php";
include "veranstaltungen.php";
include "teilnehmer.php";
include "rennen.php";
include "klasse.php";
include "menue.php";
include "startliste.php";
include "auswertung.php";
include "ergebnis.php";
include "urkunden.php";
include "import.php";
include "einlaufListe.php";

function table($title, $content) {
	$html  = "<table class=\"deco-box\" width=\"100%\" cellspacing=\"0\" >\n";
	$html .= "  <thead>\n";
	$html .= "     <tr>\n";
	$html .= "         <th class=\"deco-box-left\"   >&nbsp;</th>\n";
	$html .= "         <th class=\"deco-box-middle\" >$title</th>\n";
	$html .= "         <th class=\"deco-box-right\"  >&nbsp;</th>\n";
	$html .= "     </tr>\n";
	$html .= " </thead>\n";
	$html .= " 	<tbody>\n";
	$html .= " 	<tr class=\"content\" >\n";
	$html .= " 	    <td class=\"deco-box-left\" >&nbsp;</td>\n";
	$html .= " 	    <td class=\"deco-box-middle\" >\n";

	$html .= $content;	

	$html .= "	            </td>\n";
	$html .= "	            <td class=\"deco-box-right\"  >&nbsp;</td>\n";
	$html .= "	        </tr>\n";
	$html .= "	        <tr class=\"footer\" >\n";
	$html .= "	            <td class=\"deco-box-left\"   >&nbsp;</td>\n";
	$html .= "	            <td class=\"deco-box-middle\" >&nbsp;</td>\n";
	$html .= "	            <td class=\"deco-box-right\"  >&nbsp;</td>\n";
	$html .= "	        </tr>\n";
	$html .= "	    </tbody>\n";
	$html .= "	</table>\n";

	return $html;
}

function tableList($columns, $content, $class) {
	$html = "<table frame=\"void\" rules=\"rows\" class=\"$class\" id=\"race\">\n";
	$html .= "<thead>\n";
	$html .= "<tr>\n";
		foreach($columns as $c) {
			$html .= "<th align=\"left\">\n";
			$html .= $c."\n";
			$html .= "</th>\n";
		}
	$html .= "<tbody>\n";

	$html .= $content;

	$html .= "</tbody></table>\n";
	return $html;	
}

function connectDB() {
	global $config;
	
	$link = new mysqli($config['dbhost'], $config['dbuser'], $config['dbpassword'], $config['dbname']);
	if ($link->connect_errno) {
		printf("Connect failed: %s\n", $link->connect_error);
		exit();
	}
	
	if (!$link->query("SET NAMES 'utf8'")) {
        printf("Error: %s\n", $link->error);
    }
	
	if (!$link->query("SET CHARACTER SET 'utf8'")) {
        printf("Error: %s\n", $link->error);
    }
	
	return $link;
}

function dbRequest($sql, $action) {
	/*
	 * 0 = Ergebnis des Select Statement - true/false bei INSERT, UPDATE, DELETE
	 * 1 = Anzahl der Zeilen bei SELECT
	 * 2 = Fehlermeldung
	 * 3 = ID des Datensatz bei INSERT
	 */
	
	global $link;
	if(!$link) { echo "keine DB Verbindung"; }
	
	$result[0] = false;
	$result[1] = false;
	$result[2] = false;
	$result[3] = false;
	
	//echo htmlspecialchars($sql)."<br>";
	
	$res = $link->query($sql);
	if ($link->error) {
		$result[2] = $link->error;
		//echo htmlspecialchars($link->error)."<br>";
	}

	
	if($res && $action == 'SELECT') {
		$result[1] = $res->num_rows;
		$i = 0;
		while ($row = $res->fetch_assoc()) {
			foreach($row as $key => $value) {
				$result[0][$i][$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
			}
			$i++;
		}
		$res->close();
	}
	
	if($action == 'INSERT') {
		$result[3] = $link->insert_id;
	}
	
	if(!$link->error && $action != 'SELECT') {
		$result[0] = true;
	}

	//$link->close();
	return $result;
}


function checkIfVeranstaltungIsSelected() {
	if (isset($_SESSION['vID'])) {
		return true;
	} else {
		return false;
	}
}

function getRennenData($rennen) {
	
	$sql = "select * from lauf where id = $rennen";
	$result = dbRequest($sql, 'SELECT');
	
	foreach ($result[0] as $row) {
		$rd['startZeit'] 	= $row['start'];
		$rd['teamAnz'] 		= $row['team_anz'];
		$rd['rundenrennen']	= $row['rundenrennen'];
		$rd['use_lID']		= $row['use_lID'];
		$rd['teamrennen']	= $row['teamrennen'];
		$rd['rdVorgabe']	= $row['rdVorgabe'];
		$rd['showLogo']		= $row['showLogo'];
		$rd['mainReaderIp']	= $row['mainReaderIp'];
		$rd['titel']		= $row['titel'];
		$rd['untertitel']	= $row['untertitel'];
	}
	return $rd;
}


function filterParameters($array) {
	
	$link = connectDB();
	if(is_array($array)) {
		foreach($array as $key => $value) {
			if(is_array($array[$key])) {
				$array[$key] = filterParameters($array[$key]);
			}
			if(is_string($array[$key])) {
				$array[$key] = $link->real_escape_string($array[$key]);
			}
		}
	}
	if(is_string($array)) {
		$array = mysql_real_escape_string($array);
	}
	$link->close();
 	return $array;
	 
}
?>
