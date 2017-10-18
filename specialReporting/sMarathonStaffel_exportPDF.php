<?php

require('../Classes/fpdf/fpdf.php');
include "../config.php";
include("../function.php");
$link = connectDB();
session_start();

$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);


class PDF extends FPDF
{
	function ergebninsMannschaft() {

		$linesPerPage = 45;
		$header = $this->getHeader($_SESSION['vID']);
		
		$this->setHeader($header);

		$sql = "SELECT sr.uid, sr.zeit from specialReporting sr " .
                "WHERE sr.vid = ".$_SESSION['vID']." " .
                "ORDER BY sr.zeit";
			
		$result = dbRequest($sql, 'SELECT');
		
		$this->setMyFont();

		$this->setErgebninsMannschaftHeader();

		$fill=false;
		$i = 1;
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				
				$att = $row['uid'];
				
				// Verein ermitteln - vom langsamsten Läufer, damit kein kollition mit 10 km Lauf
				$sqlVerein = "SELECT verein from teilnehmer " .
						"WHERE vid = ".$_SESSION['vID']." and att = '".$att."' " .
						"ORDER BY zeit desc LIMIT 0, 1";
				$resVerein = dbRequest($sqlVerein, 'SELECT');

				
				if($resVerein[1] > 0) {
					foreach ($resVerein[0] as $rowVerein) {
						$verein = $rowVerein['verein'];
					}
				}
				

				$sqlTeilnehmer = "SELECT nachname, vorname, zeit from teilnehmer " .
						"where vid = ".$_SESSION['vID']." and del= 0 and disq = 0 and att = '$att' order by zeit";
				$resTeilnehmer = dbRequest($sqlTeilnehmer, 'SELECT');
								
				$ii = 1;
				if($result[1] > 0) {
					foreach ($resTeilnehmer[0] as $rowTeilnehmer) {
						if($ii == 1) {
							$this->Cell(15,5,$i,0,0,'R',$fill);
							$this->Cell(80,5,htmlspecialchars_decode(utf8_decode($verein), ENT_QUOTES),0,0,'L',$fill);
							$this->Cell(20,5,$row['zeit'],0,0,'R',$fill);
							$this->Cell(5,5,'',0,0,'R',$fill);
						} else {
							$this->Cell(15,5,'',0,0,'R',$fill);
							$this->Cell(80,5,'',0,0,'L',$fill);
							$this->Cell(20,5,'',0,0,'R',$fill);
							$this->Cell(5,5,'',0,0,'R',$fill);
						}
						$this->Cell(50,5," ".htmlspecialchars_decode(utf8_decode($rowTeilnehmer['nachname']), ENT_QUOTES).",\n ".htmlspecialchars_decode(utf8_decode($rowTeilnehmer['vorname']), ENT_QUOTES),0,0,'L',$fill);
						$this->Cell(20,5,$rowTeilnehmer['zeit'],0,0,'R',$fill);
// 						if($ii == 1) {
// 							$this->Cell(5,5,'',0,0,'R',$fill);
// 							$this->Cell(10,5,$row['vklasse'],0,0,'R',$fill);
// 							$this->Cell(10,5,$row['vplatz'],0,0,'R',$fill);
// 						} else {
// 							$this->Cell(5,5,'',0,0,'R',$fill);
// 							$this->Cell(10,5,'',0,0,'R',$fill);
// 							$this->Cell(10,5,'',0,0,'R',$fill);				
// 						}
						$this->Ln();
						$ii++;
					}
				}				
				//$this->Ln();
				$fill=!$fill;
				$i++;
	
				if($i%$linesPerPage == 0) {
					$this->AddPage('Portrait', 'A4');
					$this->setHeader($header);
					$this->setMyFont();
					$this->setErgebninsHeader();
				}
			}
		}
	}

	function setErgebninsMannschaftHeader() {
		$this->SetFillColor(200,200,200);
		$this->Cell(15,5,"Platz",'B',0,'R',1);
		$this->Cell(80,5,"Verein",'B',0,'L',1);
		$this->Cell(20,5,"Zeit",'B',0,'R',1);
		$this->Cell(5,5,"",'B',0,'R',1);
		$this->Cell(50,5," Name",'B',0,'L',1);
		$this->Cell(20,5,"Zeit",'B',0,'R',1);
		$this->Ln();
		$this->SetFillColor(224,235,255);
	}

	function setMyFont() {
		$this->SetTextColor(0);
		$this->SetFont('Arial','',10);
		//$this->SetDrawColor(0,0,0);
		//$this->SetLineWidth(.5);
	}

	function getHeader($veranstaltung) {
		global $rData;
		$sql = "select titel, untertitel, datum from veranstaltung where id = $veranstaltung";
		$result = dbRequest($sql, 'SELECT');

		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				$header['titel'] 		= htmlspecialchars_decode($row['titel'], ENT_QUOTES);
				$header['untertitel'] 	= htmlspecialchars_decode($row['untertitel'], ENT_QUOTES);
				$header['datum'] 		= htmlspecialchars_decode($row['datum'], ENT_QUOTES);
			}
		}

		$header['lauf'] 		= "Marathon Staffel";
		$header['lauf2'] 		= "42,2km";

		return $header;
	}

	function setHeader($header) {
		
		$this->Image('../img/logoPDF.png',8,8,40,20,'');
		
		$this->SetTextColor(0);
		$this->SetY(1);
		$this->SetFont('Arial','',10);
		$this->Ln(5);
		$this->Cell(160);
		$d = explode("-",$header['datum']);
		$this->Cell(30,10,$d[2].".".$d[1].".".$d[0],0,0,'R');
		$this->Ln(5);

		$this->SetFont('Arial','BI',16);
		$this->Cell(80);
		$this->Cell(30,10,utf8_decode($header['titel']),0,0,'C');
		if($header['untertitel'] != "") {
			$this->SetFont('Arial','BI',12);
			$this->Ln(5);
			$this->Cell(80);
			$this->Cell(30,10,utf8_decode($header['untertitel']),0,0,'C');
		}

		$this->Ln(5);
		$this->SetFont('Arial','BI',10);
		$this->Cell(80);
		$this->Cell(30,10,utf8_decode($header['lauf']),0,0,'C');
		$this->Ln(5);
		$this->Cell(80);
		$this->Cell(30,10,utf8_decode($header['lauf2']),0,0,'C');
		$this->Ln(12);
	}

	function Footer() {
		$this->SetTextColor(50,50,50);
		$this->SetY(-30);
		$this->SetFont('Arial','',8);
		$this->Cell(0,10,'w w w . o p e n - r f i d - t i m i n g . d e',0,0,'C');
		$this->Ln(5);
		$this->SetTextColor(0);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		$this->SetFont('Arial','',8);
		$this->Cell(0,10,'Stand: '.date('d.m.Y H:i'),0,0,'R');
	}

}

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('Portrait', 'A4');

$filename = "MarathonStaffel.pdf";
$pdf->ergebninsMannschaft();

$link->close();
$pdf->Output($filename, "I");

?>
