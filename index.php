<?php
/*
 * Created on 20.11.2015
 *
 */

if (stristr($_SERVER["REQUEST_URI"], '/index.php') === false) {
	header('Location: '.$_SERVER["SCRIPT_NAME"].'?func=veranstaltung');
}

$showContent = false;
session_start();
include "function.php";
$link = connectDB();
$allowedFunctions = array('veranstaltung', 'teilnehmer', 'auswertung', 'rennen', 'klasse', 'import', 'einlaufListe', 'ziel');

$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);
$html = "";

# Wenn keine Funktion übergeben wurde, dann wird die Veranstaltungsauswahl angezeigt
if (!isset($_GET['func'])) {
	$func[0] = 'veranstaltung';
} else {
	$func[0] = "";
	$func[1] = "";
	$func = explode(".", $_GET['func']);
}	

#Prüfung ob eine erlaubte function übergeben wird.
if(array_search($func[0], $allowedFunctions) !== false) {
	if (isset($_SESSION['vID']) || $func[0] == 'veranstaltung') {
		$showContent = true;
	}
	
	if (!isset($_SESSION['vID'])) {
		$func[0] = 'veranstaltung';
		$showContent = true;
	}
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
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="cache-control" content="no-cache; no-store; max-age=0" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="description" content="openTiming SportsTiming" />
	<meta http-equiv="Content-Language" content="de" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<meta name="decorator" content="main" />
	
	<title>openTiming</title>
	
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/opentiming.css" rel="stylesheet">
	<link href="css/font-awesome.css" rel="stylesheet">
	<link href="css/bootstrap-datepicker3.css" rel="stylesheet">
	<link href="js/jquery-ui/jquery-ui.min.css" rel="stylesheet">
	
	<script src="js/jquery-2.1.4.js"></script>
	<script src="js/jquery-ui/jquery-ui.min.js"></script>
	
	<script>
		/*** Handle jQuery plugin naming conflict between jQuery UI and Bootstrap ***/
		$.widget.bridge('uibutton', $.ui.button);
		$.widget.bridge('uitooltip', $.ui.tooltip);
	</script>

    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/bootstrap-datepicker.de.min.js"></script>
    <script src="js/opentiming.js"></script>
    <script src="js/base64.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>

</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#"><span class="navbar-brand-orange">open</span>Timing</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-left">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-wrench"></i> Administration <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="index.php?func=veranstaltung">Veranstaltung</a></li>
							<li><a href="index.php?func=rennen">Rennen</a></li>
							<li><a href="index.php?func=klasse">Klassen</a></li>					
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> Teilnehmer <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="index.php?func=teilnehmer">Teilnehmerliste</a></li>
							<li><a href="index.php?func=teilnehmer&id=new">Teilnehmer Eingabe</a></li>
							<li><a href="index.php?func=import.teilnehmer">Teilnehmer Laden</a></li>
						</ul>
					</li>
		
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-clock-o"></i> Zeit <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="index.php?func=import.zeit">Zeitliste Einlesen</a></li>
							<li><a href="index.php?func=ziel.edit">Readerzeit manuell Bearbeiten</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="index.php?func=einlaufListe">Einlaufliste</a></li>
							<li><a href="index.php?func=ziel.analyse">Analyse Zielzeiten</a></li>
						</ul>
					</li>
					<li><a href="index.php?func=auswertung"><i class="fa fa-cog"></i> Auswertung</a></li>
				</ul>
				
<?php 
if ($testDiv == true) {
	echo "<div class=\"nav navbar-nav navbar-right testsystem\">Das ist das Testsystem</div>";
}
?>
		
			</div>
		</div>
	</nav>
	
	<div class="main">
	<h2 id="page-header" class="page-header text-center"><?php echo $_SESSION['vTitel']; ?></h2>
		<div class="race-table">

<?php
if ($showContent == true) { 
	$func[0]();
} else {
	echo "Use of disallowed function"; die;
}
?>
		
		</div>

		<div class="content-table"></div>
	</div>

	
	<div class="modal fade" tabindex="-1" role="dialog" id="modal" aria-labelledby="gridSystemModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
      			<div class="modal-header">
      				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      				<h4 class="modal-title" id="gridSystemModalLabel">Laufwertung</h4>
      			</div>
      			<div class="modal-body" id="modal-body">
      				<span class="text-muted">loading...</span>
      			</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" onclick="clearModal(); return false;" data-dismiss="modal">Close</button>
				</div>
    		</div>
 		</div>
	</div>
	
	
</body>
</html>

<?php
$link->close();
#phpinfo(32);
#print_r($_SESSION);
?>