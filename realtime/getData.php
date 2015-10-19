<?php
include "config.php";
$link = connectDB();
$sql = "select distinct zeit.nummer as nummer, teilnehmer.nachname as nachname, teilnehmer.vorname as vorname, teilnehmer.verein as verein, teilnehmer.zeit as zeit, teilnehmer.att from zeit inner join teilnehmer on zeit.nummer = teilnehmer.stnr where zeit.vid = teilnehmer.vid order by zeit.id desc LIMIT 20";
$result = dbRequest($sql, 'SELECT');

$i=0;
if($result[1] > 0) {
	foreach ($result[0] as $row) {
?>

<tr class="<?php if($i%2 == 0) { echo "odd"; } else { echo "even"; } ?>">
<td width="30" align"left"><?php echo $row["nummer"] ?></td>
<td align"left"><?php echo $row["nachname"].", ".$row["vorname"] ?></td>
<td align"left"><?php echo $row["verein"] ?></td>
<td align"left"><?php echo $row["att"] ?></td>
<td align"left"><?php echo $row["zeit"] ?></td>
<td align"left"><?php ?></td>
</tr>

<?php
	$i++;
	}
}

function connectDB() {
	global $config;
	
	$link = new mysqli($config['dbhost'], $config['dbuser'], $config['dbpassword'], $config['dbname']);
	if ($link->connect_errno) {
		printf("Connect failed: %s\n", $link->connect_error);
		exit();
	}
	
	if (!$link->query("SET NAMES 'utf8'")) {
        printf("Error: %s\n", $link->error);
    }
	
	if (!$link->query("SET CHARACTER SET 'utf8'")) {
        printf("Error: %s\n", $link->error);
    }
	
	return $link;
}

function dbRequest($sql, $action) {
	/*
	 * 0 = Ergebnis des Select Statement - true/false bei INSERT, UPDATE, DELETE
	 * 1 = Anzahl der Zeilen bei SELECT
	 * 2 = Fehlermeldung
	 * 3 = ID des Datensatz bei INSERT
	 */
	
	global $link;
	if(!$link) { echo "keine DB Verbindung"; }
	
	$result[0] = false;
	$result[1] = false;
	$result[2] = false;
	$result[3] = false;
	
	//echo htmlspecialchars($sql)."<br>";
	
	$res = $link->query($sql);
	if ($link->error) {
		$result[2] = $link->error;
		//echo htmlspecialchars($link->error)."<br>";
	}

	
	if($res && $action == 'SELECT') {
		$result[1] = $res->num_rows;
		$i = 0;
		while ($row = $res->fetch_assoc()) {
			foreach($row as $key => $value) {
				$result[0][$i][$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
			}
			$i++;
		}
		$res->close();
	}
	
	if($action == 'INSERT') {
		$result[3] = $link->insert_id;
	}
	
	if(!$link->error && $action != 'SELECT') {
		$result[0] = true;
	}

	//$link->close();
	return $result;
}


?>
