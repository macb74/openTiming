<?php

require('Classes/fpdf/fpdf.php');
include("function.php");
$link = connectDB();
session_start();

$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);


class PDF extends FPDF
{
	function ergebnisGesamt($id) {

		$linesPerPage = 45;
		$header = $this->getHeader($_SESSION['vID'], $id);

		$rd = getRennenData($id);
		$sqlAddOn = "";
		if ($rd['rundenrennen'] == 1) { $sqlAddOn = "runden desc, "; }

		$this->setHeader($header);
		
		$sql = "SELECT t.*, l.titel FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
		"where t.vID = ".$_SESSION['vID']." ".
			"and lid = $id and del= 0 and disq = 0 and platz > 0 ".
		"order by $sqlAddOn zeit, platz";

		$result = dbRequest($sql, 'SELECT');

		$this->setMyFont();

		$this->setErgebninsHeader($rd['rundenrennen'], $rd['teamrennen'], false);

		$fill=false;
		$i = 1;
		$ii = 1;
		if($result[1] > 0) {
			foreach ($result[0] as $row) {

				if($rd['teamrennen'] == 0) {
					$this->Cell(10,5,$i,0,0,'R',$fill);
					$this->Cell(11,5,$row['stnr'],0,0,'R',$fill);
					$this->Cell(50,5," ".htmlspecialchars_decode(utf8_decode($row['nachname']), ENT_QUOTES).", ".htmlspecialchars_decode(utf8_decode($row['vorname']), ENT_QUOTES),0,0,'L',$fill);
					$this->Cell(60,5,htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES),0,0,'L',$fill);
					$this->Cell(12,5,htmlspecialchars_decode(utf8_decode($row['klasse']), ENT_QUOTES),0,0,'R',$fill);
					if( $rd['rundenrennen'] == 1 ) { $this->Cell(15,5,$row['runden'],0,0,'R',$fill); }
					$this->Cell(18,5,$row['zeit'],0,0,'R',$fill);
					$this->Cell(10,5,$row['akplatz'],0,0,'R',$fill);
					if( $rd['rundenrennen'] == 0 ) { $this->Cell(12,5,$row['platz'],0,0,'R',$fill); }
					$this->Cell(8,5,$row['att'],0,0,'R',$fill);
		
					$this->Ln();
					$fill=!$fill;
					$i++;
		
					if($i%$linesPerPage == 0) {
						$this->AddPage('Portrait', 'A4');
						$this->setHeader($header);
						$this->setMyFont();
						$this->setErgebninsHeader($rd['rundenrennen'], $rd['teamrennen'], false);
					}
					
				} else {
					
					$this->Cell(10,5,$i,0,0,'R',$fill);
					$this->Cell(11,5,$row['stnr'],0,0,'R',$fill);
					$this->SetFont('Arial','B',10);
					$this->Cell(100,5,htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES),0,0,'L',$fill);
					$this->SetFont('Arial','',10);
					$this->Cell(12,5,htmlspecialchars_decode(utf8_decode($row['klasse']), ENT_QUOTES),0,0,'R',$fill);
					if( $rd['rundenrennen'] == 1 ) { $this->Cell(15,5,$row['runden'],0,0,'R',$fill); }
					$this->Cell(18,5,$row['zeit'],0,0,'R',$fill);
					$this->Cell(10,5,$row['akplatz'],0,0,'R',$fill);
					if( $rd['rundenrennen'] == 0 ) { $this->Cell(12,5,$row['platz'],0,0,'R',$fill); }
					
					$this->Ln();
					$this->Cell(10,5,'',0,0,'R',$fill);
					$this->Cell(11,5,'',0,0,'R',$fill);
					$this->Cell(100,5," ".htmlspecialchars_decode(utf8_decode($row['nachname']), ENT_QUOTES).", ".htmlspecialchars_decode(utf8_decode($row['vorname']), ENT_QUOTES),0,0,'L',$fill);
					$this->Cell(12,5,'',0,0,'R',$fill);
					if( $rd['rundenrennen'] == 1 ) { $this->Cell(15,5,'',0,0,'R',$fill); }
					$this->Cell(18,5,'',0,0,'R',$fill);
					$this->Cell(10,5,'',0,0,'R',$fill);
					if( $rd['rundenrennen'] == 0 ) { $this->Cell(12,5,'',0,0,'R',$fill); }
					
					
					$this->Ln();
					$fill=!$fill;
					$i++;
					$ii=$ii+2;
		
					if($ii%$linesPerPage == 0 || $ii%$linesPerPage == 1) {
						$this->AddPage('Portrait', 'A4');
						$this->setHeader($header);
						$this->setMyFont();
						$this->setErgebninsHeader($rd['rundenrennen'], $rd['teamrennen']);
					}				
									
				}
			}
		}

	}

	function startliste($id, $sort) {

		$linesPerPage = 45;
		$header = $this->getHeader($_SESSION['vID'], $id);
		$rd = getRennenData($id);
		
		$this->setHeader($header);

		if($rd['teamrennen'] == 1 && $sort == 'nachname') { $sort = 'verein'; }
		$sql = "SELECT t.*, l.titel FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
			"where t.vID = ".$_SESSION['vID']." ".
				"and lid = $id and del= 0 and disq = 0 ".
			"order by $sort";

		$result = dbRequest($sql, 'SELECT');

		$this->setMyFont();
		$this->setStartHeader($rd['teamrennen']);

		$fill=false;
		$i = 1;
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
	
				if($rd['teamrennen'] == 0) {
					$this->Cell(15,5,$row['stnr'],0,0,'R',$fill);
					$this->Cell(50,5," ".htmlspecialchars_decode(utf8_decode($row['nachname']), ENT_QUOTES).", ".htmlspecialchars_decode(utf8_decode($row['vorname']), ENT_QUOTES),0,0,'L',$fill);
					$this->Cell(70,5,htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES),0,0,'L',$fill);
					$this->Cell(15,5,htmlspecialchars_decode(utf8_decode($row['klasse']), ENT_QUOTES),0,0,'R',$fill);
					$this->Cell(10,5,htmlspecialchars_decode(utf8_decode($row['att']), ENT_QUOTES),0,0,'L',$fill);
					$this->Cell(20,5,htmlspecialchars_decode(utf8_decode($row['titel']), ENT_QUOTES),0,0,'L',$fill);
		
					$this->Ln();
					$fill=!$fill;
					$i++;
		
					if($i%$linesPerPage == 0) {
						$this->AddPage('Portrait', 'A4');
						$this->setHeader($header);
						$this->setMyFont();
						$this->setStartHeader($rd['teamrennen']);
					}
	
				} else {
	
					$this->Cell(15,5,$row['stnr'],0,0,'R',$fill);
					$this->SetFont('Arial','B',10);
					$this->Cell(100,5,htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES),0,0,'L',$fill);
					$this->SetFont('Arial','',10);
					$this->Cell(15,5,htmlspecialchars_decode(utf8_decode($row['klasse']), ENT_QUOTES),0,0,'R',$fill);
					$this->Cell(20,5,htmlspecialchars_decode(utf8_decode($row['titel']), ENT_QUOTES),0,0,'L',$fill);
		
					$this->Ln();
					$this->Cell(15,5,'',0,0,'R',$fill);
					$this->Cell(100,5," ".htmlspecialchars_decode(utf8_decode($row['nachname']), ENT_QUOTES).", ".htmlspecialchars_decode(utf8_decode($row['vorname']), ENT_QUOTES),0,0,'L',$fill);
					$this->Cell(15,5,'',0,0,'R',$fill);
					$this->Cell(20,5,'',0,0,'L',$fill);
					
					$this->Ln();				
					$fill=!$fill;
					$i=$i+2;
		
					if($i%$linesPerPage == 0 || $i%$linesPerPage == 1) {
						$this->AddPage('Portrait', 'A4');
						$this->setHeader($header);
						$this->setMyFont();
						$this->setStartHeader($rd['teamrennen']);
					}
				}	
			}
		}

	}

	function ergebninsMannschaft($id) {

		$linesPerPage = 45;
		$header = $this->getHeader($_SESSION['vID'], $id);
		$rd = getRennenData($id);
		
		$this->setHeader($header);

		$sql = "SELECT t.verein, t.vnummer, t.vtime, t.vplatz, t.vklasse FROM `teilnehmer` as t ".
		"where t.vID = ".$_SESSION['vID']." ".
			"and t.lid = $id and del= 0 and disq = 0 and vplatz > 0 ".
			"group by vnummer order by vtime asc, vnummer";
			
		$result = dbRequest($sql, 'SELECT');

		$this->setMyFont();

		$this->setErgebninsMannschaftHeader();

		$fill=false;
		$i = 1;
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				
				$vnummer = $row['vnummer'];
				$this->Cell(15,5,$i,0,0,'R',$fill);
				$this->Cell(60,5,htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES),0,0,'L',$fill);
				$this->Cell(20,5,$row['vtime'],0,0,'R',$fill);
				$this->Cell(5,5,'',0,0,'R',$fill);
				
				$sql2 = "SELECT nachname, vorname, zeit from teilnehmer " .
						"where lid = $id and del= 0 and disq = 0 and vnummer = '$vnummer' order by zeit";
				$res2 = dbRequest($sql2, 'SELECT');
					
				$ii = 1;
				if($result[1] > 0) {
					foreach ($res2[0] as $row2) {
						if($ii != 1) {
							$this->Cell(15,5,'',0,0,'R',$fill);
							$this->Cell(60,5,'',0,0,'L',$fill);
							$this->Cell(20,5,'',0,0,'R',$fill);
							$this->Cell(5,5,'',0,0,'R',$fill);
						}
						$this->Cell(40,5," ".htmlspecialchars_decode(utf8_decode($row2['nachname']), ENT_QUOTES).",\n ".htmlspecialchars_decode(utf8_decode($row2['vorname']), ENT_QUOTES),0,0,'L',$fill);
						$this->Cell(20,5,$row2['zeit'],0,0,'R',$fill);
						if($ii == 1) {
							$this->Cell(5,5,'',0,0,'R',$fill);
							$this->Cell(10,5,$row['vklasse'],0,0,'R',$fill);
							$this->Cell(10,5,$row['vplatz'],0,0,'R',$fill);
						} else {
							$this->Cell(5,5,'',0,0,'R',$fill);
							$this->Cell(10,5,'',0,0,'R',$fill);
							$this->Cell(10,5,'',0,0,'R',$fill);				
						}
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

	function ergebnisKlasse($id) {

		$linesPerPage = 45;
		$header = $this->getHeader($_SESSION['vID'], $id);
		
		$rd = getRennenData($id);
		$sqlAddOn = "";
		if ($rd['rundenrennen'] == 1) { $sqlAddOn = "runden desc, "; }

		// Alle vorhandenen Klassen ermittlen
		// Da ein GROUP BY keine Umlaute beachtet, wird nach den MD5 Summen der Klassennamen gruppiert

		$sql = "SELECT MD5(klasse) as k, klasse from teilnehmer as t where t.vID = ".$_SESSION['vID']." and t.lid = $id and platz > 0 group by k order by klasse";
		$rKlassen = dbRequest($sql, 'SELECT');
		
		$i = 0;
		$kl = array();
		
		if($rKlassen[1] > 0) {
			foreach ($rKlassen[0] as $row) {
				$kl[$i] = $row['klasse'];
				$i++;
			}
		}

		$fill=false;
		$i = 0;
		$ii = 1;

		// Ergebniss fuer alle vorhandene Klassen ausgeben
		foreach ($kl as $k) {

			if ( $i != 0 ) { $this->AddPage('Portrait', 'A4'); }
			$this->setHeader($header);
			$this->setMyFont();
			$this->setErgebninsHeader($rd['rundenrennen'], $rd['teamrennen'], true);
			
			$sql = "SELECT t.*, l.titel FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
				"where t.vID = ".$_SESSION['vID']." ".
				"and lid = $id and del= 0 and disq = 0 and platz > 0 and MD5(t.klasse) = '".md5($k)."' ".
				"order by $sqlAddOn zeit, platz";

			$result = dbRequest($sql, 'SELECT');
			
			$i = 1;
			if($result[1] > 0) {
				foreach ($result[0] as $row) {
			
					if($rd['teamrennen'] == 0) {
						$this->Cell(10,5,$row['akplatz'],0,0,'R',$fill);
						$this->Cell(11,5,$row['stnr'],0,0,'R',$fill);
						$this->Cell(50,5," ".htmlspecialchars_decode(utf8_decode($row['nachname']), ENT_QUOTES).", ".htmlspecialchars_decode(utf8_decode($row['vorname']), ENT_QUOTES),0,0,'L',$fill);
						$this->Cell(60,5,htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES),0,0,'L',$fill);
						$this->Cell(12,5,htmlspecialchars_decode(utf8_decode($row['klasse']), ENT_QUOTES),0,0,'R',$fill);
						if( $rd['rundenrennen'] == 1 ) { $this->Cell(15,5,$row['runden'],0,0,'R',$fill); }
						$this->Cell(18,5,$row['zeit'],0,0,'R',$fill);
						$this->Cell(8,5,$row['att'],0,0,'R',$fill);
	
						$this->Ln();
						$fill=!$fill;
						$i++;
			
						if($i%$linesPerPage == 0) {
							$this->AddPage('Portrait', 'A4');
							$this->setHeader($header);
							$this->setMyFont();
							$this->setErgebninsHeader($rd['rundenrennen'], $rd['teamrennen'], true);
						}
						
					} else {
						
						$this->Cell(10,5,$i,0,0,'R',$fill);
						$this->Cell(11,5,$row['stnr'],0,0,'R',$fill);
						$this->SetFont('Arial','B',10);
						$this->Cell(100,5,htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES),0,0,'L',$fill);
						$this->SetFont('Arial','',10);
						$this->Cell(12,5,htmlspecialchars_decode(utf8_decode($row['klasse']), ENT_QUOTES),0,0,'R',$fill);
						if( $rd['rundenrennen'] == 1 ) { $this->Cell(15,5,$row['runden'],0,0,'R',$fill); }
						$this->Cell(18,5,$row['zeit'],0,0,'R',$fill);
						//if( $rd['rundenrennen'] == 0 ) { $this->Cell(12,5,$row['platz'],0,0,'R',$fill); }
						
						$this->Ln();
						$this->Cell(10,5,'',0,0,'R',$fill);
						$this->Cell(11,5,'',0,0,'R',$fill);
						$this->Cell(100,5," ".htmlspecialchars_decode(utf8_decode($row['nachname']), ENT_QUOTES).", ".htmlspecialchars_decode(utf8_decode($row['vorname']), ENT_QUOTES),0,0,'L',$fill);
						$this->Cell(12,5,'',0,0,'R',$fill);
						if( $rd['rundenrennen'] == 1 ) { $this->Cell(15,5,'',0,0,'R',$fill); }
						$this->Cell(18,5,'',0,0,'R',$fill);
						//if( $rd['rundenrennen'] == 0 ) { $this->Cell(12,5,'',0,0,'R',$fill); }
						
						
						$this->Ln();
						$fill=!$fill;
						$i++;
						$ii=$ii+2;
			
						if($ii%$linesPerPage == 0 || $ii%$linesPerPage == 1) {
							$this->AddPage('Portrait', 'A4');
							$this->setHeader($header);
							$this->setMyFont();
							$this->setErgebninsHeader($rd['rundenrennen'], $rd['teamrennen'], true);
						}				
					}				
				}
			}				
						
		}

	}


	function setStartHeader($team) {
		$this->SetFillColor(200,200,200);
		$this->Cell(15,5,"Stnr.",'B',0,'R',1);
		if($team == 0) { $this->Cell(50,5," Name",'B',0,'L',1); }
		if($team == 1) { $this->Cell(100,5," Name",'B',0,'L',1); }
		if($team == 0) { $this->Cell(70,5,"Verein",'B',0,'L',1); }
		$this->Cell(15,5,"Klasse",'B',0,'R',1);
		if($team == 0) { $this->Cell(10,5,"Att",'B',0,'L',1); }
		$this->Cell(20,5,"Lauf",'B',0,'L',1);
		$this->Ln();
		$this->SetFillColor(224,235,255);
	}

	function setErgebninsHeader($rundenrennen, $team, $klasse) {
		$this->SetFillColor(200,200,200);
		$this->Cell(10,5,"Platz",'B',0,'R',1);
		$this->Cell(11,5,"Stnr.",'B',0,'R',1);
		if( $team == 0) { $this->Cell(50,5," Name",'B',0,'L',1); }
		if( $team == 1) { $this->Cell(100,5," Name",'B',0,'L',1); }
		if( $team == 0) { $this->Cell(60,5,"Verein",'B',0,'L',1); }
		$this->Cell(12,5,"Klasse",'B',0,'R',1);
		if( $rundenrennen == 1) { $this->Cell(15,5,"Runden",'B',0,'R',1); }
		$this->Cell(18,5,"Zeit",'B',0,'R',1);
		if( !$klasse) { $this->Cell(10,5,"AK-P",'B',0,'R',1); }
		if( $rundenrennen == 0 && !$klasse) { $this->Cell(12,5,"M/W-P",'B',0,'R',1); }
		if( $team == 0) { $this->Cell(8,5,"Att",'B',0,'R',1); }
		$this->Ln();
		$this->SetFillColor(224,235,255);
	}

	function setErgebninsMannschaftHeader() {
		$this->SetFillColor(200,200,200);
		$this->Cell(15,5,"Platz",'B',0,'R',1);
		$this->Cell(60,5,"Verein",'B',0,'L',1);
		$this->Cell(20,5,"Zeit",'B',0,'R',1);
		$this->Cell(5,5,"",'B',0,'R',1);
		$this->Cell(40,5," Name",'B',0,'L',1);
		$this->Cell(20,5,"Zeit",'B',0,'R',1);
		$this->Cell(5,5,"",'B',0,'R',1);
		$this->Cell(10,5,"Klasse",'B',0,'R',1);
		$this->Cell(10,5,"AK",'B',0,'R',1);
		$this->Ln();
		$this->SetFillColor(224,235,255);
	}

	function setMyFont() {
		$this->SetTextColor(0);
		$this->SetFont('Arial','',10);
		//$this->SetDrawColor(0,0,0);
		//$this->SetLineWidth(.5);
	}

	function getHeader($veranstaltung, $rennen) {
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

		$header['lauf'] 		= htmlspecialchars_decode($rData['titel'], ENT_QUOTES);
		$header['lauf2'] 		= htmlspecialchars_decode($rData['untertitel'], ENT_QUOTES);

		return $header;
	}

	function setHeader($header) {
		
		$rd = getRennenData($_GET['id']);
		if($rd['showLogo'] == 1 ) {
			$this->Image('img/logoPDF.png',8,8,40,20,'');
		}
		
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

$rData = getRennenData($_GET['id']);
$filename = $rData['titel']."_".$rData['untertitel'].".pdf";

if($_GET['action'] == "ergebnisGesamt") {
	$pdf->ergebnisGesamt($_GET['id']);
	$filename = "Ergebnis_Gesamt_".$filename;	
} elseif($_GET['action'] == "ergebnisKlasse") {
	$pdf->ergebnisKlasse($_GET['id']);
	$filename = "Ergebnis_Klassen_".$filename;		
} elseif($_GET['action'] == "startliste") {
	$pdf->startliste($_GET['id'], $_GET['sort']);
	$filename = "Startliste_".$filename;	
} elseif($_GET['action'] == "ergebninsMannschaft") {
	$pdf->ergebninsMannschaft($_GET['id']);
	$filename = "Ergebnis_Mannschaft".$filename;	
}
$link->close();
$pdf->Output($filename, "I");

?>
