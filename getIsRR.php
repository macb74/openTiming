<?php
include ("function.php");
$link=connectDB();
$_GET = filterParameters($_GET);
$_POST = filterParameters($_POST);
$rData = getRennenData($_GET['id']);
echo $rData['rundenrennen'];
$link->close();
?>
