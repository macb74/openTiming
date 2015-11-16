<?php
include "../function.php";
$link = connectDB();
$sql = "select min(zeit.zeit) as zeit, 
	zeit.nummer as nummer, 
    t.nachname as nachname, 
    t.vorname as vorname, 
    t.verein as verein, 
    t.att,
    l.start as start
from zeit 
inner join teilnehmer as t on t.stnr = zeit.nummer and t.vID = zeit.vID 
inner join lauf as l on t.lid = l.ID
where zeit.zeit > l.start
group by t.id 
order by min(zeit.id) desc 
LIMIT 20;";
$result = dbRequest($sql, 'SELECT');

$i=0;
if($result[1] > 0) {
	foreach ($result[0] as $row) {
		$laufzeit = getRealTime($row['start'], $row['zeit']);
?>

<tr class="<?php if($i%2 == 0) { echo "odd"; } else { echo "even"; } ?>">
<td width="30" align"left"><?php echo $row["nummer"] ?></td>
<td align"left"><?php echo $row["nachname"].", ".$row["vorname"] ?></td>
<td align"left"><?php echo $row["verein"] ?></td>
<td align"left"><?php echo $row["att"] ?></td>
<td align"left"><?php echo $laufzeit ?></td>
</tr>

<?php
	$i++;
	}
}

?>
