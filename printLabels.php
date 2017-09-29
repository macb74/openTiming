<?php

require('Classes/fpdf/fpdf.php');
include("function.php");
$link = connectDB();
session_start();

$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);


class PDF extends FPDF
{
    var $startX = 5;
    var $startY = 20;
    
	function printLabels() {
		
	    $labelsPerPage = 24;
	    $labelsPerLine = 3;
	    $line = 0;
	    $label = 1;
	    $leftMargin = 0;
	    $lineHeight = 34;
	    $columnWidth = 70;
	    
		$sql = "SELECT * FROM `teilnehmer` ".
		          "where vID = ".$_SESSION['vID']." ".
		          "order by stnr";

		$result = dbRequest($sql, 'SELECT');

		$this->setMyFont();
		
		if($result[1] > 0) {
			foreach ($result[0] as $row) {

			    if($label%$labelsPerPage == 1) {
			        $this->nextPage();
			        $line = 0;
			    }
			    
			    $this->SetLeftMargin($this->startX + $leftMargin);
			    $this->SetX($this->startX + $leftMargin);
			    $this->SetY($this->startY + $line);
			    
			    
			    $this->SetFont('Arial','B',12);
			    $text = $row['stnr'];
			    $this->Cell(0,0,$text,0,0,'L',false);
			    $this->Ln(5);
			    
			    $this->SetFont('Arial','B',10);
			    $text = htmlspecialchars_decode(utf8_decode($row['nachname'].", ".$row['vorname']), ENT_QUOTES);
			    $this->Cell(0,0,$text,0,0,'L',false);
			    $this->Ln(4.5);
			    
			    $this->SetFont('Arial','',10);
			    $text = htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES);
			    $this->Cell(0,0,$text,0,0,'L',false);
			    $this->Ln(4.5);
			    
			    $text = htmlspecialchars_decode(utf8_decode("Jg: ".$row['jahrgang']." ".$row['klasse']), ENT_QUOTES);
			    $this->Cell(0,0,$text,0,0,'L',false);
			    //$this->Ln(3);
			    
			    
			    $leftMargin = $leftMargin + $columnWidth;
			    if($label%$labelsPerLine == 0) {
			        $leftMargin = 0;
			        $line = $line + $lineHeight;
			    }
			    
				$label++;

			}
		}

	}


	function nextPage() {
	    $this->AddPage('Portrait', 'A4');
	    $this->setMyFont();
	    $this->SetY($this->startY);
	    $this->SetX($this->startX);
	}
	
	function setMyFont() {
		$this->SetTextColor(0);
		$this->SetFont('Arial','',10);
		//$this->SetDrawColor(0,0,0);
		//$this->SetLineWidth(.5);
	}


}

$pdf=new PDF();
$pdf->AliasNbPages();
//$pdf->AddPage('Portrait', 'A4');

$filename = "labels.pdf";
$pdf->printLabels();

$link->close();
$pdf->Output($filename, "I");

?>
