<?php

function ziel() {
	global $func;
	$html="";

	if( isset($func[1])) {
		if( $func[1] == 'analyse' ) {
			zielAnalyseHeader();
		}
		if( $func[1] == 'edit' ) {
			zielEditForm();
		}
	}
}

function zielAnalyseHeader() {

	# Display Rennen
	$veranstaltung = $_SESSION['vID'];
	$sql = "select * from lauf where vID = $veranstaltung order by start asc, titel;";
	$result = dbRequest($sql, 'SELECT');

	?>
	<h3>Zieleinlauf Analyse</h3>
	
	<div class="table-responsive">
		<table class="table table-striped table-vcenter">
			<thead>
				<tr>
					<th>ID</th>
					<th>Titel</th>
					<th>Untertitel</th>
					<th>Start</th>
					<th></th>
				</tr>
			</thead>
		<tbody>
			
	<?php
	
	
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			$sql = "select count(ID) as anz from teilnehmer where del = 0 and vID = $veranstaltung and lID = ".$row['ID'];
			$resultCount = dbRequest($sql, 'SELECT');
			foreach ($resultCount[0] as $rowCount) {
				$anzTeilnehmer = $rowCount['anz'];
			}
				
			$subtitle = "";
			if ($row['untertitel'] != "") { $subtitle = "<i>- ".$row['untertitel']."</i>"; }
			
?>
			<tr>
				<td><?php echo $row['ID']; ?></td>
				<td><?php echo $row['titel']." ".$subtitle." (".$anzTeilnehmer.")"; ?></td>
				<td><?php echo $row['untertitel']; ?></td>
				<td><?php echo $row['start']; ?></td>
				<td>
					<div class="form-inline">
						<span>Start: </span>
						<input class="form-control input-sm input-very-small" id='startAnalyseTime_<?php echo $row['ID']; ?>' value='<?php echo $row['start'] ?>'>
						<span>Dauer: </span>
						<input class="form-control input-sm input-very-small" id='duration_<?php echo $row['ID']; ?>' value='01:00:00'>
						<a id="<?php echo $row['ID']; ?>" class="zielanalyse" href="#" onclick="javascript:showZielzeitAnalyse(this); return false" >start Analyse</a>
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

<?php
}

function showZielAnalyse() {
	
	$rennen = $_GET['id'];
	$start = $_GET['start'];
	$duration = $_GET['duration'];
	
	$sourceFile = 'getZielAnalyseData.php?lID='.$rennen.'&start='.$start.'&duration='.$duration;

?>
    <link href="css/timeline.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="js/d3.v3.min.js"></script>
    <script type="text/javascript" src="js/timeline.js"></script>
    
	<div id="timeline"></div>

<script>

    /*  You need a domElement, a sourceFile and a timeline.

        The domElement will contain your timeline.
        Use the CSS convention for identifying elements,
        i.e. "div", "p", ".className", or "#id".

        The sourceFile will contain your data.
        If you prefer, you can also use tsv, xml, or json files
        and the corresponding d3 functions for your data.


        A timeline can have the following components:

        .band(bandName, sizeFactor
            bandName - string; the name of the band for references
            sizeFactor - percentage; height of the band relation to the total height
            Defines an area for timeline items.
            A timeline must have at least one band.
            Two bands are necessary, to change the selected time interval.
            Three and Bands are allowed.

        .xAxis(bandName)
            bandName - string; the name of the band the xAxis will be attached to
            Defines an xAxis for a band to show the range of the band.
            This is optional, but highly recommended.

        .labels(bandName)
            bandName - string; the name of the band the labels will be attached to
            Shows the start, length and end of the range of the band.
            This is optional.

        .tooltips(bandName)
            bandName - string; the name of the band the labels will be attached to
            Shows current start, length, and end of the selected interval of the band.
            This is optional.

        .brush(parentBand, targetBands]
            parentBand - string; the band that the brush will be attached to
            targetBands - array; the bands that are controlled by the brush
            Controls the time interval of the targetBand.
            Required, if you want to control/change the selected time interval
            of one of the other bands.

        .redraw()
            Shows the initial view of the timeline.
            This is required.

        To make yourself familiar with these components try to
        - comment out components and see what happens.
        - change the size factors (second arguments) of the bands.
        - rearrange the definitions of the components.
    */

    

    // Define domElement and sourceFile
    var domElement = "#timeline";
    var sourceFile = "<?php echo $sourceFile; ?>";

    // Read in the data and construct the timeline
    d3.csv(sourceFile, function(dataset) {

        timeline(domElement)
            .data(dataset)
            .band("mainBand", 0.6)
            .band("naviBand", 0.20)
            .xAxis("mainBand")
            .tooltips("mainBand")
            .xAxis("naviBand")
            .labels("mainBand")
            .labels("naviBand")
            .brush("naviBand", ["mainBand"])
    		.setColor()
            .redraw();

    });

</script>

<?php 
}

function zielEditForm() {
		
	$sql = "SELECT * FROM zeit where vID = ".$_SESSION['vID']." order by zeit asc";
	$result = dbRequest($sql, 'SELECT');
	
?>
	<script>
		$(document).ready(function(){
			$("#submit").click(function(event){
			    event.preventDefault();
			    submitForm( '#saveManReaderTime', 'index.php?func=ziel.edit' );
			});
		});
		
	</script>

	<div class="container-fluid">
		<div class="row">
			<div class="alert alert-danger hidden col-sm-offset-3 col-sm-6" id="alert" role="alert"></div>
		</div>
		<div class="row">
			<form role="form" class="form-horizontal" id="saveManReaderTime" name="saveManReaderTime">
				<input type="hidden" name="form" value="saveManReaderTime">
			
				<div class="form-group"> 
					<label class="col-sm-offset-2 col-sm-1 control-label" for="lid">LID:</label>
					<div class="col-sm-1">
		    			<input class="form-control" type="text" name='lid' id="lid" placeholder="">
		    		</div>
					<label class="col-sm-1 control-label" for="stnr">Stnr.:</label>
					<div class="col-sm-1">
			    		<input class="form-control" type="text" name="stnr" id="stnr" placeholder="">
			    	</div>
					<label class="col-sm-1 control-label" for="zielzeit">Zielzeit:</label>
					<div class="col-sm-2">
		    			<input class="form-control" type="text" name="zielzeit" id="zielzeit" placeholder="YYYY-MM-DD 00:00:00">
					</div>
					<div class="col-sm-1">
						<button type="submit" id="submit" class="btn btn-success" value="save">save</button>
			  		</div>
				</div>
			</form>
		</div>
	</div>
	
	<h3>Reader Zeiten</h3>
	
	<div class="table-responsive">
	<table class="table table-striped table-vcenter">
	<thead>
	<tr>
	<th>VID</th>
	<th>LID</th>
	<th>Stnr.</th>
	<th>Zielzeit</th>
	<th>Timestamp</th>
	<th>Reader IP</th>
	<th>Action</th>
	</tr>
	</thead>
	<tbody>
	
<?php 
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			while(strlen($row['millisecond']) < 3 ) { $row['millisecond'] = "0".$row['millisecond']; }

?>
		<tr>
			<td><?php echo $row['vID']; ?></td>
			<td><?php echo $row['lID']; ?></td>
			<td><?php echo $row['nummer']; ?></td>
			<td><?php echo $row['zeit'].".".$row['millisecond']; ?></td>
			<td><?php echo $row['TIMESTAMP']; ?></td>
			<td><?php echo $row['Reader']; ?></td>
			<td>
				<a id="<?php echo $row['ID']; ?>" href="#" onclick="javascript:deleteManReaderTime(<?php echo $row['ID'] ?>); return false;"><i class="fa fa-times"></i></a>
				<?php if($row['del'] == 1) { ?>
					&nbsp;<i class="fa fa-trash"></i>
				<?php } ?>
				<?php if($row['Reader'] == '0.0.0.0') { ?>
					&nbsp;<i class="fa fa-pencil"></i>
				<?php } ?>
			</td>
		</tr>
<?php 

		}
	}
	
?>
	</tbody>
	</table>
	</div>
<?php

}

function deleteManReaderTime() {
	$del = 0;
	$sql = "select del from zeit where id = ".$_GET['id'];
	$result = dbRequest($sql, 'SELECT');
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			if($row['del'] == 0) {
				$del = 1;
			} else {
				$del = 0;
			}
		}
	}
	
	$sql = "update zeit set del = ".$del." where id = ".$_GET['id'];
	$result = dbRequest($sql, 'INSERT');
	
	if($result[0] == true) {
		echo "ok";
	} else {
		echo $result[2];
	}
}

function saveManReaderTime() {
	$sql = "insert into zeit (vid, lid, nummer, zeit, reader) values (".$_SESSION['vID'].", ".$_POST['lid'].", ".$_POST['stnr'].", '".$_POST['zielzeit']."', '0.0.0.0')";
	$result = dbRequest($sql, 'INSERT');
	
	if($result[0] == true) {
		echo "ok";
	} else {
		echo $result[2];
	}

}

?>