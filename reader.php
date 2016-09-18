<?php

function reader() {
	showReaderList();	
}

function showReaderList() {
	$config = getConfig();
?>

	<script>

		$(document).ready(function(){

			$('#myTabs a').click(function (e) {
				  e.preventDefault()
				  $(this).tab('show')
				})
			
			getReaderData(0, 'null');
			getReaderData(1, 'null');
		});

		function setConfig(key, value) {
			params = { form: "setConfig", key: key, value: $( '#'+value).val() };
			var jqxhr = $.post( "ajaxRequest.php", params);
		}

		
		function getReaderData(r, a) {
			var action = 'action:null';

			switch(a) {
				case 0:
					action = 'antenna:' + $('#antenna'+r).val();
					break;
				case 1:
					action = 'time:true';
					break;
				case 2:
					action = 'trValidTime:' + $('#trValidTime'+r).val();
					break;
				case 3:
					action = 'power:' + $('#power'+r).val();
					break;
				case 4:
					action = 'resetReaderFile:true';
			}
			
			params = { form: "getReaderData", wsdl: $('#url'+r).val(), action: action };
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
			f = val.split(';');
			f.sort();
			f.forEach(function(entry) {
			    table = table + "<tr><td>" + entry + "</td><td><a class='btn btn-success' href='#' onclick=\"showReaderResults('" + entry + "'); return false;\" role='button'>SHOW</a></td></tr>";
			});

			$('#tbody0').html(table);
		}


		function showReaderResults(file) {
			$('#modal').modal();

			var jqxhr = $.get( "ajaxRequest.php?func=showReaderResults&id=" + file );

			jqxhr.done(function( data ) {
				//$( '#modal-body' ).html( data );
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
	    <div role="tabpanel" class="tab-pane <? if ($i == 0) { echo "active"; } ?>" id="reader<?php echo $i; ?>">

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
						<input name="trValidTime" maxlength="200" type="text" class="form-control" id="trValidTime<?php echo $i; ?>" placeholder="Transponder Valid Time" value="">
					</div>
					<div class="col-sm-4">
						<a class="btn btn-success" href="#" onclick="getReaderData(<?php echo $i; ?>,2); return false;" role="button">SET</a>
					</div>
				</div>
			 
			 	<div class="form-group">
					<label for="mode" class="col-sm-4 control-label">Mode:</label>
					<div class="col-sm-3">
						<input name="mode" maxlength="200" type="text" class="form-control" id="mode<?php echo $i; ?>" placeholder="Mode" value="">
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
						<input name="time" maxlength="200" type="text" class="form-control" id="time<?php echo $i; ?>" placeholder="Time" value="">
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
	$params['power']           = "";
	$params['trValidTime']     = "";
	$params['antenna']         = "";
	$params['time']            = "";
	$params['mode']            = "";
	$params['resetReaderFile'] = "";
	
	if(isset($_POST['action'])) {
		$a = explode(":", $_POST['action']);
		$params[$a[0]] = $a[1];
	}
		
	try {
		$client = new SoapClient($_POST['wsdl'], array('cache_wsdl' => WSDL_CACHE_NONE));
		$result = $client->setReaderConfig($params['power'], $params['time'], $params['trValidTime'], $params['antenna'], $params['mode'], $params['resetReaderFile']);
		print json_encode($result);
	} catch (SoapFault $fault) {
		print json_encode($fault);
	}
	
}
