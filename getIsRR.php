<?php
include ("function.php");
$link = connectDB();
$rData = getRennenData($_GET['id']);
mysql_close($link);
echo $rData['rundenrennen'];
?>