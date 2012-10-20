<?php
include ("function.php");
	$link = connectDB();
	$q = htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8');
	$sql = "select distinct verein from teilnehmer where verein LIKE '$q%'";
	$result = mysql_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		echo htmlspecialchars_decode($row['verein'], ENT_QUOTES)." \n";
	}

	mysql_close($link);
?>