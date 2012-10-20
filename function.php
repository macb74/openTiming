<?php
/*
 * Created on 06.11.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once("xajax_core/xajax.inc.php");
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
	$link = mysql_connect($config['dbhost'], $config['dbuser'], $config['dbpassword']);
		if (!$link) {
    		die('Could not connect: ' . mysql_error());
		}

	$db_selected = mysql_select_db($config['dbname'], $link);
		if (!$db_selected) {
   			die ('Can\'t use foo : ' . mysql_error());
		}
		
	mysql_query("SET NAMES 'utf8'");
	mysql_query("SET CHARACTER SET 'utf8'");
	return $link;
}

function checkIfVeranstaltungIsSelected() {
	if (isset($_SESSION['vID'])) {
		return true;
	} else {
		return false;
	}
}

function clearDiv() {
		$objResponse = new xajaxResponse();
		$html ="";
		$objResponse->assign('data_div', 'innerHTML', $html);
		return $objResponse;	
}

function getRennenData($rennen) {
	$sql = "select * from lauf where id = $rennen";
	
	$result = mysql_query($sql);
	if (!$result) { die('Invalid query: ' . mysql_error()); }
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$rd['startZeit'] 	= htmlspecialchars_decode($row['start'], ENT_QUOTES);
		$rd['teamAnz'] 		= $row['team_anz'];
		$rd['rundenrennen']	= $row['rundenrennen'];
		$rd['use_lID']		= $row['use_lID'];
		$rd['teamrennen']	= $row['teamrennen'];
		$rd['rdVorgabe']	= $row['rdVorgabe'];
		$rd['showLogo']		= $row['showLogo'];
		$rd['mainReaderIp']	= htmlspecialchars_decode($row['mainReaderIp'], ENT_QUOTES);
		$rd['titel']		= htmlspecialchars_decode($row['titel'], ENT_QUOTES);
		$rd['untertitel']	= htmlspecialchars_decode($row['untertitel'], ENT_QUOTES);
		
	}
	return $rd;
}


function filterParameters($array) {
	
	if(is_array($array)) {
		foreach($array as $key => $value) {
			if(is_array($array[$key])) {
				$array[$key] = filterParameters($array[$key]);
			}
			if(is_string($array[$key])) {
				$array[$key] = mysql_real_escape_string($array[$key]);
			}
		}
	}
	if(is_string($array)) {
		$array = mysql_real_escape_string($array);
	}
 	return $array;
	 
}
?>
