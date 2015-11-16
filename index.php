<?php
/*
 * Created on 06.11.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

if (stristr($_SERVER["REQUEST_URI"], '/index.php') === false) {
	header('Location: '.$_SERVER["SCRIPT_NAME"].'?func=veranstaltungen');
}

session_start();
include "function.php";
$link = connectDB();
$allowedFunctions = array('veranstaltungen', 'teilnehmer', 'auswertung', 'ergebnis', 'einlaufListe', 'klasse', 'rennen', 'startliste', 'urkunden', 'import', 'ziel');

$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);
$html = "";

if(isset($_GET['jqRequest'])) {
	if($_GET['func'] == 'showStartList')      { $html = showStartResult($_GET['lid']); echo $html;}
	if($_GET['func'] == 'showStartWithoutKl') { $html = showStartWithoutKl($_GET['lid']); echo $html;}
	if($_GET['func'] == 'showResult')         { $html = showResult($_GET['lid']); echo $html;}
	if($_GET['func'] == 'showResultM')        { $html = showResultM($_GET['lid']); echo $html;}
	if($_GET['func'] == 'showWithowtTime')    { $html = showWithowtTime($_GET['lid']); echo $html;}
	if($_GET['func'] == 'showEinlaufListe')   { $html = showEinlaufListe($_GET['lid'], $_GET['action']); echo $html;}
	if($_GET['func'] == 'saveManZielzeit')    { $html = saveManZielzeit($_GET['id'], $_GET['action'], $_GET['time']); echo $html;}
	if($_GET['func'] == 'getKlasse')          { $html = getKlasse($_GET['jg'], $_GET['sex'], $_GET['lid'], 1); echo $html;}
	if($_GET['func'] == 'lockRace')           { $html = lockRace($_GET['lid'], $_GET['lock']); echo $html;}
	if($_GET['func'] == 'showZielAnalyse')    { $html = showZielAnalyse($_GET['lid'], $_GET['start'], $_GET['duration']); echo $html;}
	if($_GET['func'] == 'saveReaderTime')     { $html = saveReaderTime($_GET['id'], $_GET['action'], $_GET['values']); echo $html;}
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

if (!isset($_SESSION['vTitel']))      { $_SESSION['vTitel'] = ''; }
if (!isset($_SESSION['vDatum']))      { $_SESSION['vDatum'] = ''; }
if (!isset($_SESSION['vUntertitel'])) { $_SESSION['vUntertitel'] = ''; }
if (!isset($_SESSION['rID']))         { $_SESSION['rID'] = 0; }

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
	<link href="js/jquery-ui/jquery-ui.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/timeline.css" rel="stylesheet" type="text/css" />
	
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery-ui/jquery-ui.js"></script>
	<script type="text/javascript" src="js/timeline.js"></script>
	<script type="text/javascript" src="js/openTiming.js"></script>
	<script type="text/javascript" src="js/base64.js"></script>
	<script type="text/javascript" src="js/d3.v3.min.js"></script>
    <script type="text/javascript" src="js/timeline.js"></script>

</head>

<body>

<?php 
if ($testDiv == true) {
	echo "<div id=\"testsystem\">Das ist das Testsystem</div>";
}
?>

<div class="portal">
	<div class="otlogo"></div>
	<div class="header">
		<div class="portaltitle"><?php echo $_SESSION['vTitel'] ?></div>
		<div class="toptitle"><?php echo $_SESSION['vUntertitel'] ?></div>
		<div class="subTitle"><?php echo $_SESSION['vDatum'] ?></div>
	</div>
	
	<div class="main">
		<div class="menu">
			<div class="menuTitle">Men&uuml;</div>
			<div class="menuBody"><?php menue(); ?></div>
		</div>
		<div class="main-right"><?php echo $html; ?></div>
	</div>
</div>
<div class="footer">
		<div class="copy">&copy; 2015 open Timing by M. Bußmann</div>
</div>

<?php if($func[0] == 'teilnehmer' && ($func[1] == 'edit' || $func[1] == 'insert')) {?>
<script type="text/javascript" src="js/teilnehmer.js"></script>
<?php }?>

<?php if($func[0] == 'einlaufListe') { ?>
	<script>
	var pageToLoad = 'index.php?jqRequest&func=showEinlaufListe&lid=0&action=none';
	$("#data_div").load(pageToLoad);
	</script>
<?php }?>

<?php if($func[0] == 'ziel' && $func[1] == 'edit') { ?>
	<script>
	var pageToLoad = 'index.php?jqRequest&func=saveReaderTime&action=show&id&values';
	$("#data_div").load(pageToLoad);
	</script>
<?php }?>

</body>
</html>

<?php
$link->close();
#phpinfo();
?>