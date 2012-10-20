<?php 
session_start();
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
	<td></td>
	<td width="200"></td>
	<td></td>
  </tr>
  <tr>
    <td></td>
    <td class="grey-bg" align="left" width="250">
    	<p>&nbsp;</p>
		<div style="padding:10px">
			<input type="hidden" id="jahr" value="<?php echo substr($_SESSION['vDatum'], 0, 4); ?>">
	    	<input name="l1" id="l1" type="text" value="0" onkeyup="calculateAge(); return false;" size="6"> Jahrgang 1. Läufer<br>
	    	<input name="l2" id="l2" type="text" value="0" onkeyup="calculateAge(); return false;" size="6"> Jahrgang 2. Läufer<br>
	    	<input name="l3" id="l3" type="text" value="0" onkeyup="calculateAge(); return false;" size="6"> Jahrgang 3. Läufer<br>
	    	<br>
    	</div>
		<table>
		  <tr>
			<td width="1" align="right">Gruppenjahrgang:</td>
			<td><div id="calcResultJg"></div></td>
    	  </tr>
		  <tr>
			<td width="1" align="right">Gruppenalter:</td>
			<td><div id="calcResultAlter"></div></td>
    	  </tr>    	  
    	</table>
    </td>
    <td></td>
  </tr>
  <tr>
	<td></td>
	<td width="200"></td>
	<td></td>
  </tr>
</table>


