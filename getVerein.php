<?php
include ("function.php");
$link = connectDB();
$q = htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8');
$sql = "select distinct verein from teilnehmer where verein LIKE '$q%'";
$result = dbRequest($sql, 'SELECT');

foreach ($result[0] as $row) {
	echo $row['verein']." \n";
}
$link->close();
?>
