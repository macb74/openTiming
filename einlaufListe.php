<?php

function einlaufListe() {
	echo einlaufListeForm();
}

function einlaufListeForm() {

?>
	<script>
	
		$(document).ready(function(){
			checkEinlaufListe( this );
		});
	
	</script>
	
	<h3>Einlaufliste</h3>
	
	<div class="table-responsive">
		<table class="table table-striped table-vcenter">
			<thead>
				<tr>
					<th>ID</th>
					<th>Titel</th>
					<th>Start</th>
					<th></th>
				</tr>
			</thead>
		<tbody>
	
<?php

	$veranstaltung = $_SESSION['vID'];
	$sql = "select * from lauf where vID = $veranstaltung order by start asc, titel;";
	$result = dbRequest($sql, 'SELECT');
	
	if(!isset($_SESSION['rennenIDs']) || $_SESSION['rennenIDs'] == '') {
		$rennenIDs = array();
	} else {
		$rennenIDs = explode( ",", $_SESSION['rennenIDs']);
	}

	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			if(in_array($row['ID'], $rennenIDs) === true) { $c = "checked"; } else { $c = ""; }
				
	?>
	
				<tr>
					<td><?php echo $row['ID']; ?></td>
					<td><?php echo $row['titel']." / ".$row['untertitel']; ?></td>
					<td><?php echo substr($row['start'], 10); ?></td>
					<td>
						<input <?php echo $c; ?> class="chkboxtable" type="checkbox" onchange="javascript:checkEinlaufListe( this );" name="<?php echo $row['ID']; ?>" id="<?php echo $row['ID']; ?>">
					</td>
				</tr>

	<?php
		
			}
		}
		
	?>
			</tbody>
		</table>
	</div>
		
	
<?php

}

function showEinlaufListe() {
	$rennen = $_GET['id'];
	$action = $_GET['action'];
		
	// der anzuzeigenden rennen werden in der Variable $_SESSION['rennenIDs'] gespeichert
	if(!isset($_SESSION['rennenIDs']) || $_SESSION['rennenIDs'] == '') { 
		$rennenIDs = array(); 
	} else {
		$rennenIDs = explode( ",", $_SESSION['rennenIDs']);		
	}

	if($action == 'add') {
		if(in_array($rennen, $rennenIDs) === false) {
			array_push( $rennenIDs , $rennen );
		}
	} else {
		if(in_array($rennen, $rennenIDs) === true) {
			$id = array_search($rennen, $rennenIDs);
			unset($rennenIDs[$id]);
		}
	}
	
	$_SESSION['rennenIDs'] = implode(",", $rennenIDs);
	//echo $_SESSION['rennenIDs'];
			
	
	if($_SESSION['rennenIDs'] != "") {
		$i = 0;
		$rIDs = explode( ",", $_SESSION['rennenIDs']);
		$tmptable = "tmp_".rand(100, 999);
		

		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS $tmptable (
		`ID` bigint(20) NOT NULL DEFAULT '0',
		`stnr` int(11) NOT NULL,
		`vorname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`nachname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`verein` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`klasse` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
		`lid` int(11) NOT NULL,
		`lname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		`usemantime` int(1) NOT NULL DEFAULT '0',
		`manzeit` time NOT NULL DEFAULT '00:00:00',
		`zielzeit` datetime,
		`millisecond` int(3),
		`startzeit` datetime,
		PRIMARY KEY (`ID`)
		) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		
		$result = dbRequest($sql, 'INSERT');
		
		foreach ($rIDs as $rid) {
			$rd = getRennenData($rid);
			$startZeit = $rd['startZeit'];
			if($rd['use_lID'] == 1) { $sql_lID = "and lid = $rid "; } else { $sql_lID = ""; }
			
			$sql = "INSERT into $tmptable (SELECT t.ID, t.stnr, t.vorname, t.nachname, t.verein, t.klasse, t.lid, '".$rd['titel']."' as lname, t.usemantime, t.manzeit, min(z.zeit) as zielzeit, z.millisecond, '".$startZeit."' as startzeit FROM `teilnehmer` as t 
				LEFT JOIN (select nummer, zeit, millisecond from zeit where zeit > '".$startZeit."' and vID = ".$_SESSION["vID"]." $sql_lID) as z ON t.stnr = z.nummer 
				where t.vid = ".$_SESSION["vID"]." and t.lid = ".$rid." and disq = 0 and del = 0 group by t.stnr);";
			
			//echo $sql;
			$result = dbRequest($sql, 'INSERT');
			$i++;
		}
		
		// Zielzeit fuer manuell gesetzte Laufzeit berechnen und eintragen
		$sql = "select * from $tmptable where usemantime = 1";
		$result = dbRequest($sql, 'SELECT');
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				$zielzeit = getSeconds($row['startzeit']) + getSeconds('1970-01-01 '.$row['manzeit']);
				date_default_timezone_set("UTC");
				$zielzeit = date("Y-m-d H:i:s", $zielzeit);
				date_default_timezone_set("Europe/Berlin");
				$sql = "update $tmptable set zielzeit = '".$zielzeit."' where ".$row['ID']." = ID";
				$result = dbRequest($sql, 'UPDATE');
			}
		}

 		// um richtig sortieren zu koennen werden die Zeiten in der spalte manzeit zusammengefasst.
 		$sql = "update $tmptable set manzeit = zielzeit where zielzeit is not NULL and usemantime <> 2";
 		$result = dbRequest($sql, 'UPDATE');
		
		$sql = "SELECT * from $tmptable where zielzeit is not NULL or usemantime = 2 order by manzeit, millisecond asc;";
		$result = dbRequest($sql, 'SELECT');
		
		$html2 = "";
		$i=1;
		$dataSetBefore['zeit'] = 'none';
		$dataSetBefore['klasse'] = 'none';
		
		$sameTimeAsBefore ='';
		
?>
			<div class="table-responsive">
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<th>Name</th>
							<th>Verein</th>
							<th>Klasse</th>
							<th>Rennen</th>
							<th>Laufzeit</th>
							<th>Stnr.</th>
							<th>Zielzeit</th>
						</tr>
					</thead>
		
<?php		
		
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				$laufzeit = getRealTime($row['startzeit'], $_SESSION['vDatum']." ".$row['manzeit']);
				if($row['usemantime'] == 1 ) { $umt = '*'; } else { $umt = ''; }

?>
			<tr>
				<td><a href="index.php?func=teilnehmer&id=<?php echo $row['ID']; ?>&nextFunc=einlaufListe"><?php echo $row['nachname'].", ".$row['vorname']; ?></a></td>
				<td><?php echo $row['verein']; ?></td>
				<td><?php echo $row['klasse']; ?></td>
				<td><?php echo $row['lname']; ?></td>
				<td <?php echo $sameTimeAsBefore; ?>><?php echo $laufzeit.$umt; ?></td>
				<td><?php echo $row['stnr']; ?></td>
				<td>
					<div class="col-sm-5">
						<input id="zeit_<?php echo $row['ID']; ?>" class="form-control input-sm input-very-small" value="<?php echo $row['manzeit']; ?>">
					</div>
					&nbsp;&nbsp;<a class="manzeit" id="<?php echo $row['ID']; ?>" onclick="javascript:saveManZielzeit( this, 'save'); return false;" href="#"><i class="fa fa-floppy-o fa-lg"></i></a>
					
					<?php if ($row['usemantime'] == 2 ) { ?>
					&nbsp;&nbsp;|&nbsp;&nbsp;<a class="setmanzeit" id="<?php echo $row['ID']; ?>" onclick="javascript:saveManZielzeit( this, 'del'); return false;" href="#"><i class="fa fa-times fa-lg"></i></a>
					<?php } ?>	
				</td>					
			</tr>
<?php				
				$dataSetBefore['zeit'] = $laufzeit;
				$dataSetBefore['klasse'] = $row['klasse'];
				
				$i++;
			}
		}
		
		$sql = "SELECT * from $tmptable where zielzeit is NULL and usemantime <> 2";
		
		$result = dbRequest($sql, 'SELECT');
		
		$dataSetBefore['zeit'] = 'none';
		$dataSetBefore['klasse'] = 'none';
		
		$sameTimeAsBefore ='';
		
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				$laufzeit = '00:00:00';
				if($row['usemantime'] == 1 ) { $umt = '*'; $laufzeit = $row['manzeit']; } else { $umt = ''; }
				
				
?>
				<tr>
					<td><a href="index.php?func=teilnehmer&id=<?php echo $row['ID']; ?>&nextFunc=einlaufListe"><?php echo $row['nachname'].", ".$row['vorname']; ?></a></td>
					<td><?php echo $row['verein']; ?></td>
					<td><?php echo $row['klasse']; ?></td>
					<td><?php echo $row['lname']; ?></td>
					<td <?php echo $sameTimeAsBefore; ?>><?php echo $laufzeit.$umt; ?></td>
					<td><?php echo $row['stnr']; ?></td>
					<td>
						<div class="col-sm-5">
							<input id="zeit_<?php echo $row['ID']; ?>" class="form-control input-sm input-very-small" value="<?php echo $row['manzeit']; ?>">
						</div>
						&nbsp;&nbsp;<a class="manzeit" id="<?php echo $row['ID']; ?>" onclick="javascript:saveManZielzeit( this, 'save'); return false;" href="#"><i class="fa fa-floppy-o fa-lg"></i></a>
					
						<?php if ($row['usemantime'] == 2 ) { ?>
						&nbsp;&nbsp;|&nbsp;&nbsp;<a class="setmanzeit" id="<?php echo $row['ID']; ?>" onclick="javascript:saveManZielzeit( this, 'del'); return false;" href="#"><i class="fa fa-times fa-lg"></i></a>
						<?php } ?>
					</td>					
				</tr>
				
<?php
				$dataSetBefore['zeit'] = $laufzeit;
				$dataSetBefore['klasse'] = $row['klasse'];
				$i++;
			}
		}
?>
			</tbody>
		</table>
	</div>
<?php 	
	
	}
}

function saveManZielzeit() {
	$id = $_GET['id'];
	$action = $_GET['action'];
	$time = $_GET['time'];
	
	if ($action == 'save') {
		$sql = "update teilnehmer set usemantime = 2, manzeit = '".base64_decode($time)."' where id = $id";
	} elseif ($action == 'del') {
		$sql = "update teilnehmer set usemantime = 0, manzeit = '' where id = $id";
	}
	$result = dbRequest($sql, 'UPDATE');
	echo "ok";
}

