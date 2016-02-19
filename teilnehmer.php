<?php
/*
 * Created on 23.11.2015
 *
 */
function teilnehmer() {

	if (isset($_GET['id'])) { showTeilnehmerEditForm(); } else { showTeilnehmer(); }
	
}

function saveTeilnehmer() {
	
	//phpinfo(32);

	$f[0] 		= $_SESSION['vID'];
	$f[1] 		= $_POST['rID'];
	$f[2]		= $_POST['id'];
	$f[3]	 	= $_POST['nachname'];
	$f[4] 		= $_POST['vorname'];
	$f[5]		= $_POST['geschlecht'];
	$f[6] 		= $_POST['ort'];
	$f[7] 		= $_POST['jahrgang'];
	$f[8] 		= $_POST['stnr'];
	$f[9] 		= $_POST['verein'];
	$f[10]		= $_POST['zeit'];
	if (isset($_POST['disq'])) { $f[11] = 1; } else { $f[11] = 0; }
	if (isset($_POST['useManTime'])) { $f[16] = 1; } else { $f[16] = 0; }
	$f[12]		= "";
	$f[13]		= 0;
	$f[15]		= $_POST['klasse'];
	$f[17]		= "";
	$f[18]		= $_POST['manRunden'];
	$f[19]		= $_POST['vklasse'];
	$f[20]		= $_POST['att'];
	
	if($_POST['id'] != "new") {
		$sql = "update teilnehmer set " .
				"vID = $f[0], lID = $f[1], nachname = '$f[3]', vorname = '$f[4]', " .
				"geschlecht = '$f[5]', ort = '$f[6]', jahrgang = $f[7], stnr = $f[8], " .
				"verein = '$f[9]', manzeit = '$f[10]', zeit = '$f[10]', useManTime = $f[16] ,disq = $f[11], klasse = '$f[15]', man_runden = $f[18], vklasse = '$f[19]', att = '$f[20]' " .
				"where ID = $f[2]";
	} else {
		$sql = "insert into teilnehmer " .
				"(vID, lID, nachname, vorname, jahrgang, geschlecht, ort, stnr, verein, zeit, manzeit, useManTime, disq, klasse, man_runden, vklasse, att) " .
				"values ( $f[0], $f[1], '$f[3]', '$f[4]', $f[7], '$f[5]', '$f[6]', '$f[8]', '$f[9]', '$f[10]','$f[10]', $f[16], $f[11], '$f[15]', $f[18], '$f[19]', '$f[20]')";			
	}
	
	//echo $sql;
	$result = dbRequest($sql, 'INSERT');
	if($result[2] == "") {
		echo 'ok';
	} else {
		echo $result[2];
	}

}

function showTeilnehmerEditForm() {
	
		$stnr		= "";
		$lID		= "";
		$nachname	= "";
		$vorname 	= "";
		$verein 	= "";
		$jahrgang	= "";
		$klasse		= "";
		$useManTime = "";
		$geschlecht	= "";
		$ort		= "";
		$zeit		= "00:00:00";
		$disq		= "";
		$id			= "";
		$autRunden	= "0";
		$manRunden	= "0";
		$vKlasse	= "";
		$att		= "";

	if($_GET['id'] != "new") {
		$sql = "select * from teilnehmer where ID = ".$_GET['id'];
		$result = dbRequest($sql, 'SELECT');

		foreach ($result[0] as $row) {
			$stnr		= $row['stnr'];
			$lID		= $row['lID'];
			$nachname	= $row['nachname'];
			$vorname 	= $row['vorname'];
			$verein 	= $row['verein'];
			$jahrgang	= $row['jahrgang'];
			$klasse		= $row['klasse'];
			$useManTime = $row['useManTime'];
			$geschlecht	= $row['geschlecht'];
			$ort		= $row['ort'];
			$zeit		= $row['zeit'];
			$disq		= $row['disq'];
			$id			= $row['ID'];
			$autRunden	= $row['aut_runden'];
			$manRunden	= $row['man_runden'];
			$vKlasse	= $row['vklasse'];
			$att		= $row['att'];
			
		}
		
		$rInfo = getRennenData($lID);
		
	} else {
		$id = 'new';
	}

?>
	<script>
	
 		$(document).ready(function(){
			
			$("#verein").autocomplete({
				source: 'ajaxRequest.php?func=getVerein&', 
				minLength: 2
			});


			$("#submit-close").click(function(event){
			    event.preventDefault();
				checkAndSubmit( 'close' );
			});

			
			$("#submit").click(function(event){
			    event.preventDefault();
				checkAndSubmit( 'next' );
			});


			$("#rID").change(function(){
				getKlasse($('#jahrgang').val(), $('#geschlecht').val(), $('#rID').val());
				showHideRunden($("option:selected", this).attr('rr'));
			});

			
			$("#jahrgang").change(function(){
				getKlasse($('#jahrgang').val(), $('#geschlecht').val(), $('#rID').val());
			});

			
			$("#geschlecht").change(function(){
				getKlasse($('#jahrgang').val(), $('#geschlecht').val(), $('#rID').val());
			});

			
			$("#manRunden").change(function(){
				var a = $("#manRunden").val();
				var b = $("#autRunden").val();

				if(a == '') { a = 0 }
				var c = parseInt(a) + parseInt(b);

				console.log(c);
				$("#sumRunden").val(c);
			});

			
			showHideRunden($("option:selected", '#rID').attr('rr'));
			getKlasse($('#jahrgang').val(), $('#geschlecht').val(), $('#rID').val());
			
		});
 		
		function checkAndSubmit( form ) {
		    var msg = '';
			if( $('#nachname').val().length  < 2 ) { msg = msg + '<strong>Nachname</strong> darf nicht leer sein<br>'; }
		    if( $('#vorname').val().length  < 2 ) { msg = msg + '<strong>Vorname</strong> darf nicht leer sein<br>'; }
			if( $('#rID').val() == 'X' ) { msg = msg + 'Bitte einen <strong>Lauf</strong> ausw&auml;hlen<br>'; }

			var next = 'teilnehmer';
			if( $('#nextFunc').val() != '' ) { next = $('#nextFunc').val(); }
			
		    if( msg == '' ) {
			    if( form == 'next' ) {
					submitForm('#editTeilnehmer', false);
					$(".alert").addClass('hidden');
			    } else {
			    	submitForm('#editTeilnehmer', 'index.php?func=' + next);
				}
		    } else {
				$(".alert").html(msg);
				$(".alert").removeClass('hidden');
		    }
		}

	</script>
	
	<div class="alert alert-danger hidden col-sm-offset-3 col-sm-6" id="alert" role="alert"></div>
	<form role="form" class="form-horizontal" id="editTeilnehmer" name="editTeilnehmer">
		<div class="form-group">
			<input type="hidden" name="form" value="saveTeilnehmer">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<input type="hidden" name="nextFunc" id= "nextFunc" value="<?php if(isset($_GET['nextFunc'])) { echo $_GET['nextFunc']; } ?>">
		</div>
		
		<div class="form-group">
			<label for="stnr" class="col-sm-4 control-label">Startnummer:</label>
			<div class="col-sm-4">
				<input name="stnr" maxlength="200" type="text" class="form-control" id="stnr" placeholder="Stnr" value="<?php echo $stnr; ?>">
			</div>
		</div>
		
		
		<div class="form-group">
			<label for="title" class="col-sm-4 control-label">Name, Vorname:</label>
			<div class="col-sm-2">
				<input name="nachname" maxlength="200" type="text" class="form-control" id="nachname" placeholder="Name" value="<?php echo $nachname; ?>">
			</div>
			<div class="col-sm-2">
				<input name="vorname" maxlength="200" type="text" class="form-control" id="vorname" placeholder="Vorname" value="<?php echo $vorname; ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="title" class="col-sm-4 control-label">Jahrgang:</label>
			<div class="col-sm-1">
				<input name="jahrgang" maxlength="200" type="text" class="form-control" id="jahrgang" placeholder="Jahrgang" value="<?php echo $jahrgang; ?>">
			</div>
			<label for="title" class="col-sm-1 control-label">Klasse:</label>
			<div class="col-sm-1">
				<input readonly name="klasse" type="text" class="form-control" id="klasse" placeholder="Klasse" value="<?php echo $klasse; ?>">
			</div>
			<div class="col-sm-1">
				<input readonly name="vklasse" type="text" class="form-control" id="vklasse" placeholder="Klasse" value="<?php echo $vKlasse; ?>">
			</div>
		</div>

		<div class="form-group">
			<label for="title" class="col-sm-4 control-label">Geschlecht:</label>
			<div class="col-sm-4">
				<select class="form-control" name="geschlecht" id="geschlecht">
					<option value="-">-</option>
					<option value="M" <?php if($geschlecht == "M") { echo "selected"; }?>>M</option>
					<option value="W" <?php if($geschlecht == "W") { echo "selected"; }?>>W</option>
					<option value="X" <?php if($geschlecht == "X") { echo "selected"; }?>>X</option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label for="verein" class="col-sm-4 control-label">Verein:</label>
			<div class="col-sm-4">
				<input name="verein" maxlength="200" type="text" class="form-control" id="verein" placeholder="Verein" value="<?php echo $verein; ?>">
			</div>
		</div>

		<div class="form-group">
			<label for="ort" class="col-sm-4 control-label">Ort:</label>
			<div class="col-sm-4">
				<input name="ort" maxlength="200" type="text" class="form-control" id="ort" placeholder="Ort" value="<?php echo $ort; ?>">
			</div>
		</div>

		<div class="form-group">
			<label for="zeit" class="col-sm-4 control-label">Zeit:</label>
			<div class="col-sm-2">
				<input name="zeit" type="text" class="form-control" id="zeit" placeholder="00:00:00" value="<?php echo $zeit; ?>">
			</div>
			<label class="col-sm-3 checkbox-inline">
  				<input type="checkbox" name="useManTime" id="useManTime" value="1" <?php if($useManTime == 1) { echo "checked"; }?>>manuell eingegebene Zeit nutzen
			</label>
		</div>
		
		<div class="form-group">
			<label for="att" class="col-sm-4 control-label">Attribut:</label>
			<div class="col-sm-4">
				<input name="att" maxlength="2" type="text" class="form-control" id="att" placeholder="Attribut" value="<?php echo $att; ?>">
			</div>
		</div>
		
		<div id="rundenrennen" class="hidden">
			<div class="form-group">
				<label for="runden" class="col-sm-4 control-label">Runden:</label>
				<div class="col-sm-1">
					<input readonly name="autRunden" type="text" class="form-control" id="autRunden" placeholder="" value="<?php echo $autRunden; ?>">
				</div>
				<div class="col-sm-1">
					<input name="manRunden" type="text" class="form-control" id="manRunden" placeholder="" value="<?php echo $manRunden; ?>">
				</div>
				<label for="title" class="col-sm-1 control-label">Summe:</label>
				<div class="col-sm-1">
					<input readonly name="sumRunden" type="text" class="form-control" id="sumRunden" placeholder="" value="">
				</div>
			</div>
		</div>
		
		<div class="form-group">
			<label for="rennen" class="col-sm-4 control-label">Lauf:</label>
			<div class="col-sm-4">
				<select id="rID" name="rID"  class="form-control">
					<option rr="0" value="X">bitte w&auml;hlen</option>

<?php

	$sql = "select * from lauf where vID = ".$_SESSION['vID']." order by start";
	$result2 = dbRequest($sql, 'SELECT');

	foreach ($result2[0] as $row2) {
		$rID 		= $row2['ID'];
		$kID 		= $row2['klasse'];
		$titel 		= $row2['titel'];
		$utitel 	= $row2['untertitel'];
		$rr			= $row2['rundenrennen'];

		if($_GET['id'] != "new") {
			if($rID == $lID) { $s="selected"; } else { $s=""; }
		}
		echo "<option rr=\"$rr\" value=\"$rID\" $s>$titel - $utitel</option>\n";
	}
	
?>

				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="disq" class="col-sm-4 control-label">Disqualifiziert:</label>
			<label class="col-sm-3 checkbox-inline">
  				<input type="checkbox" name="disq" id="disq" <?php if($disq == 1) { echo "checked"; }?>>
			</label>
		</div>
		
		<div class="form-group">
			<div class="col-sm-offset-4 col-sm-5">
				<button type="submit" id="submit" class="btn btn-success" value="save">save + new</button>
				<button type="submit" id="submit-close" class="btn btn-success" value="save + close">save + close</button>
				<a type="button" class="btn btn-default" href="index.php?func=<?php if(isset($_GET['nextFunc'])) { echo $_GET['nextFunc']; } else { echo 'teilnehmer'; } ?>">cancel</a>
			</div>
		</div>
		
		
	</form>
	
<?php
}

function showTeilnehmer()  {

	$sql = "SELECT t.*, l.titel FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID where t.vID = '".$_SESSION['vID']."' and del=0 order by nachname asc;";
	$result = dbRequest($sql, 'SELECT');

?>

	<h3>Teilnehmer</h3>
	<div class="table-responsive">
		<table class="table table-striped table-condensed">
			<thead>
				<tr>
					<th>Stnr.</th>
					<th>Name</th>
					<th>Verein</th>
					<th>JG</th>
					<th>G</th>
					<th>Klasse</th>
					<th>Rennen</th>
					<th>Zeit</th>
					<th>Platz</th>
					<th>AK Platz</th>
				</tr>
			</thead>
			<tbody>
	
<?php	
		
	if($result[1] > 0) {
		foreach ($result[0] as $row) {

?>

				<tr>
					<td><?php echo $row['stnr']; ?></td>
					<td><a href="<?php echo $_SERVER["SCRIPT_NAME"]."?func=teilnehmer&id=".$row['ID'] ?>"><?php echo $row['nachname'].", ".$row['vorname'] ?></a></td>
					<td><?php echo $row['verein']; ?></td>
					<td><?php echo $row['jahrgang']; ?></td>
					<td><?php echo $row['geschlecht']; ?></td>
					<td><?php echo $row['klasse']; ?></td>
					<td><?php echo $row['titel']; ?></td>
					<td><?php echo $row['zeit']; ?></td>
					<td><?php echo $row['platz']; ?></td>
					<td><?php echo $row['akplatz']; ?></td>
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

function deleteTeilnehmer() {
	$sql = "update teilnehmer set del = 1 where ID = ".$_GET['ID'];
	$result = dbRequest($sql, 'DELETE');
	
	if($result[2] == "") {
		echo 'ok';
	} else {
		echo $result[2];
	}	
}

function getKlasse($jg, $sex, $rennen, $ajax) {

	$jahr = substr($_SESSION['vDatum'], 0, 4);
	$alter = $jahr - $jg;

	$k[0] = getKlasseData($alter, $sex, $rennen, 0);
	$k[1] = getKlasseData($alter, $sex, $rennen, 1);
	
	if($ajax == 1) {
		echo $k[0].";".$k[1];
	} else {
		return $k;
	}
}

function getKlasseData($alter, $sex, $rennen, $mannschaft) {

	$k = "";
	if($rennen == "X") { return $k;}
	
	if($mannschaft == 0) { $klasse = 'klasse'; } else { $klasse = 'vklasse'; }
	
	$sql = "SELECT l.*, kd.* FROM `lauf` as l " .
		"INNER JOIN klasse_data as kd ON kd.kID = l.$klasse " .
		"where kd.altervon <= $alter " .
		"and kd.alterbis >= $alter " .
		"and kd.geschlecht = '$sex' " .
		"and l.ID = $rennen";
			
	$result = dbRequest($sql, 'SELECT');
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			$k = $row['name'];
		} 
	}
	
	return $k;
}

?>
