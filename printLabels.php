<?php

require('Classes/fpdf/fpdf.php');
include("function.php");
$link = connectDB();
session_start();

$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);


class PDF extends FPDF
{

    function printLabels($labelsPerPage) {

	    $line = 0;
	    $label = 1;
	    $leftMargin = 0;

		$sql = "SELECT * FROM `teilnehmer` ".
		          "where vID = ".$_SESSION['vID']." ".
		          "order by stnr";

		$result = dbRequest($sql, 'SELECT');

		$this->setDefaultValues();
		$dimensions = $this->getDimensions($labelsPerPage);

		if($result[1] > 0) {
			foreach ($result[0] as $row) {

			    if($label%$labelsPerPage == 1) {
			        $this->nextPage($dimensions['$startX'], $dimensions['$startY']);
			        $line = 0;
			    }

			    $this->SetLeftMargin($dimensions['$startX'] + $leftMargin);
			    $this->SetX($dimensions['$startX'] + $leftMargin);
			    $this->SetY($dimensions['$startY'] + $line);


			    $this->SetFont('Arial','B',$dimensions['nrSize']);
			    $text = $row['stnr'];
			    $this->Cell(0,0,$text,0,0,'L',false);
			    $this->Ln($dimensions['nrSpace']);

			    $this->SetFont('Arial','B',$dimensions['nameSize']);
			    $text = htmlspecialchars_decode(utf8_decode($row['nachname'].", ".$row['vorname']), ENT_QUOTES);
			    $this->Cell(0,0,$text,0,0,'L',false);
			    $this->Ln($dimensions['nameSpace']);

			    $this->SetFont('Arial','',$dimensions['nameSize']);
			    $text = htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES);
			    $this->Cell(0,0,$text,0,0,'L',false);
			    $this->Ln($dimensions['nameSpace']);

			    $text = htmlspecialchars_decode(utf8_decode("Jg: ".$row['jahrgang']." / ".$row['klasse']), ENT_QUOTES);
			    $this->Cell(0,0,$text,0,0,'L',false);
			    //$this->Ln(3);


			    $leftMargin = $leftMargin + $dimensions['columnWidth'];
			    if($label%$dimensions['labelsPerLine'] == 0) {
			        $leftMargin = 0;
			        $line = $line + $dimensions['lineHeight'] ;
			    }

				$label++;

			}
		}

	}

	function getDimensions($labelsPerPage) {
	    if($labelsPerPage == 52) {
	        $d['labelsPerLine'] = 4;
	        $d['lineHeight']    = "20.8";
	        $d['columnWidth']   = 48;
	        $d['nrSize']        = "10";
	        $d['nameSize']      = "8";
	        $d['nrSpace']       = "4";
	        $d['nameSpace']     = "3.5";
	        $d['$startX']       = 15;
	        $d['$startY']       = 17;
	    }

	    if($labelsPerPage == 24) {
	        $d['labelsPerLine'] = 3;
	        $d['lineHeight']    = 34;
	        $d['columnWidth']   = 70;
	        $d['nrSize']        = "12";
	        $d['nameSize']      = "10";
	        $d['nrSpace']       = "5";
	        $d['nameSpace']     = "4.5";
	        $d['$startX']       = 5;
	        $d['$startY']       = 20;
	    }

	    return $d;
	}

	function nextPage($startX, $startY) {
	    $this->AddPage('Portrait', 'A4');
	    $this->SetY($startY);
	    $this->SetX($startX);
	}

	function setDefaultValues() {
		$this->SetTextColor(0);
		$this->SetFont('Arial','',10);
		$this->SetAutoPageBreak(false);
		//$this->SetDrawColor(0,0,0);
		//$this->SetLineWidth(.5);
	}


}

if(!isset($_GET['labels'])) {
    print "Anzahl der Labels übergeben (?labels=24)";
    die;
}

if($_GET['labels'] == 52) {
    $labelsPerPage = 52;
}

if($_GET['labels'] == 24) {
    $labelsPerPage = 24;
}


$pdf=new PDF();
$pdf->AliasNbPages();
//$pdf->AddPage('Portrait', 'A4');

$filename = "labels.pdf";
$pdf->printLabels($labelsPerPage);

$link->close();
$pdf->Output($filename, "I");

?>
