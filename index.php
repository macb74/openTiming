<?php
/*
 * Created on 06.11.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

session_start();
include "function.php";
//$link = connectDB();
$allowedFunctions = array('veranstaltungen', 'teilnehmer', 'auswertung', 'ergebnis', 'klasse', 'rennen', 'startliste', 'urkunden', 'import');

$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);
$html = "";

if(isset($_GET['jqRequest'])) {
	if($_GET['func'] == 'showStartList')      { $html = showStartResult($_GET['lid']); echo $html;}
	if($_GET['func'] == 'showStartWithoutKl') { $html = showStartWithoutKl($_GET['lid']); echo $html;}
	if($_GET['func'] == 'showResult') { $html = showResult($_GET['lid']); echo $html;}
	if($_GET['func'] == 'showResultM') { $html = showResultM($_GET['lid']); echo $html;}
	if($_GET['func'] == 'showWithowtTime') { $html = showWithowtTime($_GET['lid']); echo $html;}
	if($_GET['func'] == 'getKlasse') { $html = getKlasse($_GET['jg'], $_GET['sex'], $_GET['lid'], 1); echo $html;}
	if($_GET['func'] == 'lockRace') { $html = lockRace($_GET['lid'], $_GET['lock']); echo $html;}
	exit;
}

# Wenn keine Funktion übergeben wurde, dann wird die Veranstaltungsauswahl angezeigt
if (!isset($_GET['func'])) {
	$func[0] = 'veranstaltungen';
} else {
	$func[0] = "";
	$func[1] = "";
	$func = explode(".", $_GET['func']);
}	

#Prüfung ob eine erlaubte function übergeben wird.
if(array_search($func[0], $allowedFunctions) !== false) {
	if (!isset($func[1])) {
		$func[1] = "";
	}
	if (isset($_SESSION['vID']) || $func[0] == 'veranstaltungen') {
		$html = $func[0]();
	}
} else {
	echo "Use of disallowed function"; die;
}
	


if (!isset($_SESSION['vTitel'])) { $_SESSION['vTitel'] = ''; }
if (!isset($_SESSION['vDatum'])) { $_SESSION['vDatum'] = ''; }
if (!isset($_SESSION['vUntertitel'])) { $_SESSION['vUntertitel'] = ''; }
if (!isset($_SESSION['rID'])) { $_SESSION['rID'] = 0; }

$testDiv = false;
if((stristr($_SERVER["SCRIPT_NAME"], 'test') !== FALSE) || (stristr($config['dbname'], 'test'))){
	$testDiv = true;
}

?>

<html>
<head>

	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="cache-control" content="no-cache; no-store; max-age=0" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="description" content="openTiming SportsTiming" />
	<meta http-equiv="Content-Language" content="de" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<meta name="decorator" content="main" />
	
	<title>openTiming</title>
	
	<link href="css/smart.css" rel="stylesheet" type="text/css" />
	<link href="css/smart-tables.css" rel="stylesheet" type="text/css" />
	<link href="css/menu.css" rel="stylesheet" type="text/css" />
	<link href="css/jquery.autocomplete.css" rel="stylesheet" type="text/css" />
	
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="js/openTiming.js"></script>

</head>

<body>

<?php 
if ($testDiv == true) {
	echo "<div id=\"testsystem\">Das ist das Testsystem</div>";
}
?>

<table class="portal" width="100%" height="95%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td height="118">

		<table class="backgrd-top" border="0" cellspacing="0" cellpadding="0" width="100%" height="118">
			<tr>
				<td width="200" height="118">&nbsp;</td>
				<td height="118">
				<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
					<tr>
						<td align="right">

						<table class="box" width="300" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="boxBody" align="right"><!-- username --></td>
							</tr>
						</table>

						</td>
					</tr>
					<tr>
						<td class="portaltitle"><?php echo $_SESSION['vTitel'] ?></td>
					</tr>
					<tr>
						<td class="toptitle"><?php echo $_SESSION['vUntertitel'] ?></td>
					</tr>
					<tr>
						<td class="subTitle" align="center" valign="top"><?php echo $_SESSION['vDatum'] ?>
						</td>
				
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>

	<tr>
		<td valign="top">
		<table width="100%" height="100%" class="backgrd-left">
			<tr>
				<td class="menucolumn" valign="top" height="100%">

				<table class="menu" cellpadding="0" cellspacing="0">
					<tr>
						<th class="menuTitle">Men&uuml;</th>
					</tr>
					<tr>
						<td class="menuBody">
						<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top"><?php menue(); ?></td>

							</tr>

							<tr>
								<td>&nbsp;</td>
							</tr>
						</table>
						</td>
					</tr>
				</table>

				</td>

				<td width="100%" valign="top">
				<table width="100%" border="0" cellspacing="0" cellpadding="3">

					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td class="mainBody" valign="top"><?php
						echo $html;
						?></td>
					</tr>

					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td valign="bottom">
		<div class="copy">&copy; 2012 open Timing by M. Bußmann</div>
		</td>
	</tr>
</table>
<?php if($func[0] == 'teilnehmer' && ($func[1] == 'edit' || $func[1] == 'insert')) {?>
<script type="text/javascript" src="js/teilnehmer.js"></script>
<?php }?>
</body>
</html>

<?php

if (isset($link)) {
	mysql_close($link);
}
//$link = connectDB();
#phpinfo();
?>