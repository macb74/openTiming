<?php
require_once('Classes/fpdf/fpdf.php');
require_once('Classes/fpdi/fpdi.php');
include("function.php");
$link = connectDB();
session_start();

$stnr=0;

$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);

class PDF extends FPDI
{
	function urkunde($action, $num, $id, $raceData) {

		$oldVnummer = "";

		if($action == 'gesamt') {
			$sql= "SELECT t.* from teilnehmer as t where t.vID = ".$_SESSION['vID']." and lid = $id and platz > 0 and platz <= $num order by geschlecht, platz";
		} elseif ($action == 'klasse') {
			$sql= "SELECT t.* from teilnehmer as t where t.vID = ".$_SESSION['vID']." and lid = $id and akplatz > 0 and akplatz <= $num order by klasse, platz";
		} elseif ($action == 'einzel') {
			$sql= "SELECT t.* from teilnehmer as t where t.vID = ".$_SESSION['vID']." and id = $id";
		} elseif ($action == 'team') {
			$sql= "SELECT t.* from teilnehmer as t where t.vID = ".$_SESSION['vID']." and lid = $id and vnummer <> '' order by vplatz, zeit asc";
		} else {
			echo "keine action gewählt";
			die;
		}

		$result = dbRequest($sql, 'SELECT');
		
		$this->setMyFont();

		$i = 1;
		if ($action != 'team') {
			if($result[1] > 0) {
				foreach ($result[0] as $row) {
									
					$this->AddPage('Portrait', 'A4');

					if($raceData['template'] != '') {
						$this->setSourceFile($raceData['template']);
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
		
					include($raceData['definition']);
		
					$i++;
		
				}
			}
		} else {
			$i = -1;
			if($result[1] > 0) {
				foreach ($result[0] as $row) {
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
			}
			
			foreach ($teamRow as $row) {
				$this->AddPage('Portrait', 'A4');
	
				if($raceData['template'] != '') {
					$this->setSourceFile($raceData['template']);
					// import page 1
					$tplIdx = $this->importPage(1);
					// use the imported page and place it at point 10,10 with a width of 100 mm
					$this->useTemplate($tplIdx, 0, 0, 0);
				}
	
				include($raceData['definition']);
	
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

	function getHeader($veranstaltung, $id, $action) {
		$sql = "select titel, untertitel, datum from veranstaltung where id = $veranstaltung";
		$result = dbRequest($sql, 'SELECT');
		
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				$header['titel'] 		= $row['titel'];
				$header['untertitel'] 	= $row['untertitel'];
				$header['datum'] 		= $row['datum'];
			}
		}

		if($action == "einzel") {
			$sql = "select lauf, lauf2 from lauf where id = $id";			
		} else {
			$sql = "select titel, untertitel from lauf where id = $id";
		}
		
		$result = dbRequest($sql, 'SELECT');		
			if($result[1] > 0) {
				foreach ($result[0] as $row) {
					$header['lauf'] 		= $row['titel'];
					$header['lauf2'] 		= $row['untertitel'];
				}
			}

		return $header;
	}


	//	function Footer() {
	//	    $this->SetY(-15);
	//	    $this->SetFont('Verdana','I',8);
	//	    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	//	}

}

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddFont('verdana','','verdana.php');
$pdf->AddFont('verdana','B','verdanab.php');

if(!isset($_GET['id'])) { $_GET['id'] = 0;}

if($_GET['action'] != 'einzel') {
	$anzahl=$_SESSION['anzUrkunden-'.$_GET['id']];
} else {
	$anzahl=0;
}

if($anzahl == 'ALL') { $anzahl = 10000; }

if(isset($_GET['action']) && ($_GET['id'] != 0)) {
	$raceData = getRaceData($_GET['action'], $_GET['id']);
	$pdf->urkunde($_GET['action'], $anzahl,  $_GET['id'], $raceData);
	$filename = $raceData['LTitel']."_".$raceData['LUntertitel'].".pdf";
}

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

function getRaceData($action, $id) {
	$raceData['template'] = "";
	$raceData['definition'] = "";
	$raceData['LTitel'] = "";
	$raceData['LUntertitel'] = "";
	$raceData['VTitel'] = "";
	$raceData['VUntertitel'] = "";
	
	if($action == "einzel") {
		$sql = "SELECT l.uDefinition uDefinition, l.uTemplate uTemplate, l.titel LTitel, l.untertitel LUntertitel, v.titel VTitel, v.untertitel VUntertitel".
			" FROM `teilnehmer` as t ".
			" INNER JOIN lauf as l ON t.lID = l.ID ".
			" INNER JOIN veranstaltung as v ON t.vID = v.ID ".
			" where t.id = $id";
	} else {
		$sql = "SELECT l.uDefinition uDefinition, l.uTemplate uTemplate, l.titel LTitel, l.untertitel LUntertitel, v.titel VTitel, v.untertitel VUntertitel".
			" FROM lauf as l  ".
			" INNER JOIN veranstaltung as v ON l.vID = v.ID ".
			" where l.id = $id";
	}
	
	$result = dbRequest($sql, 'SELECT');
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			$raceData['template']    = $row['uTemplate'];
			$raceData['definition']  = $row['uDefinition'];
			$raceData['LTitel']      = $row['LTitel'];
			$raceData['LUntertitel'] = $row['LUntertitel'];
			$raceData['VTitel']      = $row['VTitel'];
			$raceData['VUntertitel'] = $row['VUntertitel'];
		}
	}
	return $raceData;
}

$link->close();
?>
