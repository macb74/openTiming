<?php
session_start();

require_once 'PHPExcel/Classes/PHPExcel.php';
#require_once 'Spreadsheet/Excel/Writer.php';
include("function.php");

$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);

$link = connectDB();
$filename = $_GET['action'].'.xlsx';

if($_GET['action'] == "startliste") {
	exportStartliste($filename);
} else {
	exportErgebnins($_GET['id'], $filename);
}
mysql_close($link);


function exportStartliste($filename) {
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'Stnr')
				->setCellValue('B1', 'Nachname')
				->setCellValue('C1', 'Vorname')
				->setCellValue('D1', 'Verein')
				->setCellValue('E1', 'Jahrgang')
				->setCellValue('F1', 'Geschlecht')
				->setCellValue('G1', 'Klasse')
				->setCellValue('H1', 'Att')
				->setCellValue('I1', 'Rennen');

	$sql = "SELECT t.*, l.titel, l.untertitel FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
			"where t.vID = ".$_SESSION['vID']." ".
			"and del= 0 and disq = 0 ".
			"order by stnr";
	$result = mysql_query($sql);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	$i = 2;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	
		// The actual data
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$i, htmlspecialchars_decode($row['stnr'], ENT_QUOTES))
					->setCellValue('B'.$i, htmlspecialchars_decode($row['nachname'], ENT_QUOTES))
					->setCellValue('C'.$i, htmlspecialchars_decode($row['vorname'], ENT_QUOTES))
					->setCellValue('D'.$i, htmlspecialchars_decode($row['verein'], ENT_QUOTES))
					->setCellValue('E'.$i, htmlspecialchars_decode($row['jahrgang'], ENT_QUOTES))
					->setCellValue('F'.$i, htmlspecialchars_decode($row['geschlecht'], ENT_QUOTES))
					->setCellValue('G'.$i, htmlspecialchars_decode($row['klasse'], ENT_QUOTES))
					->setCellValue('H'.$i, htmlspecialchars_decode($row['att'], ENT_QUOTES))
					->setCellValue('I'.$i, htmlspecialchars_decode($row['titel'].' - '.$row['untertitel'], ENT_QUOTES));
		$i++;
	}
	
	$objPHPExcel->getActiveSheet()->setTitle('Startliste');
	$objPHPExcel->setActiveSheetIndex(0);
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Content-Type: content=text/html;charset=utf-8');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	
}

function exportErgebnins($id, $filename) {
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', 'Stnr')
	->setCellValue('B1', 'Nachname')
	->setCellValue('C1', 'Vorname')
	->setCellValue('D1', 'Verein')
	->setCellValue('E1', 'Jahrgang')
	->setCellValue('F1', 'Geschlecht')
	->setCellValue('G1', 'Klasse')
	->setCellValue('H1', 'Zeit')
	->setCellValue('I1', 'Platz')
	->setCellValue('J1', 'AK Platz')
	->setCellValue('K1', 'Runden')
	->setCellValue('L1', 'Att')
	->setCellValue('M1', 'Rennen');
	

	$sql = "SELECT t.*, l.titel, l.untertitel FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
		"where t.vID = ".$_SESSION['vID']." ".
			"and lid = $id and del= 0 and disq = 0 and platz > 0 ".
		"order by runden desc, zeit asc";

	$result = mysql_query($sql);
	if (!$result) { die('Invalid query: ' . mysql_error()); }

	$i = 2;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

		// The actual data
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$i, htmlspecialchars_decode($row['stnr'], ENT_QUOTES))
					->setCellValue('B'.$i, htmlspecialchars_decode($row['nachname'], ENT_QUOTES))
					->setCellValue('C'.$i, htmlspecialchars_decode($row['vorname'], ENT_QUOTES))
					->setCellValue('D'.$i, htmlspecialchars_decode($row['verein'], ENT_QUOTES))
					->setCellValue('E'.$i, htmlspecialchars_decode($row['jahrgang'], ENT_QUOTES))
					->setCellValue('F'.$i, htmlspecialchars_decode($row['geschlecht'], ENT_QUOTES))
					->setCellValue('G'.$i, htmlspecialchars_decode($row['klasse'], ENT_QUOTES))
					->setCellValue('H'.$i, htmlspecialchars_decode($row['zeit'], ENT_QUOTES))
					->setCellValue('I'.$i, htmlspecialchars_decode($row['platz'], ENT_QUOTES))
					->setCellValue('J'.$i, htmlspecialchars_decode($row['akplatz'], ENT_QUOTES))
					->setCellValue('K'.$i, htmlspecialchars_decode($row['runden'], ENT_QUOTES))
					->setCellValue('L'.$i, htmlspecialchars_decode($row['att'], ENT_QUOTES))
					->setCellValue('M'.$i, htmlspecialchars_decode($row['titel'].' - '.$row['untertitel'], ENT_QUOTES));
		$i++;
	}

	$objPHPExcel->getActiveSheet()->setTitle('Ergebnisliste');
	$objPHPExcel->setActiveSheetIndex(0);
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Content-Type: content=text/html;charset=utf-8');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');

}

?>
