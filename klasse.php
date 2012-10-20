<?php
/*
 * Created on 06.11.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function klasse() {
	global $func;
	$newKlass = false;
	if(!isset($func[1])) { $func[1] = ""; }

	# insert / edit Veranstaltung
	if (isset($_POST['submit']) || $func[1] == "insert" || $func[1] == "delete") {
		$link = connectDB();

		// Neuanlage einer Klasse
		if ($func[1] == "insert") {
			$sql = "insert into klasse (name) value ('neue Klasse')";
			$result = mysql_query($sql);
			if (!$result) { die('insert klasse - Invalid query: ' . mysql_error()); }
			$kID = mysql_insert_id();
			//echo $kID;
				
			$newKlass = true;
		}

		// update einer bestehenden Klasse
		if ( $_POST['submit'] == "Speichern" || $_POST['submit'] == "neue Zeile") {

			$sql = "update klasse set name = '".htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8')."' where ID = ".$_POST['kID'];
			$result = mysql_query($sql);
			if (!$result) { die('update klasse - Invalid query: ' . mysql_error()); }

			// update der klasse_data
			$i = 0;
			$count = $_POST['count'] - 1;
			while ($i <= $count) {
				$kdID = 0; $n = 0; $g = ""; $v = 0; $b = 0;
				
				$kdID = $_POST['kdID'.$i];
				$n = htmlspecialchars($_POST['name'.$i], ENT_QUOTES, 'UTF-8');
				$g = htmlspecialchars(strtoupper($_POST['gender'.$i]), ENT_QUOTES, 'UTF-8');
				$v = htmlspecialchars($_POST['altervon'.$i], ENT_QUOTES, 'UTF-8');
				$b = htmlspecialchars($_POST['alterbis'.$i], ENT_QUOTES, 'UTF-8');
				$sql = "update klasse_data set name = '$n', geschlecht = '$g', altervon = $v, alterbis = $b where ID = $kdID";
				$result = mysql_query($sql);
				if (!$result) { die('update klasse_data - Invalid query: ' . mysql_error()); }
				$i++;
			}
				
		}

		// einfügen einer neuen Zeile
		if($_POST['submit'] == "neue Zeile" || $newKlass == true) {
			if( $newKlass == true ) { $kID = $kID; } else { $kID = $_POST['kID']; }

			$name = "Name";
			$geschlecht = "x";
			$altervon = 0;
			$alterbis = 0;

			$sql = "insert into klasse_data " .
					"(kID, name, geschlecht, altervon, alterbis) " .
					"values ( $kID, '$name', '$geschlecht', $altervon, $alterbis)";
			$result = mysql_query($sql);
			if (!$result) { echo $sql; die('Invalid query: ' . mysql_error()); }
		}

		// Rücksprung bei neuer Zeile
		if($_POST['submit'] == "neue Zeile") {
			$script = $_POST['nextUrl'];
			header('Location: '.$script);
			die;
		}

		// Rücksprung bei neuer Klasse
		if($newKlass == true) {
			$script = $_SERVER["SCRIPT_NAME"]."?func=".$func[0].".edit&ID=$kID";
			header('Location: '.$script);
			die;
		}

		// delete klasse_data
		if(($func[1] == "delete") && ($func[2] == "kldata")) {
			$sql = "delete from klasse_data where ID = ".$_GET['ID'];
			$result = mysql_query($sql);
			if (!$result) { echo $sql; die('Invalid query: ' . mysql_error()); }

			$script = base64_decode($_GET['nextUrl']);
			header('Location: '.$script);
			die;
		}

		mysql_close($link);

	}

	# display Form
	if ($func[1] == "edit" || $func[1] == "insert") {

		if($func[1] == "edit") {
			$link = connectDB();
			$sql = "select * from klasse where ID = ".$_GET['ID'];
			$result = mysql_query($sql);
			if (!$result) {
				die('Invalid query: ' . mysql_error());
			}

			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$name = $row['name'];
				$kID = $row['ID'];
			}
			mysql_close($link);
		}

		$nextUrl = $_SERVER['REQUEST_URI'];
		$html  ="<form name=\"editVeranstaltungen\" method=\"POST\" action=\"?func=klasse\">\n";
		$html .="<input name=\"func\" type=\"hidden\" value=\"$func[1]\">\n";
		$html .="<input name=\"kID\" type=\"hidden\" value=\"$kID\">\n";
		$html .="<input name=\"nextUrl\" type=\"hidden\" value=\"$nextUrl\">\n";
		$html .="<div class=\"vboxitem\" >\n";
		$html .="	<span class=\"description\" >\n";
		$html .="		Hier k&ouml;nnen Sie die Klassendefinition eingeben. Felder mit einem * sind Pflicht.\n";
		$html .="	</span>\n";
		$html .="</div>\n";
		#$html .="    <p class=\"vboxspacer\">&nbsp;</p>\n";
		$html .="<div class=\"vboxitem\" >\n";
		$html .="	<table class=\"grey-bg\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" >\n";
		$html .="		<tr class=\"top-row\" >\n";
		$html .="			<td class=\"leftcolumn\" nowrap >\n";
		$html .="				Name*:\n";
		$html .="			</td>\n";
		$html .="			<td class=\"rightcolumn\" >\n";
		$html .="				<input type=\"text\" name=\"name\" maxlength=\"200\" size=\"50\" value=\"$name\"/>\n";
		$html .="			</td>\n";
		$html .="			<td class=\"errorcolumn\" ></td>\n";
		$html .="		</tr>\n";

		$html .="		<tr class=\"whiteBg\">\n";
		$html .="			<td>\n";
		$html .="				&nbsp;\n";
		$html .="			</td>\n";
		$html .="			<td>\n";
		$html .="				&nbsp;\n";
		$html .="			</td>\n";
		$html .="			<td class=\"errorcolumn\" ></td>\n";
		$html .="		</tr>\n";
		$html .="	</table>\n";

		$html .="	<table class=\"grey-bg\" width=\"50%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" >\n";

		$link = connectDB();

		$sql = "select * from klasse_data where kID = $kID order by geschlecht, name";
		$result = mysql_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}

		$html .="		<tr class=\"top-row\" >\n";
		$html .="			<td class=\"leftcolumn\" nowrap >\n";
		$html .="				Name*:\n";
		$html .="			</td>\n";
		$html .="			<td class=\"leftcolumn\" nowrap >\n";
		$html .="				Geschlecht*:\n";
		$html .="			</td>\n";
		$html .="			<td class=\"leftcolumn\" nowrap >\n";
		$html .="				Alter von*:\n";
		$html .="			</td>\n";
		$html .="			<td class=\"leftcolumn\" nowrap >\n";
		$html .="				Alter bis*:\n";
		$html .="			</td>\n";
		$html .="			<td class=\"leftcolumn\" nowrap >\n";
		$html .="				Aktion:\n";
		$html .="			</td>\n";
		$html .="		</tr>\n";

		$i= 0;
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$html .="		<tr class=\"top-row\" >\n";
			$html .="			<td class=\"leftcolumn\" nowrap >\n";
			$html .="				<input type=\"hidden\" name=\"kdID$i\" value=\"".$row['ID']."\"></input>\n";
			$html .="				<input type=\"text\" name=\"name$i\" value=\"".$row['name']."\"></input>\n";
			$html .="			</td>\n";
			$html .="			<td class=\"leftcolumn\" nowrap >\n";
			$html .="				<input type=\"text\" name=\"gender$i\" value=\"".$row['geschlecht']."\"></input>\n";
			$html .="			</td>\n";
			$html .="			<td class=\"leftcolumn\" nowrap >\n";
			$html .="				<input type=\"text\" name=\"altervon$i\" value=\"".$row['altervon']."\"></input>\n";
			$html .="			</td>\n";
			$html .="			<td class=\"leftcolumn\" nowrap >\n";
			$html .="				<input type=\"text\" name=\"alterbis$i\" value=\"".$row['alterbis']."\"></input>\n";
			$html .="			</td>\n";
			$html .="			<td class=\"leftcolumn\" nowrap >\n";
			$html .="				<a href=\"".$_SERVER["SCRIPT_NAME"]."?func=klasse.delete.kldata&ID=".$row['ID']."&nextUrl=".base64_encode($_SERVER["REQUEST_URI"])."\">delete</a>";
			$html .="			</td>\n";
			$html .="		</tr>\n";
			$i++;
		}
		mysql_close($link);

		$html .="	</table>\n";
		$html .="</div>\n";


		$html .="<div class=\"vboxitem\" >\n";
		$html .="	<div class=\"navigation-buttons\" >\n";
		$html .="		<input name=\"count\" type=\"hidden\" value=\"$i\">\n";
		$html .="		<input type=\"button\" name=\"cancel\" value=\"<< Zur&uuml;ck\" class=\"button\" ONCLICK=\"window.location.href='".$_SERVER["SCRIPT_NAME"]."?func=klasse'\">\n";
		$html .="		&nbsp;&nbsp;\n";
		$html .="		<input name=\"submit\" type=\"submit\" value=\"Speichern\" class=\"button\">\n";
		$html .="		&nbsp;&nbsp;\n";
		$html .="		<input name=\"submit\" type=\"submit\" value=\"neue Zeile\" class=\"button\">\n";
		$html .="	</div>\n";
		$html .="</div>\n";
		$html .="</form>\n";

	} else {
		# Display Rennen
		$html = "";
		$link = connectDB();
		$sql = "select * from klasse order by name asc;";
		$result = mysql_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}

		$html2 = "";
		$i=1;
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			if($i%2 == 0) { $html2 .= "<tr class=\"even\">\n"; } else { $html2 .= "<tr class=\"odd\">\n"; }

			$html2 .= "<td align\"left\">".$row['name']."</td>\n";
			$html2 .= "<td align\"center\">" .
					"<a href=\"".$_SERVER["REQUEST_URI"].".edit&ID=".$row['ID']."\">edit</a>" .
					"&nbsp;&nbsp;" .
					"</td>\n";
			$html2 .= "</tr>\n";
			$i++;
		}

		$columns = array('Name', 'Aktion');
		$html .= tableList($columns, $html2, "common meetings");

		mysql_close($link);

		$html .="<br><div class=\"vboxitem\" >\n";
		$html .="	<div class=\"navigation-buttons\" >\n";
		$html .="		<input type=\"submit\" value=\"neue Klasse\" class=\"button\" ONCLICK=\"window.location.href='".$_SERVER["REQUEST_URI"].".insert'\">\n";
		$html .="	</div>\n";
		$html .="</div>\n";

	}

	return table("Klassenverwaltung", $html);
}

?>
