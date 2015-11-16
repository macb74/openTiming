<?php
include ("function.php");
$link = connectDB();
$term = htmlspecialchars($_GET['term'], ENT_QUOTES, 'UTF-8');
$term = trim(strip_tags($_GET['term']));
$a_json = array();

$sql = "select distinct verein from teilnehmer where verein LIKE '$term%'";
$result = dbRequest($sql, 'SELECT');

if($result[1] > 0) {
	$i = 0;
	foreach ($result[0] as $row) {
		$verein = stripslashes($row['verein']);
		$a_json[$i] = $verein;
		$i++;
	}
	
	echo json_encode($a_json);
	flush();
}
$link->close();
?>
