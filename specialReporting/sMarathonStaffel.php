<?php


if(isset($_GET['ajaxFunc'])) {
    session_start();
    include "../function.php";
    $link = connectDB();
    $_GET = filterParameters($_GET);
    $_POST = filterParameters($_POST);
    
    if($_GET['ajaxFunc'] == 'getLastRaceUpdate')	{ sMarathonStaffel_getLastRaceUpdate(); }
    if($_GET['ajaxFunc'] == 'doAuswertung')		    { sMarathonStaffel_doAuswertung(); }
    if($_GET['ajaxFunc'] == 'showErgebnis')		    { sMarathonStaffel_showErgebnis(); }
}


function sMarathonStaffel() {
    sMarathonStaffel_showRaceList();
}

function sMarathonStaffel_doAuswertung() {
    $anzTeams = 0;
    $veranstaltung = $_SESSION['vID'];
    sMarathonStaffel_cleanAll($veranstaltung, null);
    $anzTeams = sMarathonStaffel_Teamwertung($veranstaltung, 3);
    echo "{ \"message\": \"<p>Es wurden <b>$anzTeams Teams</b> ausgewertet</p>\",
		\"finisher\" : 0 }";
}


function sMarathonStaffel_Teamwertung($veranstaltung, $teamAnz) {
    
    # Platz in Verein + eindeutige Vereinsnummer
    $sql = "select ID, verein, vklasse, zeit, att from teilnehmer ";
    $sql .= "where vid = $veranstaltung ";
    $sql .= "and zeit <> '00:00:00' ";
    $sql .= "and verein <> '' ";
    $sql .= "and disq = 0 ";
    $sql .= "and del = 0 ";
    $sql .= "and vklasse <> '' ";
    $sql .= "and att like 'M%' ";
    $sql .= "order by att, verein, vklasse, zeit";
    
    $result = dbRequest($sql, 'SELECT');
    
    $v 		     = '';	# Verein des vorherigen Datensatzes
    $mZeitSec    = 0;
    $count       = 0;
    
    if($result[1] > 0) {
        foreach ($result[0] as $row) {
            
            if ($v != $row["att"]) {
                $zeit = "";
            }
            
            $name             = $row['verein'];
            $att              = $row['att'];
            $zeit[$row["ID"]] = $row['zeit'];
            
            #  eine komplette Mannschaft
            if (count($zeit) == $teamAnz) {
                $count++;
                
                foreach ($zeit as $z) {
                    $mZeitSec = $mZeitSec + getSeconds('1970-01-01 '.$z);
                }
                
                $mzeit = sec2Time($mZeitSec);
                
                $q = "insert into specialReporting (vid, uid, zeit) VALUES ($veranstaltung, '".$att."', '".$mzeit."')";
                $r = dbRequest($q, 'INSERT');
                
                $mZeitSec = 0;
                $zeit = "";
            }
            
            $v = $row["att"];
            
        }
    }
    return $count;
}

function sMarathonStaffel_showRaceList() {
    
    ?>

	<script>

		$(document).ready(function(){

			$('[data-toggle="tooltip_sMarathonStaffel"]').tooltip({container: "body"});
			
			$('.sMarathonStaffel_last-race-update').mouseenter(function(data){
				var target = this;
				var rid = $( this ).attr('rid');
				var jqxhr = $.get( "specialReporting/sMarathonStaffel.php?ajaxFunc=getLastRaceUpdate" );
			
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
						echo "showContent( '".$_SESSION['contentFunc']."', ".$_SESSION['rID']." )";
					}

			?>
			
		});

		function sMarathonStaffel_doAuswertung() {
			$('#modal').modal();
			
			var jqxhr = $.getJSON( "specialReporting/sMarathonStaffel.php?ajaxFunc=doAuswertung");
			jqxhr.success(function( data ) {
				//console.log(data);
				 
				$( '#modal-body' ).html( data.message );
			});
		}


		function sMarathonStaffel_showErgebnis() {
			$( '.content-table' ).load("specialReporting/sMarathonStaffel.php?ajaxFunc=showErgebnis");
		}
	
	</script>

	<h3>Marathon Staffel</h3>
	
	<div class="table-responsive">
		<table class="table table-striped table-vcenter">
			<thead>
				<tr>
					<th>ID</th>
					<th>Titel</th>
					<th>Laufwertung</th>
					<th>Ergebnisse</th>
					<th>Urkunden</th>
				</tr>
			</thead>
		<tbody>
	
    		<tr>
    			<td>--</td>
    			<td>Marathon Staffel</td>
    			<td>
    				<div class="btn-group" role="group" aria-label="...">
    
    					<a class="btn btn-default btn-small-border sMarathonStaffel_last-race-update" onclick="javascript:sMarathonStaffel_doAuswertung()">
    						<!--<i class="fa fa-cog"></i> --><i class="fa fa-clock-o"></i> Laufwertung
    					</a>
    				</div>
    			</td>
    			<td>
    				<div class="btn-group" role="group" aria-label="...">
    					<a class="btn btn-default btn-small-border" data-toggle="tooltip_sMarathonStaffel" title="Ergebnisse Mannschaft" onclick="javascript:sMarathonStaffel_showErgebnis()">
    						<i class="fa fa-users"></i> <i class="fa fa-list"></i>
    					</a>
    					<a class="btn btn-default btn-small-border" data-toggle="tooltip_sMarathonStaffel" title="PDF Ergebnisse Mannschaft" href="specialReporting/sMarathonStaffel_exportPDF.php" target="_new">
    						<i class="fa fa-users"></i> <i class="fa fa-file-pdf-o"></i>
    					</a>
    				</div>
    			</td>
    			<td>
    				<div class="btn-group" role="group" aria-label="...">
    					<a class="btn btn-default btn-small-border" data-toggle="tooltip_sMarathonStaffel" title="Mannschaftswertung" href="specialReporting/sMarathonStaffel_urkundenPDF.php" target="_new">
    						<i class="fa"></i> <i class="fa fa-users"></i>
    					</a>
    				</div>
    			</td>
    		</tr>
	
		</tbody>
		</table>
	</div>
	
<?php 
}

function sMarathonStaffel_showErgebnis() {
        
    ?>
	<h3 class="sub-header">Ergebnisliste <small>Marathon Staffel</small>&nbsp;&nbsp;<a onclick="javascript:clearContent(); return false;" class="btn btn-default"><i class="fa fa-eraser"></i></a></h3>
		<div class="table-responsive">
			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th>Rng.</th>
						<th>Team</th>
						<th>Zeit</th>
						<th>Mitglieder</th>
					</tr>
				</thead>
				<tbody>
		
<?php 	
	
	
	$sql = "SELECT t.verein, t.nachname, t.vorname, t.verein, t.zeit as zeit, sr.zeit as tzeit, t.att from specialReporting sr " .
	           "LEFT JOIN teilnehmer t on sr.uid = t.att " .
               "WHERE t.vID = ".$_SESSION['vID']." AND sr.vid = ".$_SESSION['vID']." ".
               "ORDER BY sr.zeit, t.zeit;";

	$result = dbRequest($sql, 'SELECT');
		
	$html2            = "";
	$teamCount        = 0;
	$runnerCount      = 0;
	$att              = "";
	$oldAtt           = "";
	
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			$att = $row['att'];
            if($att != $oldAtt) {
                $runnerCount = 0;
                $teamCount++;
                
                $team[$teamCount]['verein']                        = $row['verein'];
                $team[$teamCount]['tzeit']                         = $row['tzeit'];
                $team[$teamCount]['runner'][$runnerCount]['name']  = $row['nachname'];
                $team[$teamCount]['runner'][$runnerCount]['vname'] = $row['vorname'];
                $team[$teamCount]['runner'][$runnerCount]['zeit']  = $row['zeit'];
            } else {
                $runnerCount++;
                $team[$teamCount]['runner'][$runnerCount]['name']  = $row['nachname'];
                $team[$teamCount]['runner'][$runnerCount]['vname'] = $row['vorname'];
                $team[$teamCount]['runner'][$runnerCount]['zeit']  = $row['zeit'];
            }
            $oldAtt = $att;
		}
    }

    $i = 1;
    foreach ($team as $t) {    
    
?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $t['verein']; ?></td>
					<td><?php echo $t['tzeit']; ?></td>
					<td>
						<table border='0' cellspacing='0' >
				
<?php
					foreach ($t['runner'] as $runner) {
						echo "<tr><td width='200'>".$runner['name'].", ".$runner['vname']."</td><td>".$runner['zeit']."</td></tr>";
					}
?>
		
						</table>
					</td>
				</tr>
<?php
        $i++;
	}
?>

			</tbody>
		</table>
	</div>

<?php 
}


function sMarathonStaffel_cleanAll($veranstaltung, $rennen) {
    $query = "delete from specialReporting where vid = $veranstaltung";
    $result = dbRequest($query, 'DELETE');
}

function sMarathonStaffel_getLastRaceUpdate() {
    $sql = "select timestamp from specialReporting where vid = ".$_SESSION['vID'];
    $result = dbRequest($sql, 'SELECT');
    $a = "-";
    
    if($result[1] > 0) {
        foreach ($result[0] as $row) {
            $a = stripslashes($row['timestamp']);
        }
    }
    echo $a;
}
?>
