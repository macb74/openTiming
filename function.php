<?php
/*
 * Created on 06.11.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include "config.php";
include "veranstaltung.php";
include "teilnehmer.php";
include "rennen.php";
include "klasse.php";
include "startliste.php";
include "auswertung.php";
include "ergebnis.php";
include "import.php";
include "einlaufListe.php";
include "ziel.php";
include "reader.php";
include "chat.php";
include "help.php";
include "statistic.php";

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
	
    if (!$link->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));")) {
        printf("Error: %s\n", $link->error);
    }
    
	//SET GLOBAL general_log_file = 'mysql_general.log';
	//SET GLOBAL general_log = 'ON';
	//SET GLOBAL general_log = 'OFF';

	
	/*
	Funktioniert nicht zur Laufzeit - variablen müssen in der my.ini stehen
	$link->query("SET GLOBAL slow_query_log = 'ON';");
	$link->query("SET GLOBAL long_query_time = 0;");
	$link->query("SET GLOBAL slow-query-log-file = 'd:/mysql_sloq_query_log.log';");
	*/
	
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
		echo htmlspecialchars($link->error)."<br>\n";
	}

	
	if($res && $action == 'SELECT') {
		$result[1] = $res->num_rows;
		$i = 0;
		while ($row = $res->fetch_assoc()) {
			foreach($row as $key => $value) {
			    $result[0][$i][$key] = htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');
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
		$rd['startZeit'] 	    = $row['start'];
		$rd['teamAnz'] 		    = $row['team_anz'];
		$rd['rundenrennen']	    = $row['rundenrennen'];
		$rd['use_lID']		    = $row['use_lID'];
		$rd['teamrennen']	    = $row['teamrennen'];
		$rd['rdVorgabe']	    = $row['rdVorgabe'];
		$rd['showLogo']		    = $row['showLogo'];
		$rd['mainReaderIp']	    = $row['mainReaderIp'];
		$rd['titel']	        = $row['titel'];
		$rd['untertitel']	    = $row['untertitel'];
		$rd['vID']		     	= $row['vID'];
		$rd['lockRace']		    = $row['lockRace'];
		$rd['teamAtt']	     	= $row['teamAtt'];
		$rd['teamAttVal']	    = $row['teamAttVal'];
		$rd['roc']	            = $row['roc'];
		$rd['teamTogetherWith'] = $row['teamTogetherWith'];
		$rd['teamDeaktivated']  = $row['teamDeaktivated'];
	}
	return $rd;
}


function setConfig() {
	$sql    = "update `config` set `value` = '".$_POST['value']."' where `key` = '".$_POST['key']."';";
	echo $sql;
	$result = dbRequest($sql, 'INSERT');
	//print_r($result);
}


function getConfig($string) {
	$sql    = "select * from config where `key` like '".$string."';";
	$result = dbRequest($sql, 'SELECT');

	foreach ($result[0] as $row) {
	    if($row['value'] == "") {
	        $config[$row['key']] = $row['value_txt'];
	    } else {
	        $config[$row['key']] = $row['value'];
	    }
	}
	
	return $config;
}

function filterParameters($array) {
	global $link;
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
		$array = $link->real_escape_string($array);
	}
 	return $array;
	 
}

function checkTeamTogetherWith($rennen, $teamTogetherWithString) {
	if ($teamTogetherWithString != '' && $teamTogetherWithString != '[""]') {
		$teamTogetherWith = json_decode($teamTogetherWithString, true);
		foreach ($teamTogetherWith as $item) {
			$rennen = $rennen.",".$item;
		}
  	}
	return $rennen;
}
?>
