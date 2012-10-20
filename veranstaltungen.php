<?php
/*
 * Created on 06.11.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function veranstaltungen() {
	global $func;
	$ID = 0;
	$titel = "";
	$untertitel = "";
	$dat = array(0, 0, 0);
	
	# insert / edit Veranstaltung
	if (isset($_POST['submit'])) {
		$link = connectDB();

		$datum = $_POST['year'].$_POST['month'].$_POST['day'];
		if($_POST['func'] == "edit") {
			$sql = "update veranstaltung set titel = '".htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8')."', untertitel = '".htmlspecialchars($_POST['subTitle'], ENT_QUOTES, 'UTF-8')."', datum = ".htmlspecialchars($datum, ENT_QUOTES, 'UTF-8')." where ID = ".$_POST['ID'].";";
		} else {
			$sql = "insert into veranstaltung (titel, untertitel, datum) values ( '".htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8')."', '".htmlspecialchars($_POST['subTitle'], ENT_QUOTES, 'UTF-8')."', ".htmlspecialchars($datum, ENT_QUOTES, 'UTF-8').")";
		}
		#echo $sql;
		$result = mysql_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		mysql_close($link);
	}

	# select Veranstaltung
	if (isset($func[1]) && $func[1] == "select" && isset($_GET['ID'])) {
		$_SESSION['vID'] = $_GET['ID'];
		$script = $_SERVER["SCRIPT_NAME"]."?func=".$func[0];
		header('Location: '.$script);
		die;
	}

	# display Form
	if ((isset($func[1]) && $func[1] == "edit") || (isset($func[1]) && $func[1] == "insert")) {

		if($func[1] == "edit") {
			$link = connectDB();
			$sql = "select * from veranstaltung where ID = ".$_GET['ID'];
			$result = mysql_query($sql);
			if (!$result) {
				die('Invalid query: ' . mysql_error());
			}
			
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$titel = $row['titel'];
				$untertitel = $row['untertitel'];
				$datum = $row['datum'];
				$ID = $row['ID'];
			}
			$dat = explode('-', $datum);
			mysql_close($link);
		}

		$html  ="<form name=\"editVeranstaltungen\" method=\"POST\" action=\"?func=veranstaltungen\">\n";
		$html .="<input name=\"func\" type=\"hidden\" value=\"$func[1]\">\n";
		$html .="<input name=\"ID\" type=\"hidden\" value=\"$ID\">\n";
		$html .="<div class=\"vboxitem\" >\n";
		$html .="	<span class=\"description\" >\n";
		$html .="		Hier k&ouml;nnen Sie die Veranstaltungsdaten eingeben. Felder mit einem * sind Pflicht.\n";
		$html .="	</span>\n";
		$html .="</div>\n";
		#$html .="    <p class=\"vboxspacer\">&nbsp;</p>\n";
		$html .="<div class=\"vboxitem\" >\n";
		$html .="	<table class=\"grey-bg\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" >\n";
		$html .="		<tr class=\"top-row\" >\n";
		$html .="			<td class=\"leftcolumn\" nowrap >\n";
		$html .="				Titel*:\n";
		$html .="			</td>\n";
		$html .="			<td class=\"rightcolumn\" >\n";
		$html .="				<input type=\"text\" name=\"title\" maxlength=\"200\" size=\"50\" value=\"$titel\">\n";
		$html .="			</td>\n";
		$html .="			<td class=\"errorcolumn\" ></td>\n";
		$html .="		</tr>\n";
		$html .="		<tr class=\"middle-row\" >\n";
		$html .="			<td class=\"leftcolumn\" nowrap >\n";
		$html .="				Untertitel*:\n";
		$html .="			</td>\n";
		$html .="			<td class=\"rightcolumn\" >\n";
		$html .="				<input type=\"text\" name=\"subTitle\" maxlength=\"200\" size=\"50\" value=\"$untertitel\">\n";
		$html .="			</td>\n";
		$html .="			<td class=\"errorcolumn\" ></td>\n";
		$html .="		</tr>\n";
		/*
		 $html .="		<tr class=\"middle-row\" >\n";
		 $html .="			<td class=\"leftcolumn\" nowrap >\n";
		 $html .="				Beschreibung :\n";
		 $html .="			</td>\n";
		 $html .="			<td class=\"rightcolumn\" >\n";
		 $html .="				<input type=\"text\" name=\"description\" maxlength=\"200\" size=\"50\" value=\"\">\n";
		 $html .="			</td>\n";
		 $html .="				<td class=\"errorcolumn\" ></td>\n";
		 $html .="		</tr>\n";
		 */
		$html .="		<tr class=\"middle-row\" >\n";
		$html .="   		<td class=\"leftcolumn\" nowrap >Datum*:\n";
		$html .="   		</td>\n";
		$html .="		<td class=\"rightcolumn\" >\n";
		$html .="		      	<select name=\"day\">\n";
		$html .="		      			<option value=\"\">--</option>\n";
		$i=1;
		while($i <= 31) {
			if($i < 10) { $x="0"; } else { $x=""; }
			if($x.$i == $dat[2]) { $s="selected"; } else { $s=""; }
			$html .="						<option value=\"$x$i\" $s>$x$i</option>\n";
			$i++;
		}
		$html .="		      	</select>\n";

		$html .="		      	<select name=\"month\">\n";
		$html .="		      			<option value=\"\">--</option>\n";
		$i=1;
		while($i <= 12) {
			if($i < 10) { $x="0"; } else { $x=""; }
			if($x.$i == $dat[1]) { $s="selected"; } else { $s=""; }
			$html .="						<option value=\"$x$i\" $s>$x$i</option>\n";
			$i++;
		}
		$html .="		      	</select>\n";


		$html .="		      	<select name=\"year\">\n";
		$html .="		      			<option value=\"\">--</option>\n";
		$i=2009;
		while($i <= 2020) {
			if($i == $dat[0]) { $s="selected"; } else { $s=""; }
			$html .="						<option value=\"$i\" $s>$i</option>\n";
			$i++;
		}
		$html .="		      	</select>\n";
		$html .="    		</td>\n";
		$html .="			<td class=\"errorcolumn\" ></td>\n";
		$html .="		</tr>\n";
		$html .="	</table>\n";
		$html .="</div>\n";
		#		$html .="<p class=\"vboxspacer\">&nbsp;</p>\n";
		#		$html .="<div class=\"vboxitem\" ></div>\n";
		#		$html .="<p class=\"vboxspacer\">&nbsp;</p>\n";
		$html .="<div class=\"vboxitem\" >\n";
		$html .="	<div class=\"navigation-buttons\" >\n";
		$html .="			<input type=\"button\" name=\"cancel\" value=\"<< Zur&uuml;ck\" class=\"button\" onclick=\"history.back();\">\n";
		$html .="		&nbsp;&nbsp;\n";
		$html .="		<input name=\"submit\" type=\"submit\" value=\"Speichern\" class=\"button\">\n";
		$html .="	</div>\n";
		$html .="</div>\n";
		$html .="</form>\n";

	} else {

		# Display Veranstaltungen
		$html = "";
		$link = connectDB();
		$sql = "select * from veranstaltung order by datum desc;";
		$result = mysql_query($sql);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}

		$html2 = "";
		$i=1;
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			if($i%2 == 0) { $html2 .= "<tr class=\"even\">\n"; } else { $html2 .= "<tr class=\"odd\">\n"; }

			if(isset($_SESSION['vID']) && $_SESSION['vID'] == $row['ID']) {
				$b1 = "<b>"; $b2 = "</b>";
				$_SESSION['vTitel'] = $row['titel'];
				$_SESSION['vUntertitel'] = $row['untertitel'];
				$_SESSION['vDatum'] = $row['datum'];
			} else {
				$b1 = ""; $b2 = "";
			}
			$html2 .= "<td width=\"30\" align\"left\">".$row['ID']."</td>\n";
			$html2 .= "<td align\"left\">$b1<a href=\"".$_SERVER["SCRIPT_NAME"]."?func=veranstaltungen.select&ID=".$row['ID']."\">".$row['titel']."</a>$b2</td>\n";
			$html2 .= "<td align\"left\">".$row['untertitel']."</td>\n";
			$html2 .= "<td align\"left\">".$row['datum']."</td>\n";
			$html2 .= "<td align\"center\">" .
					"<a href=\"".$_SERVER["REQUEST_URI"].".edit&ID=".$row['ID']."\">edit</a>" .
					"&nbsp;&nbsp;" .
					"</td>\n";
			$html2 .= "</tr>\n";
			$i++;
		}

		$columns = array('ID','Titel', 'Untertitel', 'Datum', 'Aktion');
		$html .= tableList($columns, $html2, "common meetings");

		mysql_close($link);

		$html .="<br><div class=\"vboxitem\" >\n";
		$html .="	<div class=\"navigation-buttons\" >\n";
		$html .="		<input type=\"submit\" value=\"neue Veranstaltung\" class=\"button\" ONCLICK=\"window.location.href='".$_SERVER["REQUEST_URI"].".insert'\">\n";
		$html .="	</div>\n";
		$html .="</div>\n";

	}

	return table("Veranstaltung", $html);
}

?>
