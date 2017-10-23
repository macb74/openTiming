<?php
require_once('../Classes/fpdf/fpdf.php');
require_once('../Classes/fpdi/fpdi.php');
include "../config.php";
include("../function.php");
$link = connectDB();
session_start();

$stnr=0;

$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);

class PDF extends FPDI
{
	function urkunde() {
	    
	    //$template = "../templates/HML_Urkunde.pdf";
	    $template = "";
		
	    $sql = "SELECT t.*, sr.zeit as tzeit from specialReporting sr " .
		  		"LEFT JOIN teilnehmer t on sr.uid = t.att " .
		  		"where t.vID = ".$_SESSION['vID']." and sr.vid = ".$_SESSION['vID']." order by sr.zeit, t.zeit asc";
		$result = dbRequest($sql, 'SELECT');
		
		$this->setMyFont();

		$i = 0;
		$ii = 0;
		$oldVnummer = "";
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				if ($row['att'] != $oldVnummer) {
				    $i++;
					$ii = 1;
					$teamRow[$i]['vplatz'] = $i;
					$teamRow[$i]['vtime']  = $row['tzeit'];
					$teamRow[$i]['Name'][$ii] = $row['nachname'].", ".$row['vorname'];
					$ii++;
				} else {
					// Verein vom langsamsten Läufer, damit kein kollition mit 10 km Lauf
					$teamRow[$i]['verein'] = $row['verein'];
					$teamRow[$i]['Name'][$ii] = $row['nachname'].", ".$row['vorname'];
					$ii++;
				}
				$oldVnummer = $row['att'];
			}
		} else {
		    echo "<h1>no Team available</h1>";
		    die;
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

			$this->SetY(177);
			$this->SetFont('Verdana','B',16);
			//Calculate width of title and position
			$titel = utf8_decode("12. Holzkirchner Marktlauf");
			$w=$this->GetStringWidth($titel);
			$this->SetX((210-$w)/2); // Mitte
			$this->SetTextColor(0);
			$this->Cell($w,20,$titel,0,0,'C',false);
			$this->Ln(6);
			
			$this->SetFont('Verdana','',12);
			//Calculate width of title and position
			$datum = utf8_decode("am 22. Oktober 2017");
			$w=$this->GetStringWidth($datum);
			$this->SetX((210-$w)/2); // Mitte
			$this->SetTextColor(0);
			$this->Cell($w,20,$datum,0,0,'C',false);
			$this->Ln(6);
			
			$this->SetFont('Verdana','',12);
			//Calculate width of title and position
			$datum = htmlspecialchars_decode(utf8_decode("Marathon Teamwertung"), ENT_QUOTES);
			$w=$this->GetStringWidth($datum);
			$this->SetX((210-$w)/2); // Mitte
			$this->SetTextColor(0);
			$this->Cell($w,20,$datum,0,0,'C',false);
			$this->Ln(17);
			
			$this->SetFont('Verdana','B',22);
			$verein = htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES);
			$w=$this->GetStringWidth($verein);
			$this->SetX((210-$w)/2); // Mitte
			//$this->SetX(200-$w);
			$this->SetTextColor(0);
			$this->Cell($w,15,$verein,0,0,'C',false);
			$this->Ln(8);
			
			$i = 1;
			$len = count($row['Name']);
			while ($i <= $len) {
			    $this->SetFont('Verdana','',12);
			    //Calculate width of title and position
			    $name = htmlspecialchars_decode(utf8_decode($row['Name'][$i]), ENT_QUOTES);
			    $w=$this->GetStringWidth($name);
			    $this->SetX((210-$w)/2); // Mitte
			    //$this->SetX(200 - $w); // Rechts
			    $this->SetTextColor(0);
			    $this->Cell($w,20,$name,0,0,'C',false);
			    $this->Ln(5);
			    $i++;
			}
			$this->Ln(12);
			
			$this->SetFont('Verdana','B',20);
			$k = $row['vplatz'].". Platz";
			$w=$this->GetStringWidth($k);
			$this->SetX((210-$w)/2);
			$this->SetTextColor(0);
			$this->Cell($w,10,$k,0,0,'C',false);
			$this->Ln(8);
			
			$this->SetFont('Verdana','B',16);
			$zeit = "Zeit: ".$row['vtime'];
			$w=$this->GetStringWidth($zeit);
			$this->SetX((210-$w)/2);
			$this->SetTextColor(0);
			$this->Cell($w,10,$zeit,0,0,'C',false);
			$this->Ln(0);

			$i++;

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

// 	function getHeader($veranstaltung, $id, $action) {
// 		$sql = "select titel, untertitel, datum from veranstaltung where id = $veranstaltung";
// 		$result = dbRequest($sql, 'SELECT');
		
// 		if($result[1] > 0) {
// 			foreach ($result[0] as $row) {
// 				$header['titel'] 		= $row['titel'];
// 				$header['untertitel'] 	= $row['untertitel'];
// 				$header['datum'] 		= $row['datum'];
// 			}
// 		}

// 		$header['lauf'] 		= "Marathon Staffel";
// 		$header['lauf2'] 		= "42,2km";

// 		return $header;
// 	}


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

$pdf->urkunde();
$filename = "Urkunde_MarathonStaffel.pdf";

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
