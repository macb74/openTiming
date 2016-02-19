<?php
/*
 * Created on 06.11.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function import() {
	global $func;
	uploadForm($func[1]);
}

function tImport() {
	$filename = uploadFile();
	$lines = parseFile($filename);
	//		echo "<pre>";
	//		print_r($lines);
	//		echo "</pre>";
	tUpdateDB($lines);
}

function zImport() {
	$filename = uploadFile();
	$lines = parseFile($filename);
	//		echo "<pre>";
	//		print_r($lines);
	//		echo "</pre>";
	zUpdateDB($lines);
}

function uploadFile() {
	$uploaddir = 'upload/';
	$uploadfile = $uploaddir.basename($_FILES['uploadFile-0']['name']);

	if (!move_uploaded_file($_FILES['uploadFile-0']['tmp_name'], $uploadfile)) {
		echo "uploadError"; die;
	}
	return $uploadfile;
}

function parseFile($file) {
	$row = 0;
	$handle = fopen ("$file","r");
	while ( ($data = fgetcsv ($handle, 10000, ";", "\"")) !== FALSE ) {
		$num = count($data);
		for ($c=0; $c < $num; $c++) {
			if ($row != 0) {					// erste Zeile enthält die Ueberschriften
				$lines[$row][$c] = $data[$c];
			}
		}
		$row++;
	}
	fclose ($handle);
	return $lines;
}

function tUpdateDB($lines) {

	$i = 1;
	$didIt = 0;
	$errMsg = "";
	
	foreach($lines as $line) {
		
		# wenn kein Nachname vorhanden, dann wird nicht importiert
		if ($line[3] != "") {
			if(!isset($line[8])) { $line[8] = ""; }	
		
			$sql = "select ID from teilnehmer where vID = $line[0] and lID = $line[1] and stnr = $line[2] and del = 0";
			$res = dbRequest($sql, 'SELECT');
			
			if ($res[1] >= 0) {
				$line[5] = strtoupper($line[5]);
				$go = 0;
				$num = $res[1];
				if($num != 0) {
					foreach ($res[0] as $row) {
						$tID = $row['ID'];
					}
				}
	
				$line = filterParameters($line);
				$klasse = getKlasse($line[6], $line[5], $line[1], 0);
					
				if(isset($_POST['update']) == 1 && $_POST['update'] == 1 && $num != 0) {
					$sql1 = "update teilnehmer set " .
					"vID = $line[0], lID = $line[1], stnr = $line[2], nachname = '".$line[3]."', vorname = '".$line[4]."', " .
					"geschlecht = '$line[5]', jahrgang = $line[6], verein = '".trim($line[7])."', att = '".trim($line[8])."', klasse = '$klasse[0]', vklasse = '$klasse[1]' " .
					"where ID = $tID";
					$go = 1;
				}
				if($num == 0) {
					$sql1 = "insert into teilnehmer " .
					"(vID, lID, stnr, nachname, vorname, geschlecht, jahrgang, verein, att, klasse, vklasse) " .
					"values ( $line[0], $line[1], $line[2], '".$line[3]."', '".$line[4]."', '$line[5]', '$line[6]', '".trim($line[7])."', '".trim($line[8])."', '$klasse[0]', '$klasse[1]')";			
					$go = 1;
				}
					
				if($go == 1) {
					$result1 = dbRequest($sql1, 'INSERT');
					if (!$result1[0]) {
						$errMsg .= "Fehler in Zeile $i - Fehlermeldung: " . $result1[2] . "<br>\n";
					} else {
						$didIt++;
					}
				}
	
			} else {
				$errMsg .= "Fehler in Zeile $i<br>\n";
			}
			$i++;
		}
	}

	$errMsg .= "$didIt Datens&aumltze erfolgreich eingef&uuml;gt / aktualisiert<br>\n";
	echo $errMsg;
}

function zUpdateDB($lines) {
	$i = 1;
	$didIt = 0;

	foreach($lines as $line) {
		
		$errMsg = "";
		$sql = "select ID from teilnehmer where vID = $line[0] and lID = $line[1] and stnr = $line[2] and del = 0";
		//echo $sql;
		$result = dbRequest($sql, 'SELECT');
		if ($result) {
			$num = $result[1];
			if($num != 0) {
				foreach ($result[0] as $row) {
					$tID = $row['ID'];
				}
			}

			if($num != 0) {
				$sql1 = "update teilnehmer set " .
				"manzeit = '$line[3]', zeit = '$line[3]', usemantime = 1 " .
				"where ID = $tID";

				//echo $sql1."<br>";
				$res1 = dbRequest($sql1, 'INSERT');
				if (!$res1[0]) {
					$errMsg .= "Fehler in Zeile $i - Fehlermeldung: " . $res1[2] . "<br>\n";
				} else {
					$didIt++;
				}
			}

		} else {
			$errMsg .= "Fehler in Zeile $i<br>\n";
		}
		$i++;
	}

	$errMsg .= "$didIt Datens&aumltze erfolgreich eingef&uuml;gt / aktualisiert<br>\n";
	echo $errMsg;
}

function uploadForm($func) {
	
?>

	<script>

		$(document).on('change', '.btn-file :file', function() {
		  var input = $(this),
		      numFiles = input.get(0).files ? input.get(0).files.length : 1,
		      label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		  input.trigger('fileselect', [numFiles, label]);
		});

		$(document).ready( function() {

			$("#submit").click(function(event){
			    event.preventDefault();			    
				submitForm('#upload', false);
			});


		    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
		        
		        var input = $(this).parents('.input-group').find(':text'),
		            log = numFiles > 1 ? numFiles + ' files selected' : label;
		        
		        if( input.length ) {
		            input.val(log);
		        } else {
		            if( log ) alert(log);
		        }
		        
		    });
		});
		
	</script>


<?php if ($func == "teilnehmer")	{ echo	"<h3>Teilnehmerliste einlesen</h3>"; } ?>
<?php if ($func == "zeit") 			{ echo	"<h3>Zeitliste einlesen</h3>"; } ?>

<?php  if ($func == "teilnehmer")	{ $form = "uploadTeilnehmer"; } ?>
<?php  if ($func == "zeit")		 	{ $form = "uploadZeit"; } ?>

	<div class="alert alert-danger hidden col-sm-offset-3 col-sm-6" id="alert" role="alert"></div>
	<form role="form" class="form-horizontal" enctype="multipart/form-data" id="upload" name="upload">

		<input type="hidden" name="form" value="<?php echo $form; ?>"></input>
	
		<div class="form-group">
			<?php if ($func == "teilnehmer") { ?>
			<label for="datei" class="col-sm-4 control-label">
				Update existing:
			</label>
			<label class="checkbox-inline">
				<input type="checkbox" name="update" value="1">	wenn diese Option nicht aktiviert ist, werden nur Datens&auml;tze angelegt, die noch nicht vorhanden sind			
			</label>
	
			
			<?php } ?>
		</div>	
		
		<div class="form-group">
			<label for="datei" class="col-sm-4 control-label">
				<?php  if ($func == "teilnehmer")	{ echo "Teilnehmerdatei:"; } ?>
				<?php  if ($func == "zeit")		 	{ echo "Zeitdatei:"; } ?>
			</label>
			<div class="col-sm-4">
				<div class="input-group">
		            <input type="text" class="form-control input-readonly" readonly>
		        	<span class="input-group-btn">
		            	<span class="btn btn-primary btn-file">
		                        Browse&hellip; <input type="file" name="uploadFile" multiple>
		                </span>
		            </span>
				</div>
	        </div>
		</div>
	
		<div class="form-group">
			<div class="col-sm-offset-4 col-sm-5">
				<button type="submit" id="submit" class="btn btn-success" value="upload">upload</button>
			</div>
		</div>
	</form>

	<p>&nbsp;</p>
	<p class="col-sm-offset-2 col-sm-8 bg-info">
		<b>Dateiformat:</b><br>
		<?php if ($func == "teilnehmer") { ?>
			Die erste Zeile enth&auml;lt die Spalten&uuml;berschriften<br>
			Veranstaltung;Rennen;Startnumer;Nachname;Vorname;Geschlecht;Jahrgang;Verein;Attribut
		<?php } else { ?>
			Die erste Zeile enth&auml;lt die Spalten&uuml;berschriften<br>
			Veranstaltung;Rennen;Startnumer;Zeit (HH:MM:SS)
		<?php } ?>
	</p>

<?php
}

?>
