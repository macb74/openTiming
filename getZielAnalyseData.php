<?php
include "function.php";
$link = connectDB();

$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);

$rd = getRennenData($_GET['lID']);
$sql = "select zeit.zeit as zeit, 
	zeit.nummer as nummer 
from zeit 
where zeit > '".base64_decode($_GET['start'])."' and zeit < ADDTIME('".base64_decode($_GET['start'])."', '".base64_decode($_GET['duration'])."') and vID = ".$rd['vID'];
$result = dbRequest($sql, 'SELECT');

echo "start,end,label\n";

$i=0;
if($result[1] > 0) {
	foreach ($result[0] as $row) {
		echo $row["zeit"].",,";
		echo $row["nummer"];
		echo "\n";
		$i++;
	}
}

?>
