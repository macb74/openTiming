<?php
/*
 * Created on 06.11.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

session_start();
include "function.php";

?>

<html>
<head>

<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache; no-store; max-age=0" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="description" content="openTiming SportsTiming" />
<meta http-equiv="Content-Language" content="de" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta name="decorator" content="main" />

<title>openTiming</title>

<link href="css/smart.css" rel="stylesheet" type="text/css" />
<link href="css/smart-tables.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<link href="css/jquery.autocomplete.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>

<script>
<!--
	var parselimit=5

	function beginrefresh(){	
		if (parselimit==1)
			window.location.reload()
		else{ 
			parselimit-=1
			setTimeout("beginrefresh()",1000)
		}
	}

window.onload=beginrefresh
//-->
</script>


</head>

<body>

<table class="timeTable" width="150" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td height="118">
			<table id="race" class="common meetings" rules="rows" frame="void">
				<tr class="odd">
					<td><b>StNr</b></td>
					<td><b>Zeit</b></td>
				</tr>
<?php

					$link = connectDB();
			
					$sql = "select * from zeit where vID = ".$_SESSION['vID']." order by ID desc LIMIT 0 , 20";
					$result = mysql_query($sql);
					if (!$result) {
						die('Invalid query: ' . mysql_error());
					}
			
					$i = 0;
					while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			
						if($i%2 == 0) { echo "<tr class=\"even\">\n"; } else { echo "<tr class=\"odd\">\n"; }
						echo "	<td>".$row['nummer']."</td>\n";
						echo "	<td>".$row['zeit']."</td>\n";
						echo "</tr>\n";
						$i++;
					}
			
					mysql_close($link);

?>		
			</table>
		</td>
	</tr>
</table>

</body>
</html>

<?php
#phpinfo();
?>