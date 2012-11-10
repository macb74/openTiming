<?php

require_once('fpdf/fpdf.php');
require_once('fpdi/fpdi.php');
include("function.php");
session_start();

$stnr=0;

$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);

class PDF extends FPDI
{
	function urkunde($action, $num, $id, $tid, $template, $uDefinition) {

                global $stnr;   # etwas haesslich, rausgabe der StNr per globaler Variable

		#$header = $this->getHeader($_SESSION['vID'], $id, $stnr);
		#$this->setHeader($header);
		$oldVnummer = "";

		if($action == 'gesamt') {
			$sql= "SELECT t.* from teilnehmer as t where t.vID = ".$_SESSION['vID']." and lid = $id and platz > 0 and platz <= $num order by geschlecht, platz";
		} elseif ($action == 'klasse') {
			$sql= "SELECT t.* from teilnehmer as t where t.vID = ".$_SESSION['vID']." and lid = $id and akplatz > 0 and akplatz <= $num order by klasse, platz";
		} elseif ($action == 'einzel') {
			$sql= "SELECT t.* from teilnehmer as t where t.vID = ".$_SESSION['vID']." and id = $tid";
		} elseif ($action == 'team') {
			$sql= "SELECT t.* from teilnehmer as t where t.vID = ".$_SESSION['vID']." and lid = $id and vnummer <> '' order by vplatz, zeit asc";
		} else {
			echo "keine action gewählt";
			die;
		}
			
		$result = mysql_query($sql);
		if (!$result) { die('Invalid query: ' . mysql_error()); }

		$this->setMyFont();

		$i = 1;
		if ($action != 'team') {
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
					
				$this->AddPage('Portrait', 'A4');
				
				if($template != '') {
					$this->setSourceFile($template);
					// import page 1
					$tplIdx = $this->importPage(1);
					// use the imported page and place it at point 10,10 with a width of 100 mm
					$this->useTemplate($tplIdx, 0, 0, 0);
				}
	
				if ($action == 'gesamt') {
					$platz = $row['platz'];
				} else {
					$platz = $row['akplatz'];
				}

                                $stnr=$row['stnr'];
	
				include($uDefinition);
	
				$i++;
	
			}
			
		} else {
			$i = -1;
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if ($row['vnummer'] != $oldVnummer) {
					$i++;
					$ii = 1;
					$teamRow[$i]['verein'] = $row['verein'];
					$teamRow[$i]['vplatz'] = $row['vplatz'];
					$teamRow[$i]['vtime']  = $row['vtime'];
					$teamRow[$i]['Name'][$ii] = $row['nachname'].", ".$row['vorname'];
					$ii++;
				} 
				if ($row['vnummer'] == $oldVnummer) {
					$teamRow[$i]['Name'][$ii] = $row['nachname'].", ".$row['vorname'];
					$ii++;
				}
				$oldVnummer = $row['vnummer'];
			}
			
			foreach ($teamRow as $row) {
				$this->AddPage('Portrait', 'A4');
	
				if($template != '') {
					$this->setSourceFile($template);
					// import page 1
					$tplIdx = $this->importPage(1);
					// use the imported page and place it at point 10,10 with a width of 100 mm
					$this->useTemplate($tplIdx, 0, 0, 0);
				}
	
				include($uDefinition);
	
				$i++;
	
			}
			
		}
	}


	function setHeader() {
	}

	function setMyFont() {
		$this->SetTextColor(0);
		$this->SetFont('Verdana','',10);
		//$this->SetDrawColor(0,0,0);
		//$this->SetLineWidth(.5);
	}

	function getHeader($veranstaltung, $rennen) {
		$sql = "select titel, untertitel, datum from veranstaltung where id = $veranstaltung";
		$result = mysql_query($sql);
		if (!$result) { die('Invalid query: ' . mysql_error()); }

		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$header['titel'] 		= $row['titel'];
			$header['untertitel'] 	= $row['untertitel'];
			$header['datum'] 		= $row['datum'];
		}

		$sql = "select titel, untertitel from lauf where id = $rennen";
		$result = mysql_query($sql);
		if (!$result) { die('Invalid query: ' . mysql_error()); }

		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$header['lauf'] 		= $row['titel'];
			$header['lauf2'] 		= $row['untertitel'];
		}

		return $header;
	}


	//	function Footer() {
	//	    $this->SetY(-15);
	//	    $this->SetFont('Verdana','I',8);
	//	    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	//	}

}

$link = connectDB();
//$filename = $_GET['action'].'.pdf';


#$pdf=new PDF();
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddFont('verdana','','verdana.php');
$pdf->AddFont('verdana','B','verdanab.php');

//$pdf->AddPage();

if(!isset($_GET['num'])) { $_GET['num'] = 0;}
if(!isset($_GET['id'])) { $_GET['id'] = 0;}
if(!isset($_GET['tid'])) { $_GET['tid'] = 0;}

if(isset($_GET['action'])) {
	$templates = getTemplate($_GET['action'], $_GET['id'], $_GET['tid']);
	$pdf->urkunde($_GET['action'], $_GET['num'],  $_GET['id'], $_GET['tid'], $templates['template'], $templates['definition']);
}

if(isset($_GET['id'])) { 
 $rData = getRennenData($_GET['id']);
 $filename = $rData['titel']."_".$rData['untertitel'].".pdf";
}

if($_GET['num']<10000) { $anzahl=$_GET['num']; } else { $anzahl="alle"; }

if($_GET['action'] == "gesamt") {
        $filename = "Urkunden_Gesamt_$anzahl-".$filename;       
} elseif($_GET['action'] == "klasse") {
        $filename = "Urkunden_Klasse_$anzahl-".$filename;       
} elseif($_GET['action'] == "einzel") {
        $filename = "Urkunde_Einzel-$stnr.pdf";       
} elseif($_GET['action'] == "team") {
        $filename = "Urkunden_Team_$anzahl-".$filename;       
}

$pdf->Output($filename,"I");

function getTemplate($action, $id, $tid) {
	$u['template'] = "";
	$u['definition'] = "";
	
	if($action == "einzel") {
		$sql = "SELECT l.uDefinition, l.uTemplate FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
		" where t.id = $tid";
	} else {
		$sql = "SELECT l.uDefinition, l.uTemplate FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
		" where l.id = $id";
	}
	$result = mysql_query($sql);
	if (!$result) { die('Invalid query: ' . mysql_error()); }
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$u['template'] = $row['uTemplate'];
		$u['definition'] = $row['uDefinition'];
	}
	return $u;
}

mysql_close($link);

?>
