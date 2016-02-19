<?php
/*
 * Created on 23.11.2015
 *
 */

function veranstaltung() {
	global $func;
	$id = 0;
	$titel = "";
	$untertitel = "";
	$datum = "";
		
	# display Form
	if (isset($_GET['id'])) {
		
		if($_GET['id'] != "new") {
			$sql = "select * from veranstaltung where ID = ".$_GET['id'];
			$result = dbRequest($sql, 'SELECT');
			
			foreach ($result[0] as $row) {
				$titel = $row['titel'];
				$untertitel = $row['untertitel'];
				$datum = $row['datum'];
				$id = $row['ID'];
			}
		}

?>

	<script>
		$( document ).ready( function() {
			
			$('.datepicker').datepicker({
			    format: 'yyyy-mm-dd',
			    autoclose: true,
			    language: 'de',
			    orientation: 'bottom'
			})


			$("#editVeranstaltung").submit(function(event){
			    // cancels the form submission
			    event.preventDefault();

			    var msg = '';
				if( $('#title').val().length  < 2 ) { msg = msg + '<strong>Titel</strong> darf nicht leer sein<br>'; }
				if( $('#date').val().length  != 10 ) { msg = msg + '<strong>Datum</strong> ist nicht im richtigen Format, oder leer<br>'; }
				
			    if( msg == '' ) {
					submitForm('#editVeranstaltung', 'index.php?func=veranstaltung');
			    } else {
					$(".alert").html(msg);
					$(".alert").removeClass('hidden');
			    }
			});
			
		});
	</script>
	
	<div class="alert alert-danger hidden col-sm-offset-3 col-sm-6" role="alert"></div>
	<form role="form" class="form-horizontal" id="editVeranstaltung" name="editVeranstaltung">
		<div class="form-group">
			<input type="hidden" name="form" value="saveVeranstaltung">
			<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
		</div>
		<div class="form-group">
			<label for="title" class="col-sm-4 control-label">Titel:</label>
			<div class="col-sm-5">
				<input name="title" maxlength="200" type="text" class="form-control" id="title" placeholder="Titel" value="<?php echo $titel; ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="subTitle" class="col-sm-4 control-label">Untertitel:</label>
			<div class="col-sm-5">
				<input name="subTitle" maxlength="200" type="text" class="form-control" id="subTitle" placeholder="Untertitel" value="<?php echo $untertitel; ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="date" class="col-sm-4 control-label">Datum:</label>
			<div class="col-sm-5">
				<div class="input-group datepicker date">
					<input name="datum" type="text" class="form-control" id="date" placeholder="Datum" value="<?php echo $datum; ?>">
					<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
				</div>
			</div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-offset-4 col-sm-5">
				<button type="submit" id="submit" class="btn btn-success">save</button>
				<a type="button" class="btn btn-default" href="index.php?func=veranstaltung">cancel</a>
			</div>
		</div>
	</form>
			
<?php

	} else {
		
		# Display Veranstaltungen
		
?>

	<h3>Veranstaltungen</h3>
	<a type="button" href="index.php?func=veranstaltung&id=new" class="btn btn-success pull-right btn-new-top">neue Veranstaltung</a>
	
		
	<div class="table-responsive">
		<table class="table table-striped table-vcenter">
			<thead>
				<tr>
					<th>ID</th>
					<th>Titel</th>
					<th>Untertitel</th>
					<th>Datum</th>
					<th>Action</th>
				</tr>
			</thead>
		<tbody>

<?php		

		$sql = "select * from veranstaltung order by datum desc;";		
		$result = dbRequest($sql, 'SELECT');

		foreach ($result[0] as $row) {
			if(isset($_SESSION['vID']) && $_SESSION['vID'] == $row['ID']) {
				$b = "bold";
			} else {
				$b = "";
			}
			
?>

				<tr>
					<td><?php echo $row['ID'] ?></td>
					<td><a class="veranstaltung-<?php echo $row['ID']." ".$b; ?>" href="#" onclick="javascript:selectVeranstaltung(<?php echo $row['ID']?>); return false;"><?php echo $row['titel']; ?></a></td>
					<td><?php echo $row['untertitel']; ?></td>
					<td><?php echo $row['datum']; ?></td>
					<td><a type="button" class="btn btn-default btn-small-border" href="<?php echo $_SERVER["REQUEST_URI"]."&id=".$row['ID']; ?>"><i class="fa fa-wrench"></i></a></td>
				</tr>
			
			
<?php } ?>

			</tbody>
		</table>
	</div>
		
<?php
	}
}

function saveVeranstaltung() {
	if($_POST['id'] == "new") {
		$sql = "insert into veranstaltung (titel, untertitel, datum) values ( '".htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8')."', '".htmlspecialchars($_POST['subTitle'], ENT_QUOTES, 'UTF-8')."', '".htmlspecialchars($_POST['datum'], ENT_QUOTES, 'UTF-8')."')";
	} else {
		$sql = "update veranstaltung set titel = '".htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8')."', untertitel = '".$_POST['subTitle']."', datum = '".htmlspecialchars($_POST['datum'], ENT_QUOTES, 'UTF-8')."' where ID = ".$_POST['id'].";";
	}
	$result = dbRequest($sql, 'INSERT');
	if($result[2] == "") {
		echo 'ok';
	} else {
		echo $result[2];
	}
}
?>
