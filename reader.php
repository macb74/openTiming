<?php

function reader() {
	showReaderList();	
}

function showReaderList() {
	$config = getConfig();
?>

	<script>

		$(document).ready(function(){			
			getReaderData(0, 'null');
			getReaderData(1, 'null');
		});

		function setConfig(key, value) {
			params = { form: "setConfig", key: key, value: $( '#'+value).val() };
			var jqxhr = $.post( "ajaxRequest.php", params);
		}

		
		function getReaderData(r, a) {
			var action = 'null';

			switch(a) {
				case 0:
					action = 'ant/' + $('#antenna'+r).val();
					break;
				case 1:
					action = 'time';
					break;
				case 2:
					action = 'validtime/' + $('#transponderValidTime'+r).val();
					break;
				case 3:
					action = 'power/' + $('#power'+r).val();
					break;
				case 4:
					action = 'resetReaderFile';
			}
			
			params = { form: "getReaderData", url: $('#url'+r).val(), action: action };
			$('#faultstring'+r).addClass('hidden');
			var jqxhr = $.post( "ajaxRequest.php", params);
			
			jqxhr.done(function( data ) {
				var response = jQuery.parseJSON(data);
				$.each( response, function( key, val ) {
					if(key == "faultstring") { 
						$('#' + key + r).html(val);
						$('#' + key + r).removeClass('hidden')
					}
					
					if(key == "files") {
						setTableData(r, val);
					}
					
					$('#' + key + r).val(val);
				});
			});
		}

		function setTableData(r, val) {
			table = "";

			val.sort(function(a, b) {
			    return a.file > b.file;
			});

			val.reverse(); 

			$.each(val, function(key, val) {
			    table = table + "<tr><td>" + val['file'] + "</td>" +
				"<td class='text-right'>" + val['linecount'] + "</td>" + 
		    	"<td><a class='btn btn-success btn-xs' href='#' onclick=\"handleReaderResults('" + $( '#url' + r ).val() + "', '" + val['file'] + "', 'show'); return false;\" role='button'>SHOW</a></td>" + 
				"<td><a class='btn btn-success btn-xs' href='#' onclick=\"handleReaderResults('" + $( '#url' + r ).val() + "', '" + val['file'] + "', 'load'); return false;\" role='button'>LOAD</a></td>" + 
				"</tr>";
			});
			
			$('#tbody'+r).html(table);
		}

		function handleReaderResults(r, file, mode) {
			if(mode == "show") {
				modal = "#modal2";
				method = "showReaderResults";
			} else {
				modal = "#modal";
				method = "loadReaderResults";
			}

			$( modal ).modal();
			params = { form: method, url: r, file: file };
			var jqxhr = $.post( "ajaxRequest.php", params);

			jqxhr.done(function( data ) {
				$( modal + '-body' ).html( data );
			});
		}

	
	</script>

	<h3 class="text-center">Reader Config</h3>

	<div>
	
	  <!-- Nav tabs -->
	  <ul class="nav nav-tabs" role="tablist">
	    <li role="presentation" class="active"><a href="#reader0" id="myTabs" aria-controls="home" role="tab" data-toggle="tab">Reader 1</a></li>
	    <li role="presentation"><a href="#reader1" aria-controls="profile" role="tab" data-toggle="tab">Reader 2</a></li>
	  </ul>
	
	  <!-- Tab panes -->
	  <div class="tab-content">
	    
<?php 
	for($i=0; $i<2; $i++) {
?>	    
	    <div role="tabpanel" class="tab-pane <?php if ($i == 0) { echo "active"; } ?>" id="reader<?php echo $i; ?>">

	    	<div class="alert alert-danger hidden col-sm-offset-3 col-sm-6" id="faultstring<?php echo $i; ?>" role="alert"></div>
	
			<form role="form" class="form-horizontal" id="editReader" name="editReader">
				<div class="form-group"></div>
			
				<div class="form-group">
					<label for="url<?php echo $i; ?>" class="col-sm-4 control-label">Reader URL:</label>
					<div class="col-sm-3">
						<input name="url" maxlength="200" type="text" class="form-control" id="url<?php echo $i; ?>" placeholder="URL" value="<?php echo $config['Reader'.$i]; ?>">
					</div>
					<div class="col-sm-4">
						<a class="btn btn-success" href="#" onclick="setConfig('Reader<?php echo $i; ?>', 'url<?php echo $i; ?>'); return false;" role="button">SET</a>
					</div>	
				</div>
			
				<div class="form-group">
					<label for="power" class="col-sm-4 control-label">Power:</label>
					<div class="col-sm-3">
						<input name="power" maxlength="200" type="text" class="form-control" id="power<?php echo $i; ?>" placeholder="Power" value="">
					</div>
					<div class="col-sm-4">
						<a class="btn btn-success" href="#" onclick="getReaderData(<?php echo $i; ?>,3); return false;" role="button">SET</a>
					</div>	
				</div>
			
				<div class="form-group">
					<label for="trValidTime" class="col-sm-4 control-label">Transponder Valid Time:</label>
					<div class="col-sm-3">
						<input name="transponderValidTime" maxlength="200" type="text" class="form-control" id="transponderValidTime<?php echo $i; ?>" placeholder="Transponder Valid Time" value="">
					</div>
					<div class="col-sm-4">
						<a class="btn btn-success" href="#" onclick="getReaderData(<?php echo $i; ?>,2); return false;" role="button">SET</a>
					</div>
				</div>
			 
			 	<div class="form-group">
					<label for="mode" class="col-sm-4 control-label">Mode:</label>
					<div class="col-sm-3">
						<input name="mode" maxlength="200" type="text" class="form-control" id="mode<?php echo $i; ?>" placeholder="Mode" value="" readonly>
					</div>
			<!-- 
					<div class="col-sm-4">
						<a class="btn btn-success" href="#" onclick="return false;" role="button">SET</a>
					</div>	
			 -->
				</div>
				
				<div class="form-group">
					<label for="time" class="col-sm-4 control-label">Time:</label>
					<div class="col-sm-3">
						<input name="readerTime" maxlength="200" type="text" class="form-control" id="readerTime<?php echo $i; ?>" placeholder="Time" value="">
					</div>
					<div class="col-sm-4">
						<a class="btn btn-success" href="#" onclick="getReaderData(<?php echo $i; ?>,1); return false;" role="button">SET</a>
					</div>	
				</div>
			
				<div class="form-group">
					<label for="antenna" class="col-sm-4 control-label">Antennen:</label>
					<div class="col-sm-3">
						<input name="antenna" maxlength="200" type="text" class="form-control" id="antenna<?php echo $i; ?>" placeholder="Antennen" value="">
					</div>
					<div class="col-sm-4">
						<a class="btn btn-success" href="#" onclick="getReaderData(<?php echo $i; ?>,0); return false;" role="button">SET</a>
					</div>		
				</div>
				
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-3">
						<a class="btn btn-success" href="#" onclick="getReaderData(<?php echo $i; ?>,-1); return false;" role="button">Update</a>
					</div>
					<div class="col-sm-4">
						<a class="btn btn-danger" href="#" onclick="getReaderData(<?php echo $i; ?>,4); return false;" role="button">CUT READER FILE</a>
					</div>
				</div>		
			</form>

			<div class="table-responsive col-sm-offset-3 col-sm-5">
				<table class="table table-striped table-vcenter">
					<thead>
						<tr>
							<th>Datei</th>
							<th>Zeilen</th>
							<th>Aktion</th>
						</tr>
					</thead>
					<tbody id="tbody<?php echo $i; ?>">
						
					</tbody>
				</table>
			</div>

		</div>
		
<?php 
	}
?>		
	  </div>
	</div>
		
<?php 
}

function getReaderData() {
	
	if(isset($_POST['action']) && $_POST['action'] != 'null') {
		$url = $_POST['url'].'/'.$_POST['action'];
		$result = getURL($url);
		//echo $result[0];
	}
	
	$url = $_POST['url'].'/info';
	$result = getURL($url);
	echo $result[0];
}

function getURL($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result[0] = curl_exec($ch);
	$result[1] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return $result;
}

function showReaderResults() {
	$f      = getReaderFile($_POST['url'], $_POST['file']);
	$farray = json_decode($f);
	$farray = array_reverse($farray);
?>
	
	<div class="text-right modal-button">
		<a href="#" class="btn btn-primary btn-sm" onclick="handleReaderResults('<?php echo $_POST['wsdl']; ?>', '<?php echo $_POST['file']; ?>', 'show'); return false;" role="button">refresh</a>
	</div>
	<div class="table-responsive">
		<table class="table table-striped table-vcenter">
			<thead>
				<tr>
					<th>Startnummer</th>
					<th>Datum</th>					
					<th>Uhrzeit</th>
					<th>Milli</th>
					<th>Reader</th>					
					<th>Antenne</th>
					<th>RSSI</th>
					<th>UID</th>
					<th>Lesezeit</th>
				</tr>
			</thead>
			<tbody>
<?php
	foreach ($farray as $line) {
		$fields = explode(";", $line);
    	echo "<tr>";
		foreach ($fields as $field) {
			echo "<td>$field</td>";
		}
    	echo "</tr>";
	}
?>	
			</tbody>
		</table>
	</div>

<?php 
}

function loadReaderResults() {
	$f = getReaderFile($_POST['url'], $_POST['file']);
	$farray = json_decode($f);
	$msg = "";
	
	foreach ($farray as $line) {
		if(substr_count($line, ';') == 8) {
			$field = explode(";", $line);
			$sql = "insert into zeit (vID, nummer, zeit, millisecond, Reader, ant, rssi) ";
			$sql .= "values (".$_SESSION['vID'].",  '".$field['0']."', '".$field['1']." ".$field['2']."', '".$field['3']."', '".$field['4']."', '".decbin($field['5'])."', '".$field['6']."') ";
			$sql .= "ON DUPLICATE KEY UPDATE id=id;";
			$result = dbRequest($sql, 'INSERT');
			if($result[2]) { $msg .= $result[2]."<br><br>"; }
		}
	}
	if ($msg == "") {
		echo "Daten eingelesen";
	} else {
		echo $msg;
	}
}

function getReaderFile($url, $file) {
	$url = $url.'/file/'.$file;
	$result = getURL($url);
	return $result[0];
}
