<?php

function showStartliste() {
	
	$rennen = $_GET['id'];
	$_SESSION['rID'] = $rennen;
	$_SESSION['contentFunc'] = $_GET['func'];
	$rd = getRennenData($rennen);
	
	$sql = "SELECT t.*, l.titel FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
		"where t.vID = ".$_SESSION['vID']." ".
			"and t.lid = $rennen and del= 0 and disq = 0 ".
			"order by stnr asc;";
	$result = dbRequest($sql, 'SELECT');

?>

	<h3 class="sub-header">Startliste <small><?php echo $rd['titel']; ?></small>&nbsp;&nbsp;<a onclick="javascript:clearContent(); return false;" class="btn btn-default"><i class="fa fa-eraser"></i></a></h3>
		<div class="table-responsive">
			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th>Stnr.</th>
						<th>Name.</th>
						<th>Verein</th>
						<th>JG</th>
						<th>G</th>
						<th>Klasse</th>
						<th>Att</th>
						<th>Rennen</th>
					</tr>
				</thead>
				<tbody>

<?php 	
	
	if($result[1] > 0) {
		foreach ($result[0] as $row) {

?>
			
				<tr>
					<td><?php echo $row['stnr']; ?></td>
					<td><a href="index.php?func=teilnehmer&id=<?php echo $row['ID']; ?>&nextFunc=auswertung"><?php echo $row['nachname'].", ".$row['vorname']; ?></a></td>
					<td><?php echo $row['verein']; ?></td>
					<td><?php echo $row['jahrgang']; ?></td>
					<td><?php echo $row['geschlecht']; ?></td>
					<td><?php echo $row['klasse']; ?></td>
					<td><?php echo $row['att']; ?></td>
					<td><?php echo $row['titel']; ?></td>
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

?>