<?php
include ("function.php");
$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);
$link = connectDB();
$rData = getRennenData($_GET['id']);
mysql_close($link);
echo $rData['rundenrennen'];
?>
