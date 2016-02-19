<?php
/*
 * Created on 06.11.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function rennen() {
		
	if (isset($_GET['id'])) { showRaceEditForm(); } else { showRennen(); }
	
}

function lockRace($rennen) {
	
	$sql = "select lockRace from lauf where ID = $rennen;";
	$result = dbRequest($sql, 'SELECT');
	foreach ($result[0] as $row) {
		$lock = $row['lockRace'];
	}
	
	if( $lock == 1 ) { $lock = 0; } else { $lock = 1; }
	
	$sql = "update lauf set lockRace = $lock where ID = $rennen;";
	$result = dbRequest($sql, 'INSERT');

	if(!$result[0]) {
		$lock = "-";
	}
	echo $lock;
}

function showRennen() {

?>

	<h3>Veranstaltungen</h3>
	<a type="button" href="index.php?func=rennen&id=new" class="btn btn-success pull-right btn-new-top">neues Rennen</a>
	
	<div class="table-responsive">
		<table class="table table-striped table-vcenter">
			<thead>
				<tr>
					<th>ID</th>
					<th>Titel</th>
					<th>Bemerkung</th>
					<th>Start</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
	
<?php

	$veranstaltung = $_SESSION['vID'];
	$sql = "select * from lauf where vID = $veranstaltung order by start asc, titel;";
	$result = dbRequest($sql, 'SELECT');

	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			
			$lockIcon = "fa-lock";
			$lock = 0;
			if($row['lockRace'] == 0) { $lockIcon = 'fa-unlock'; $lock = 1; }
			
?>

				<tr>
					<td><?php echo $row['ID'] ?></td>
					<td><?php echo $row['titel']; ?></td>
					<td><?php echo $row['untertitel']; ?></td>
					<td><?php echo $row['start']; ?></td>
					<td>
						<a type="button" class="btn btn-default btn-small-border" href="<?php echo $_SERVER["REQUEST_URI"]."&id=".$row['ID']; ?>">
							<i class="fa fa-wrench"></i>
						</a>
						<a type="button" class="btn btn-default btn-small-border" onclick="javascript:lockRace(<?php echo $row['ID'];?>); return false;" href="">
							<i id="lock-<?php echo $row['ID']; ?>" class="fa <?php echo $lockIcon; ?>"></i>
						</a>
					</td>
				</tr>

<?php

		}
		
?>

			</tbody>
		</table>
	</div>

<?php

	}
	
}

function showRaceEditForm() {
		
	global $config;
	$ID = "new";
	$start = $_SESSION['vDatum']." 00:00:00";
	$titel = ""; $teamAnz = 0; $untertitel = "";
	$dat[0] = ""; $dat[1] = ""; $dat[2] = "";
	$kl = 0; $vkl = 0; $use_lID = 0; $tr = 0; $sl = 1; $rr = 0;
	$rdVorgabe = 0; $readerIp = "0.0.0.0"; $uTemplate = ""; $uDefinition = "";
			
	if($_GET['id'] != "new") {
		$sql = "select * from lauf where ID = ".$_GET['id'];
		$result = dbRequest($sql, 'SELECT');

		foreach ($result[0] as $row) {
			$titel = $row['titel'];
			$untertitel = $row['untertitel'];
			$start = $row['start'];
			$ID = $row['ID'];
			$kl = $row['klasse'];
			$vkl = $row['vklasse'];
			$teamAnz = $row['team_anz'];
			$uTemplate = $row['uTemplate'];
			$uDefinition = $row['uDefinition'];
			$rr = $row['rundenrennen'];
			$tr = $row['teamrennen'];
			$sl = $row['showLogo'];
			$use_lID = $row['use_lID'];
			$rdVorgabe = $row['rdVorgabe'];
			$readerIp = $row['mainReaderIp'];
		}					
	}
	
	$sql = "select * from klasse order by name";
	$result = dbRequest($sql, 'SELECT');
	$kID = 0;
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			$kArray[$kID]['ID'] = $row['ID'];
			$kArray[$kID]['name'] = $row['name'];
			$kID++;
		}				
	}

?>
		
	<script>
		$( document ).ready( function() {
			
			$("#editRennen").submit(function(event){
			    // cancels the form submission
			    event.preventDefault();

			    var msg = '';
				if( $('#title').val().length  < 2 ) { msg = msg + '<strong>Titel</strong> darf nicht leer sein<br>'; }
				if( $('#start').val().length  != 19 ) { msg = msg + '<strong>Startzeit</strong> ist nicht im richtigen Format, oder leer<br>'; }
				
			    if( msg == '' ) {
					submitForm('#editRennen', 'index.php?func=rennen');
			    } else {
					$(".alert").html(msg);
					$(".alert").removeClass('hidden');
			    }
			});
			
		});
	</script>
	
	<div class="alert alert-danger hidden col-sm-offset-3 col-sm-6" role="alert"></div>
	<form role="form" class="form-horizontal" id="editRennen" name="editRennen">
		<div class="form-group">
			<input type="hidden" name="form" value="saveRennen">
			<input type="hidden" name="id" value="<?php echo $ID; ?>">
		</div>
		<div class="form-group">
			<label for="title" class="col-sm-4 control-label">Titel:</label>
			<div class="col-sm-5">
				<input name="title" maxlength="200" type="text" class="form-control" id="title" placeholder="Titel" value="<?php echo $titel; ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="subTitle" class="col-sm-4 control-label">Bemerkung:</label>
			<div class="col-sm-5">
				<input name="subTitle" maxlength="200" type="text" class="form-control" id="subTitle" placeholder="Untertitel" value="<?php echo $untertitel; ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="start" class="col-sm-4 control-label">Start:</label>
			<div class="col-sm-5">
				<input name="start" maxlength="200" type="text" class="form-control" id="start" placeholder="Start" value="<?php echo $start; ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="klasse" class="col-sm-4 control-label">Klasse:</label>
			<div class="col-sm-5">
				<select class="form-control" name="klasse">
<?php

	foreach($kArray as $k) {
		if($kl == $k['ID']) { $s="selected"; } else { $s=""; }
		echo "<option	value=\"".$k['ID']."\" $s>".$k['name']."</option>\n";
	}
	
?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="vklasse" class="col-sm-4 control-label">Klasse (Mannschaft):</label>
			<div class="col-sm-5">
				<select class="form-control" name="vklasse">
<?php

	foreach($kArray as $k) {
		if($vkl == $k['ID']) { $s="selected"; } else { $s=""; }
		echo "<option	value=\"".$k['ID']."\" $s>".$k['name']."</option>\n";
	}
	
?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="teamAnz" class="col-sm-4 control-label">Teammitglieder:</label>
			<div class="col-sm-5">
				<input name="teamAnz" maxlength="200" type="text" class="form-control" id="teamAnz" placeholder="Teammitglieder" value="<?php echo $teamAnz; ?>">
			</div>
		</div>
	
		<div class="form-group">
			<label for="use_lID" class="col-sm-4 control-label">Lauf ID bei der Auswertung berücksichtigen:</label>
			<label class="radio-inline">
				<input type="radio" name="use_lID" id="use_lID_nein" value="0" <?php if ($use_lID == 0) { echo "checked"; } ?>>Nein
			</label>

			<label class="radio-inline">
  				<input type="radio" name="use_lID" id="use_lID_ja" value="1" <?php if ($use_lID == 1) { echo "checked"; }?>>Ja
			</label>
		</div>
		
	
		<div class="form-group">
			<label for="teamrennen" class="col-sm-4 control-label">Teamrennen:</label>
			<label class="radio-inline">
				<input type="radio" name="teamrennen" id="teamrennen_nein" value="0" <?php if ($tr == 0) { echo "checked"; } ?>>Nein
			</label>

			<label class="radio-inline">
  				<input type="radio" name="teamrennen" id="teamrennen_ja" value="1" <?php if ($tr == 1) { echo "checked"; }?>>Ja (relevant für die Darstellung der Ergebnisliste)
			</label>
		</div>
		
		
		<div class="form-group">
			<label for="showLogo" class="col-sm-4 control-label">openTiming Logo anzeigen:</label>
			<label class="radio-inline">
				<input type="radio" name="showLogo" id="showLogo_nein" value="0" <?php if ($sl == 0) { echo "checked"; } ?>>Nein
			</label>

			<label class="radio-inline">
  				<input type="radio" name="showLogo" id="showLogo_ja" value="1" <?php if ($sl == 1) { echo "checked"; }?>>Ja
			</label>
		</div>
		
		
		<div class="form-group">
			<label for="rr" class="col-sm-4 control-label">Rundenrennen:</label>
			<label class="radio-inline">
				<input type="radio" name="rr" id="rr_0" value="0" <?php if ($rr == 0) { echo "checked"; } ?>>kein Rundenrennen
			</label>
			<div class="col-sm-offset-4">
				<div class="radio">
					<label>
		  				<input type="radio" name="rr" id="rr_1" value="1" <?php if ($rr == 1) { echo "checked"; }?>>Rundenrennen mit Zeitvorgabe
					</label>
				</div>
			</div>
			<div class="col-sm-offset-4">
				<div class="radio form-inline">
					<label>
		  				<input type="radio" name="rr" id="rr_2" value="2" <?php if ($rr == 2) { echo "checked"; }?>>Rundenrennen mit 
					</label>

<?php
 
	if($rdVorgabe == "") { $rdVorgabe = 1;}

?>

					<input name="rdVorgabe" size="3" maxlength="2" type="text" class="form-control input-sm input-very-small" id="rdVorgabe" placeholder="" value="<?php echo $rdVorgabe; ?>"> Runden Vorgabe
				</div>
			</div>
		</div>
	
		<div class="form-group">
			<label for="reader" class="col-sm-4 control-label">Reader IP für Rundenzeiten:</label>
			<div class="col-sm-5">
				<input name="reader" maxlength="200" type="text" class="form-control" id="reader" placeholder="Reader" value="<?php echo $readerIp; ?>">
			</div>
		</div>
		
		<div class="form-group">
			<label for="uTemplate" class="col-sm-4 control-label">Urkundenvorlage:</label>
			<div class="col-sm-5">
				<input name="uTemplate" maxlength="200" type="text" class="form-control" id="uTemplate" placeholder="Urkundenvorlage" value="<?php echo $uTemplate; ?>">
			</div>
		</div>
		
		<div class="form-group">
			<label for="uDefinition" class="col-sm-4 control-label">Urkundendefinition:</label>
			<div class="col-sm-5">
				<input name="uDefinition" maxlength="200" type="text" class="form-control" id="uDefinition" placeholder="Urkunden Definition" value="<?php echo $uDefinition; ?>">
			</div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-offset-4 col-sm-5">
				<button type="submit" id="submit" class="btn btn-success">save</button>
				<a type="button" class="btn btn-default" href="index.php?func=rennen">cancel</a>
			</div>
		</div>
	</form>

<?php

}


function saveRennen() {
	
	$teamAnz = 0;
	if($_POST['id'] != "new") {
		$sql = "update lauf set vID = '".$_SESSION['vID']."',
							 titel = '".$_POST['title']."',
							 untertitel = '".$_POST['subTitle']."',
							 start = '".$_POST['start']."',
							 klasse = '".$_POST['klasse']."',
							 team_anz = ".$_POST['teamAnz'].",
							 uTemplate = '".$_POST['uTemplate']."',
							 uDefinition = '".$_POST['uDefinition']."',
							 rundenrennen = ".$_POST['rr']." ,
							 use_lID = ".$_POST['use_lID'].",
							 teamrennen = ".$_POST['teamrennen'].",
							 rdVorgabe = ".$_POST['rdVorgabe'].",
							 vklasse = ".$_POST['vklasse'].",
							 showLogo = ".$_POST['showLogo'].",
							 mainReaderIp = '".$_POST['reader']."'
						where ID = ".$_POST['id'].";";
	} else {
		$sql = "insert into lauf (vID, titel, untertitel, start,
										klasse, team_anz, uDefinition, uTemplate,
										rundenrennen, use_lID, teamrennen, rdVorgabe,
										vklasse, showLogo, mainReaderIp) values
										( '".$_SESSION['vID']."',
										'".$_POST['title']."',
										'".$_POST['subTitle']."',
										'".$_POST['start']."',
										".$_POST['klasse'].",
										".$_POST['teamAnz'].",
										'".$_POST['uDefinition']."',
										'".$_POST['uTemplate']."',
										".$_POST['rr'].",
										".$_POST['use_lID'].",
										".$_POST['teamrennen'].",
										".$_POST['rdVorgabe'].",
										".$_POST['vklasse'].",
										".$_POST['showLogo'].",
										'".$_POST['reader']."')";
	}

	//echo $sql;
	$result = dbRequest($sql, 'INSERT');
	if($result[2] == "") {
		echo 'ok';
	} else {
		echo $result[2];
	}

}

?>