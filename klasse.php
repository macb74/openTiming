<?php
/*
 * Created on 22.11.2015
 *
 */

function klasse() {

	if (!isset($_GET['id'])) {
		showKlassen();
	} else {
		showKlasseEditForm();
	}

}	

function showKlassen() {

?>

	<h3>Klassen</h3>
	<a type="button" href="index.php?func=klasse&id=new" class="btn btn-success pull-right btn-new-top">neue Altersklassen</a>
	
	<div class="alert alert-danger hidden col-sm-offset-3 col-sm-6" role="alert"></div>
	<div class="clearfix"></div>	<!-- umbruch nach alert -->
	<div class="table-responsive">
		<table class="table table-striped table-vcenter">
			<thead>
				<tr>
					<th>Name</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>

<?php 
	
	$sql = "select * from klasse order by name asc;";
	$result = dbRequest($sql, 'SELECT');
	
	if($result[1] > 0) {
		foreach ($result[0] as $row) {

?>

			<tr>
				<td><?php echo $row['name']; ?></td>
				<td>
					<a type="button" class="btn btn-default btn-small-border" href="<?php echo $_SERVER["REQUEST_URI"]."&id=".$row['ID']; ?>"><i class="fa fa-wrench"></i></a>					
					<a type="button" class="btn btn-default btn-small-border" onclick="javascript:deleteFullKlasse(<?php echo $row['ID']; ?>); return false;" href="#"><i class="fa fa-trash"></i></a>
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
	
function showKlasseEditForm() {

	if ($_GET['id'] == "new") {
		$sql = "insert into klasse (name) value (' neue Klasse')";
		$result = dbRequest($sql, 'INSERT');
		$kID = $result[3];
	} else {
		$kID = $_GET['id'];
	}
	
	$sql = "select * from klasse where ID = ".$kID;
	$result = dbRequest($sql, 'SELECT');

	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			$name = $row['name'];
			$kID = $row['ID'];
		}
	}
		
?>

	<script>
		$( document ).ready( function() {

			$("#editKlasse").submit(function(event){
				event.preventDefault();
	
			    var msg = '';
				if( $('#name').val().length  < 2 ) { msg = msg + '<strong>Name</strong> darf nicht leer sein<br>'; }
				
			    if( msg == '' ) {
					submitForm('#editKlasse', 'index.php?func=klasse');
			    } else {
					$(".alert").html(msg);
					$(".alert").removeClass('hidden');
			    }
			});
			
		});
	</script>


	<h3>Klasse <small><?php echo $name; ?></small></h3>
	<a type="button" href="#" onclick="javascript:addKlasseZeile(<?php echo $kID; ?>); return true;" class="btn btn-success pull-right btn-new-top">neue Zeile</a>
	
	<div class="alert alert-danger hidden col-sm-offset-3 col-sm-6" role="alert"></div>
	<form role="form" class="form-horizontal" id="editKlasse" name="editKlasse">
		<div class="form-group">
			<input type="hidden" name="form" value="saveKlasse">
			<input type="hidden" name="id" value="<?php echo $kID; ?>">
		</div>
		
		<div class="form-group">
			<label for="name" class="col-sm-4 control-label">Name:</label>
			<div class="col-sm-5">
				<input name="name" maxlength="200" type="text" class="form-control" id="name" placeholder="Name" value="<?php echo $name; ?>">
			</div>
		</div>

		<div class="col-sm-offset-3 col-sm-7">
		<table class="table table-striped table-vcenter">
			<thead>
				<tr>
					<th>Name</th>
					<th>Geschlecht</th>
					<th>Alter von</th>
					<th>Alter bis</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			
<?php 

	if ($_GET['id'] != "new") {
		$sql = "select * from klasse_data where kID = $kID order by name";
		$result = dbRequest($sql, 'SELECT');

		$i= 0;
		if($result[1] > 0) {
			foreach ($result[0] as $row) {

?>
				<tr>
					<td>
						<input type="hidden" name="kdid<?php echo $i; ?>" value="<?php echo $row['ID']; ?>">
						<input name="name<?php echo $i; ?>" type="text" class="form-control input-sm" id="name<?php echo $i; ?>" placeholder="Name" value="<?php echo $row['name']; ?>">
					</td>
					<td><input name="gender<?php echo $i; ?>" type="text" class="form-control input-sm" id="gender<?php echo $i; ?>" placeholder="Geschlecht" value="<?php echo $row['geschlecht']; ?>"></td>
					<td><input name="altervon<?php echo $i; ?>" type="text" class="form-control input-sm" id="altervon<?php echo $i; ?>" placeholder="von" value="<?php echo $row['altervon']; ?>"></td>
					<td><input name="alterbis<?php echo $i; ?>" type="text" class="form-control input-sm" id="alterbis<?php echo $i; ?>" placeholder="bis" value="<?php echo $row['alterbis']; ?>"></td>
					<td><a type="button" class="btn btn-default" onclick="javascript:deleteKlasse(<?php echo $row['ID'].", ".$_GET['id'];?>); return false;" href="#"><i class="fa fa-trash"></i></a></td>
				</tr>

<?php
				$i++;
			}
		}
	}

?>
			</tbody>
		</table>
	</div>
	
	<input type="hidden" name="count" value="<?php echo $i; ?>">
	
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-5">
			&nbsp;&nbsp;&nbsp;<button type="submit" id="submit" class="btn btn-success">save</button>
			<a type="button" class="btn btn-default" href="index.php?func=klasse">cancel</a>
		</div>
	</div>
	
	</form>	
<?php

}

function saveKlasse() {
	$sql = "update klasse set name = '".htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8')."' where ID = ".$_POST['id'];
	$result = dbRequest($sql, 'UPDATE');

	$i = 0;
	$count = $_POST['count'] - 1;
	while ($i <= $count) {
		$kdID = 0; $n = 0; $g = ""; $v = 0; $b = 0;

		$kdID = $_POST['kdid'.$i];
		$n = $_POST['name'.$i];
		$g = strtoupper($_POST['gender'.$i]);
		$v = $_POST['altervon'.$i];
		$b = $_POST['alterbis'.$i];
		$sql = "update klasse_data set name = '$n', geschlecht = '$g', altervon = $v, alterbis = $b where ID = $kdID";
		$result = dbRequest($sql, 'UPDATE');
		if (!$result[0]) { die('update klasse_data - Invalid query: ' . $result[2]); }
		$i++;
	}
	
	echo 'ok';
}

function addKlasse() {

	$name = " Name";
	$geschlecht = "X";
	$altervon = 0;
	$alterbis = 0;
	$sql = "insert into klasse_data " .
			"(kID, name, geschlecht, altervon, alterbis) " .
			"values ( ".$_GET['id'].", '$name', '$geschlecht', $altervon, $alterbis)";
	$result = dbRequest($sql, 'INSERT');
	
	if($result[2] == "") {
		echo 'ok';
	} else {
		echo $result[2];
	}

}

function deleteKlasse() {
	$sql = "delete from klasse_data where ID = ".$_GET['id'];
	$result = dbRequest($sql, 'DELETE');
	
	if($result[2] == "") {
		echo 'ok';
	} else {
		echo $result[2];
	}
}

function deleteFullKlasse() {
	
	$sql = "select * from lauf where klasse = ".$_GET['id']." or vklasse = ".$_GET['id'].";";
	$result = dbRequest($sql, 'SELECT');
	
	if($result[1] != 0) {
		echo "Diese Klasse wird noch verwendet";
		die;
	}
	
	$sql = "delete from klasse_data where kid = ".$_GET['id'];
	$result = dbRequest($sql, 'DELETE');
	
	$sql = "delete from klasse where ID = ".$_GET['id'];
	$result = dbRequest($sql, 'DELETE');

	if($result[2] == "") {
		echo 'ok';
	} else {
		echo $result[2];
	}
}

?>
