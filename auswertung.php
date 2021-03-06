<?php

function auswertung() {
	showRaceList();	
}

function doAuswertung() {
	$rennen = $_GET['id'];
	$anzTeilnehmer = 0;
	$anzTeams = 0;
	$veranstaltung = $_SESSION['vID'];
	$rInfo = getRennenData($rennen);
	if($rInfo['lockRace'] == 1 ) { die; }
	
	cleanAll($veranstaltung, $rennen);
	setKlasse($veranstaltung,$rennen);
	updateZeit($veranstaltung, $rennen, $rInfo);

	if($rInfo['rundenrennen'] == 1) {
	    updateAnzRunden($veranstaltung, $rennen, $rInfo);
	}

	$anzTeams = updateTeam($veranstaltung, $rennen, $rInfo);

	$anzTeilnehmer = updatePlatzierung($veranstaltung, $rennen, $rInfo);
	updateStatus($veranstaltung, $rennen);
	
	echo "{ \"message\": \"<p>Es wurden <b>$anzTeilnehmer Teilnehmer</b> ausgewertet<br>Es wurden <b>$anzTeams Teams</b> ausgewertet</p>\",
		\"finisher\" : $anzTeilnehmer }";
}

function getSeconds($s) {
	date_default_timezone_set("UTC");
	$sec = strtotime($s);
	date_default_timezone_set("Europe/Berlin");
	return $sec;
}
	
function getRealTime($startZeit, $zeit) {
	$zielSec = getSeconds($zeit);
	$startSec = getSeconds($startZeit);
	$zeit = $zielSec - $startSec;
	
	$zeit = sec2Time($zeit);
	return $zeit;
}

function sec2Time($sec){
  if(is_numeric($sec)){
    if($sec >= 3600){
      	$value["hours"] = floor($sec/3600);
      	if($value["hours"] < 10) { $value["hours"] = "0".$value["hours"]; }
      	$sec = ($sec%3600);
    } else {
    	$value["hours"] = "00";
    }
    if($sec >= 60){
      	$value["minutes"] = floor($sec/60);
      	if($value["minutes"] < 10) { $value["minutes"] = "0".$value["minutes"]; }
      	$sec = ($sec%60);
    } else {
		$value["minutes"] = "00";
    }
    $value["seconds"] = floor($sec);
    if($value["seconds"] < 10) { $value["seconds"] = "0".$value["seconds"]; }
    
    $time = $value["hours"].":".$value["minutes"].":".$value["seconds"];
    return $time;
  }
}

function cleanAll($veranstaltung, $rennen) {
	$query = "update teilnehmer set zeit='00:00:00', platz = 0, akplatz = 0, vplatz = 0, vnummer = '', mplatz = 0, vtime = '00:00:00', runden = 0 where vid = $veranstaltung and lid = $rennen";
	$result = dbRequest($query, 'UPDATE');

	$query = "update teilnehmer set manzeit='00:00:00' where useManTime = 0 and vid = $veranstaltung and lid = $rennen";
	$result = dbRequest($query, 'UPDATE');
}

function updateZeit($veranstaltung, $rennen, $rInfo) {
    global $config;

	if($rInfo['use_lID'] == 1) { $sql_lID = "and z.lid = $rennen "; } else { $sql_lID = ""; }
	
	switch($rInfo["rundenrennen"]) {
		case 1:  $zeit = "max(zeit)"; break;   # Bei Rennen auf Zeit: Ende = letzte Runde
		case 2:  $zeit = "z.zeit"; break;      # Bei Rennen auf x Runden: alle Runden, letzte zaehlt wenn gleich Vorgabe
        default: $zeit = "min(zeit)";          # Bei normalen Rennen: erster Zieldurchlauf zaehlt
	} 

        # Test auf Zeitumstellungslauf - rennen IDs muessen in Config gesetzt sein
        if(isset($config['zeitsprungLIDs'])) {
          if(array_search($rennen, $config['zeitsprungLIDs']) !== false) define('ZEITSPRUNG',true);
        }
        if(defined('ZEITSPRUNG')) $zeit="if(HOUR($zeit)>2 AND HOUR($zeit)<12,timediff($zeit,'01:00:00'),$zeit)";

    $startZeit = $rInfo['startZeit'];
	if($rInfo["rundenrennen"] != 2) {
	# ohne Rundenvorgabe oder kein Rundenrennen:

		$sql = "update teilnehmer t ".
					"inner join (select nummer, $zeit zeit, millisecond ".
					"from zeit ".
					"	where del = 0 ".
					"	and zeit > '".$startZeit."' ".
					"	and vid = $veranstaltung group by nummer) z on t.stnr = z.nummer ".
					"	set t.zeit = SEC_TO_TIME(to_seconds(z.zeit) - to_seconds('".$startZeit."')), ".
					"		t.millisecond = z.millisecond ".
					"where t.lid = $rennen;";

		$result = dbRequest($sql, 'UPDATE');

	} else {
        # Rennen auf x Runden:
		$sql = "select t.id, t.stnr as stnr, $zeit as zeit, z.millisecond ".
			"from teilnehmer as t inner join zeit as z on t.stnr = z.nummer ".
			"where t.vid = $veranstaltung and z.vid = $veranstaltung and t.lid = $rennen ".$sql_lID.
			"and z.zeit > '".$startZeit."' and z.del = 0 order by stnr, zeit asc";
		//echo $sql;
	
		$result = dbRequest($sql, 'SELECT');
	
		$validRounds=1;
		$sTime = "00:00:00";
		$oldStnr = 0;
		
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				
			    if($oldStnr == $row['stnr']) { 
					$dif = abs(getSeconds($row['zeit']) - getSeconds($sTime));
					if( $dif > 10 ) {
					    $validRounds++;
						$sTime = $row['zeit'];
					}
				} else { 
				    $validRounds=1;
				}

				if($validRounds == $rInfo['rdVorgabe'] && !isset($update[$row['stnr']])) {
				    $update[$row['stnr']] = true;
					$realTime = getRealTime($startZeit, $row['zeit']);
					$sql = "update teilnehmer set Zeit = '$realTime', millisecond = ".$row['millisecond'].", aut_runden = $validRounds where id = ".$row['id'];
					$res = dbRequest($sql, 'UPDATE');
				}
				
				$oldStnr = $row['stnr'];
			}
		}
		
		$sql = "update teilnehmer set runden = aut_runden + man_runden where vid = $veranstaltung and lid = $rennen";
		$res = dbRequest($sql, 'UPDATE');
	}

	// manuell eingetragene Zeiten in der Einlaufliste
	$sql = "select t.id, t.stnr as stnr, t.manzeit ".
			"from teilnehmer as t ".
			"where usemantime = 2 and t.vid = $veranstaltung and t.lid = $rennen ".$sql_lID.
			"group by t.stnr";

	$result = dbRequest($sql, 'SELECT');
	
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			$zielzeit = $_SESSION['vDatum']." ".$row['manzeit'];
			$realTime = getRealTime($startZeit, $zielzeit);
			$sql = "update teilnehmer set Zeit = '$realTime', millisecond = 0 where id = ".$row['id'];
			$res = dbRequest($sql, 'UPDATE');
		}
	}
	
	// manuell eingetragene Laufzeiten
	$query = "update teilnehmer set zeit=manzeit, millisecond = 0 where manzeit <> '00:00:00' and useManTime = 1 and vid = $veranstaltung and lid = $rennen";
	$result = dbRequest($query, 'UPDATE');
	
}

function updatePlatzierung($veranstaltung, $rennen, $rInfo) {

	if($rInfo["rundenrennen"] == 1) { $orderBy = "order by runden desc, zeit asc, millisecond asc"; } else { $orderBy = "order by zeit asc, millisecond asc"; }
	
	$sql = "select id, klasse, geschlecht from teilnehmer ".
	"where vid = $veranstaltung and lid = $rennen and zeit <> '00:00:00' ".
		"and klasse <> '' and disq = 0 and del = 0 ".$orderBy;
	
	$result = dbRequest($sql, 'SELECT');
	
	$m = 1;
	$w = 1;
	$x = 1;
	$kl = array();
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			$aktKl = "";
			$sql2 = "";
			$aktKl = $row['klasse'];
			if(isset($kl[$aktKl])) { $kl[$aktKl]++; } else { $kl[$aktKl] = 1; }
			
			if($row['geschlecht'] == 'M') {
				$sql2 = "update teilnehmer set platz = $m, akplatz = $kl[$aktKl] where id = $row[id]";
				$m++; 
			} elseif($row['geschlecht'] == 'W') {
				$sql2 = "update teilnehmer set platz = $w, akplatz = $kl[$aktKl] where id = $row[id]";
				$w++; 
			} else {
				$sql2 = "update teilnehmer set platz = $x, akplatz = $kl[$aktKl] where id = $row[id]";
				$x++;
			}
	
			$result2 = dbRequest($sql2, 'UPDATE');
		}
	}	
	
	return $result[1];
}

function updateTeam($veranstaltung, $rennen, $rInfo) {

    if( $rInfo['teamDeaktivated'] == 1) {
        return "0 (deaktiviert)";
    }
    
	//check if race has teamTogetherWith with another race
	$query = "select teamTogetherWith from lauf where teamTogetherWith like '%\"".$rennen."\"%' and vid = $veranstaltung";
    $result = dbRequest($query, 'SELECT');
	if($result[1] > 0) {
		return "0 (mit anderem Rennen verbunden)";
	}
	
	$rennen = checkTeamTogetherWith($rennen, $rInfo['teamTogetherWith']);
	
	if ($rInfo['teamTogetherWith'] != '' && $rInfo['teamTogetherWith'] != '[""]') {
        // cleanup old results
        $query = "update teilnehmer set vnummer = '', vtime = '00:00:00', vplatz = '0' where vid = $veranstaltung and lid in ($rennen)";
        $result = dbRequest($query, 'UPDATE');   
    }
    
    $teamAnz = $rInfo['teamAnz'];
    $teamAtt = "";
    if( $rInfo['teamAtt'] == 1) {
        $teamAtt = "and att like '".$rInfo['teamAttVal']."' ";
    }
    
    if( $rInfo['teamAtt'] == 2) {
        $teamAtt = "and att = '' ";
    }
    
	# Platz in Verein + eindeutige Vereinsnummer
	$sql = "select ID, verein, vklasse from teilnehmer ";
	$sql .= "where vid = $veranstaltung ";
	$sql .= "and lid in ($rennen)";
	$sql .= "and zeit <> '00:00:00' ";
	$sql .= "and verein <> '' ";
	$sql .= "and disq = 0 ";
	$sql .= "and del = 0 ";
	$sql .= "and vklasse <> '' ";
	$sql .= $teamAtt;
	$sql .= "order by verein, vklasse, zeit";
	
	$result = dbRequest($sql, 'SELECT');

	$v 		= '';	# Verein des vorherigen Datensatzes
	$vnr 	= 1;	# Eindeutige Mannschaftsnummer
	$alleMannschaften = array();
	$mannschaft = array();
	
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			if ($v != $row["verein"]."_".$row['vklasse']) { 
				$mannschaft = array();
			}
			
			$r = $row["ID"];
			$mannschaft[$r]["ID"] = $r;
			$mannschaft[$r]["vnr"] = $vnr;
			$mannschaft[$r]["vkl"] = $row['vklasse'];
						
			#  eine komplette Mannschaft
			if (count($mannschaft) == $teamAnz) {
				$mplatz = 1;
				foreach ($mannschaft as $m) {
					$_id 		= $m["ID"];
					$_vnr 		= $m["vnr"];
					$_vkl 		= $m["vkl"];
					$_uVnr		= $rennen."_".$_vkl."_".$_vnr;
					$q = "update teilnehmer set vnummer = '".$_uVnr."', mplatz = $mplatz where ID = $_id";
					$r = dbRequest($q, 'UPDATE');
					$mplatz++;
				}
				#print_r($mannschaft);
				$alleMannschaften[$vnr-1] = $_uVnr;
				$mannschaft = array();
				$vnr++;
			}
		
			$v = $row["verein"]."_".$row['vklasse'];
		}		
	}

	
	# Mannschaftszeiten aktualisieren
	$sql = "select verein, vnummer, SEC_TO_TIME(SUM(time_to_sec(zeit))) zeit from teilnehmer vtime where lid in ($rennen) and vnummer <> '' group by vnummer order by vtime asc";
	$res = dbRequest($sql, 'SELECT');
	
	$i = 1;
	if($res[1] > 0) {
		foreach ($res[0] as $row) {
			$sql = "update teilnehmer set vtime = '".$row['zeit']."' where vnummer = '".$row['vnummer']."'";
			$res = dbRequest($sql, 'UPDATE');
		}
		$i++;
	}

	
	

	if($alleMannschaften) {		
		# Mannschaftsplatzierungen aktualisieren
		$sql2 = "select vnummer, vtime, vklasse, SUM(runden) as runden from teilnehmer where vid = $veranstaltung and lid in ($rennen) and vtime <> '00:00:00' and vnummer <> '' group by vnummer order by runden desc, vtime, vnummer";
		$res2 = dbRequest($sql2, 'SELECT');
		$kl = array();
		
		if($res2[1] > 0) {
			foreach ($res2[0] as $row) {
				$aktKl = "";
				$sql3 = "";
				$aktKl = $row['vklasse'];
				if( isset($kl[$aktKl])) { $kl[$aktKl]++; } else { $kl[$aktKl] = 1; }
		
				$sql3 = "update teilnehmer set vplatz = $kl[$aktKl] where vnummer = '".$row['vnummer']."'";
				//echo $sql3;
				$res3 = dbRequest($sql3, 'UPDATE');
			}
		}	
		
	}
	return count($alleMannschaften);
}

function showRaceList() {
	
?>

	<script>

		$(document).ready(function(){
	
			$('[data-toggle="tooltip"]').tooltip({container: "body"});

			$('.last-race-update').mouseenter(function(data){
				var target = this;
				var rid = $( this ).attr('rid');
				var jqxhr = $.get( "ajaxRequest.php?func=getLastRaceUpdate&id=" + rid);
			
				jqxhr.done(function( data ) {
					$( target ).tooltip( {container: 'body' } )
					.attr('data-original-title', data)
					.tooltip('fixTitle')
					.tooltip('show');
				});
			});


			$(".btn").mouseup(function(){
			    $(this).blur();
			})
			
			<?php 

					if($_SESSION['rID'] != 0) {
						echo "showContent( '".$_SESSION['contentFunc']."', ".$_SESSION['rID']." );";
					}
					
					if($_SESSION['vSpecial'] != "") {
					    echo "$( '#specialReporting' ).load( 'ajaxRequest.php?func=specialReporting' );";
					}

			?>
			
		});
	
	</script>

	<h3>Auswertung</h3>
	
	<div class="table-responsive">
		<table class="table table-striped table-vcenter">
			<thead>
				<tr>
					<th>ID</th>
					<th>Titel</th>
					<th>Start</th>
					<th>Laufwertung</th>
					<th>Startlisten</th>
					<th>Ergebnisse</th>
					<th>Urkunden</th>
				</tr>
			</thead>
		<tbody>
	
<?php	
	
	$veranstaltung = $_SESSION['vID'];
	$sql = "select * from lauf where vID = $veranstaltung order by start asc, titel;";
	$result = dbRequest($sql, 'SELECT');

	if($result[1] > 0) {
		foreach ($result[0] as $row) {

			$count = getCountRunner($row['ID']);
			
?>

				<tr>
					<td><?php echo $row['ID']; ?></td>
					<td><?php echo $row['titel']; ?> <small><?php echo $row['untertitel']; ?> (<?php echo $count[0]; ?> / <span id="finisher-<?php echo $row['ID']; ?>"><?php echo $count[1] ?></span>)</small></td>
					<td><?php echo substr($row['start'], 10); ?></td>
					<td>
						<div class="btn-group" role="group" aria-label="...">
							<?php if($row['lockRace'] == 1) { $disabled = "disabled=\"disabled\""; } else { $disabled = ""; } ?>
							<?php if($row['lockRace'] != 1) { $onclick = "onclick=\"javascript:doAuswertung(".$row['ID'].")\""; } else { $onclick = ""; } ?>
							<a rid="<?php echo $row['ID']; ?>" class="btn btn-default btn-small-border last-race-update" <?php echo $disabled; ?> <?php echo $onclick; ?>>
								<!--<i class="fa fa-cog"></i> --><i class="fa fa-clock-o"></i> Laufwertung
							</a>
						</div>
					</td>
					<td>
						<div class="btn-group" role="group" aria-label="...">
							<a class="btn btn-default btn-small-border" data-toggle="tooltip" title="Bildschirmliste" onclick="javascript:showContent('showStartliste', <?php echo $row['ID']; ?>)">
								 <i class="fa"></i> <i class="fa fa-list"></i>
							</a>
							<a class="btn btn-default btn-small-border" data-toggle="tooltip" title="PDF nach Name sortiert" href="exportPDF.php?action=startliste&sort=nachname&id=<?php echo $row['ID']; ?>" target="_new">
								<i class="fa fa-file-pdf-o"></i> <i class="fa fa-sort-alpha-asc"></i>
							</a>
							<a class="btn btn-default btn-small-border" data-toggle="tooltip" title="PDF nach Startnummer sortiert" href="exportPDF.php?action=startliste&sort=stnr&id=<?php echo $row['ID']; ?>" target="_new">
								<i class="fa fa-file-pdf-o"></i> <i class="fa fa-sort-numeric-asc"></i>
							</a>
						</div>
					</td>
					<td>
						<div class="btn-group" role="group" aria-label="...">
							<a class="btn btn-default btn-small-border" data-toggle="tooltip" title="Bildschirmliste" onclick="javascript:showContent('showErgebnisse', <?php echo $row['ID']; ?>)">
								<i class="fa fa-male"></i> <i class="fa fa-list"></i>
							</a>
							<a class="btn btn-default btn-small-border" data-toggle="tooltip" title="PDF Gesammt" href="exportPDF.php?action=ergebnisGesamt&id=<?php echo $row['ID']; ?>" target="_new">
								<i class="fa fa-male"></i> <i class="fa fa-file-pdf-o"></i>
							</a>
							<a class="btn btn-default btn-small-border" data-toggle="tooltip" title="PDF nach Klassen" href="exportPDF.php?action=ergebnisKlasse&id=<?php echo $row['ID']; ?>" target="_new">
								<i class="fa fa-male"></i><i class="fa fa-female"></i> <i class="fa fa-file-pdf-o"></i>
							</a>
						</div>
						<div class="btn-group" role="group" aria-label="...">
							<a class="btn btn-default btn-small-border" data-toggle="tooltip" title="Ergebnisse Mannschaft" onclick="javascript:showContent('showErgebnisseM', <?php echo $row['ID']; ?>)">
								<i class="fa fa-users"></i> <i class="fa fa-list"></i>
							</a>
							<a class="btn btn-default btn-small-border" data-toggle="tooltip" title="PDF Ergebnisse Mannschaft" href="exportPDF.php?action=ergebninsMannschaft&id=<?php echo $row['ID']; ?>" target="_new">
								<i class="fa fa-users"></i> <i class="fa fa-file-pdf-o"></i>
							</a>
						</div>
						<?php if($row['rundenrennen'] == 1) {?>
						<div class="btn-group" role="group" aria-label="...">
							<a class="btn btn-default btn-small-border" data-toggle="tooltip" title="PDF Rundenzeiten" href="exportRundenzeiten.php?&id=<?php echo $row['ID']; ?>" target="_new">
								<i class="fa fa-repeat"></i> <i class="fa fa-file-pdf-o"></i>
							</a>
						</div>
						<?php } ?>
					</td>
					<td>
						<div class="btn-group" role="group" aria-label="...">
							<a class="btn btn-default btn-small-border" data-toggle="tooltip" title="Gesammtwertung" href="urkundenPDF.php?action=gesamt&id=<?php echo $row['ID']; ?>" target="_new">
								<i class="fa"></i> <i class="fa fa-user"></i>
							</a>
							<a class="btn btn-default btn-small-border" data-toggle="tooltip" title="Klassenwertung" href="urkundenPDF.php?action=klasse&id=<?php echo $row['ID']; ?>" target="_new">
								<i class="fa"></i> <i class="fa fa-user-times"></i>
							</a>
							<a class="btn btn-default btn-small-border" data-toggle="tooltip" title="Mannschaftswertung" href="urkundenPDF.php?action=team&id=<?php echo $row['ID']; ?>" target="_new">
								<i class="fa"></i> <i class="fa fa-users"></i>
							</a>
						</div>
						<div class="btn-group" role="group">
							<a class="btn btn-default btn-small-border dropdown-toggle" id="num-of-results-<?php echo $row['ID']; ?>" data-toggle="dropdown" aria-haspopup="true" id="selectUrkundeResult" aria-expanded="false">

<?php 

if(isset($_SESSION['anzUrkunden-'.$row['ID']])) { 
	echo $_SESSION['anzUrkunden-'.$row['ID']];
	if($_SESSION['anzUrkunden-'.$row['ID']] != 'ALL') {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	}
} else {
	$_SESSION['anzUrkunden-'.$row['ID']] = 3;
	echo "3&nbsp;&nbsp;&nbsp;&nbsp;";
} 

?>

								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li><a onclick="javascript:selectUrkundeResult(3, <?php echo $row['ID']; ?>);">3</a></li>
								<li><a onclick="javascript:selectUrkundeResult(6, <?php echo $row['ID']; ?>);">6</a></li>
								<li><a onclick="javascript:selectUrkundeResult('ALL', <?php echo $row['ID']; ?>);">ALL</a></li>
							</ul>
						</div>
					</td>
				</tr>
	
<?php
	
		}
	}
	
?>
			</tbody>
		</table>
	</div>
	<div id="specialReporting"></div>
<?php 

}

function showWithowtTime($rennen) {
	
	$html = "<br>";
	$html = "<p><a href=\"#\" onClick=\"clearDiv()\">clear</a></p>";
	$sql = "SELECT t.*, l.titel FROM `teilnehmer` as t INNER JOIN lauf as l ON t.lID = l.ID ".
		"where t.vID = ".$_SESSION['vID']." ".
			"and t.lid = $rennen and del= 0 and disq = 0 and zeit = '00:00:00' ".
			"order by nachname asc;";
	$result = dbRequest($sql, 'SELECT');

	$html2 = "";
	$i=1;
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			if($i%2 == 0) { $html2 .= "<tr class=\"even\">\n"; } else { $html2 .= "<tr class=\"odd\">\n"; }
			$html2 .= "<td align=\"left\">".$row['stnr']."</td>\n";
			$html2 .= "<td align=\"left\"><a href=\"".$_SERVER["SCRIPT_NAME"]."?func=teilnehmer.edit&ID=".$row['ID']."&nextUrl=".base64_encode($_SERVER["SCRIPT_NAME"]."?func=auswertung")."\">".$row['nachname'].", ".$row['vorname']."</a></td>\n";		
			$html2 .= "<td align=\"left\">".$row['verein']."</td>\n";
			$html2 .= "<td align=\"left\">".$row['jahrgang']."</td>\n";
			$html2 .= "<td align=\"left\">".$row['geschlecht']."</td>\n";
			$html2 .= "<td align=\"left\">".$row['klasse']."</td>\n";
			$html2 .= "<td align=\"left\">".$row['titel']."</td>\n";
			$html2 .= "<td align=\"left\">".$row['zeit']."</td>\n";
	
			$html2 .= "</tr>\n";
			$i++;
		}
	}

	$columns = array('Stnr', 'Name', 'Verein', 'JG', 'G', 'Klasse', 'Rennen', 'Zeit');
	$html .= tableList($columns, $html2, "common");
	
	return $html;
}

function updateAnzRunden($veranstaltung, $rennen, $rInfo) {
	$numbers = array();
	$startZeit = $rInfo['startZeit'];
	
	// um mit mehreren Readern arbeiten zu können werden immer nur Runden gezählt, die länger als 10 sec. dauern.
	// alles was kleiner als 10 sec ist, wird als zeit vom backup Reader interpretiert und nicht gezählt.
	
	if($rInfo['use_lID'] == 1) { $sql_lID = "and lid = $rennen "; } else { $sql_lID = ""; }
	
	$sql = "select nummer from zeit where vid = $veranstaltung $sql_lID and zeit > '".$startZeit."' and del = 0 group by nummer";
	
	$result = dbRequest($sql, 'SELECT');
	
	$i = 0;
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			$numbers[$i] = $row['nummer'];
			$i++;
		}
	}
	
	foreach ($numbers as $number) {
		$result = "";
		$sql = "";
		$re2 = "";
		$sql2 = "";
		
		$sql = "select nummer, zeit from zeit where vid = $veranstaltung $sql_lID and zeit > '".$startZeit."' and nummer = $number and del = 0 order by zeit";
				
		$result = dbRequest($sql, 'SELECT');
		
		$rowCount = 0;
		$sTime = "00:00:00";
		if($result[1] > 0) {
			foreach ($result[0] as $row) {
				$dif = abs(getSeconds($row['zeit']) - getSeconds($sTime));
				
				if( $dif > 10 ) {
					$rowCount++;
					$sTime = $row['zeit'];
				}
			}
		}

		$sql2 = "update teilnehmer set aut_runden = ".$rowCount." where stnr = ".$number." and vid = $veranstaltung and lid = $rennen";
		$res2 = dbRequest($sql2, 'UPDATE');		
	}

	$sql = "update teilnehmer set runden = aut_runden + man_runden where vid = $veranstaltung and lid = $rennen";
	$res = dbRequest($sql, 'UPDATE');
}

function updateStatus($veranstaltung, $rennen) {
	$timestamp = date("YmdHis", time());
	$sql = "update lauf set aktualisierung = $timestamp where vid = $veranstaltung and id = $rennen";
	$res = dbRequest($sql, 'UPDATE');
}

function getCountRunner($race) {

	$sql = "select id from teilnehmer where lid = $race and disq = 0 and del = 0;";
	$res = dbRequest($sql, 'SELECT');
	$count[0] = $res[1];
	
	$sql = "select id from teilnehmer where lid = $race and disq = 0 and del = 0 and zeit <> '00:00:00';";
	$res = dbRequest($sql, 'SELECT');
	$count[1] = $res[1];
	
	return $count;
}

function setKlasse($veranstaltung, $rennen) {
	
	$klassen = getKlasseDataAuswertung($rennen);
	
	$sql = "select id, lid, geschlecht, jahrgang from teilnehmer ".
			"where vid = $veranstaltung and lid = $rennen and disq = 0 and del = 0";
	
	$result = dbRequest($sql, 'SELECT');
	
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			$klasse = getKlasseAuswertung($row['jahrgang'], $row['geschlecht'], $klassen, 0);
			$sql = "update teilnehmer set klasse = '$klasse[0]', vklasse = '$klasse[1]' where id = ".$row['id'];
			$res = dbRequest($sql, 'UPDATE');
		}
	}
}

function getKlasseAuswertung($jg, $sex, $klassen, $ajax) {
	
	$teilnehmerKlasse[0] = "";
	$teilnehmerKlasse[1] = "";
	
	$jahr = substr($_SESSION['vDatum'], 0, 4);
	$alter = $jahr - $jg;
	
	foreach ($klassen[0] as $klasse) {
		if( isset($klasse[$sex])) {
			if( $alter >= $klasse[$sex]['von'] && $alter <= $klasse[$sex]['bis'] ) {
				$teilnehmerKlasse[0] = $klasse[$sex]['name'];
			}
		}
	}
	
	foreach ($klassen[1] as $klasse) {
		if( isset($klasse[$sex])) {
			if( $alter >= $klasse[$sex]['von'] && $alter <= $klasse[$sex]['bis'] ) {
				$teilnehmerKlasse[1] = $klasse[$sex]['name'];
			}
		}
	}
	
	if ($ajax == 1) {
		echo $teilnehmerKlasse[0].";".$teilnehmerKlasse[1];
	} else {
		return $teilnehmerKlasse;
	}
}

function getKlasseDataAuswertung($rennen) {
	
	if ($rennen == "X") {
		return $klasse;
	}
	
	// Klasse der Einzelwertung
	$sql = "SELECT kd.* FROM `klasse_data` kd left join lauf l on l.klasse = kd.kID where l.id = $rennen";
	$result = dbRequest($sql, 'SELECT');
	
	$i = 0;
	if ($result[1] > 0) {
		foreach ($result[0] as $row) {
			$klasse[0][$i][$row['geschlecht']]['von'] = $row['altervon'];
			$klasse[0][$i][$row['geschlecht']]['bis'] = $row['alterbis'];
			$klasse[0][$i][$row['geschlecht']]['name'] = $row['name'];
			$i++;
		}
	}
	
	// Klasse der Vereins- / Teamwertung
	$sql = "SELECT kd.* FROM `klasse_data` kd left join lauf l on l.vklasse = kd.kID where l.id = $rennen";
	$result = dbRequest($sql, 'SELECT');
	
	if ($result[1] > 0) {
		foreach ($result[0] as $row) {
			$klasse[1][$i][$row['geschlecht']]['von'] = $row['altervon'];
			$klasse[1][$i][$row['geschlecht']]['bis'] = $row['alterbis'];
			$klasse[1][$i][$row['geschlecht']]['name'] = $row['name'];
			$i++;
		}
	}
	
	return $klasse;
}

function initKlassen() {
	
	$klasse[0][0]['M']['von'] = -1;
	$klasse[0][0]['M']['bis'] = -1;
	$klasse[0][0]['M']['name'] = "";
	
	$klasse[0][0]['W']['von'] = -1;
	$klasse[0][0]['W']['bis'] = -1;
	$klasse[0][0]['W']['name'] = "";
	
	$klasse[0][0]['X']['von'] = -1;
	$klasse[0][0]['X']['bis'] = -1;
	$klasse[0][0]['X']['name'] = "";
	
	$klasse[0][0]['-']['von'] = -1;
	$klasse[0][0]['-']['bis'] = -1;
	$klasse[0][0]['-']['name'] = "";
	
	$klasse[1][0]['M']['von'] = -1;
	$klasse[1][0]['M']['bis'] = -1;
	$klasse[1][0]['M']['name'] = "";
	
	$klasse[1][0]['W']['von'] = -1;
	$klasse[1][0]['W']['bis'] = -1;
	$klasse[1][0]['W']['name'] = "";
	
	$klasse[1][0]['X']['von'] = -1;
	$klasse[1][0]['X']['bis'] = -1;
	$klasse[1][0]['X']['name'] = "";
	
	$klasse[1][0]['-']['von'] = -1;
	$klasse[1][0]['-']['bis'] = -1;
	$klasse[1][0]['-']['name'] = "";
	
}