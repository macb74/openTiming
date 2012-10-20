$(document).ready(function(){
	$("#verein").autocomplete('getVerein.php');
});

$(document).ready(function(){
	$("#rID").change(function(){
		showHideRunden(document.getElementById('rID').value);
	});
});

$(document).ready(function(){
	$("#calculator").click(function(){
		if(document.getElementById('data_div').innerHTML == '') {
			$.get('calculator.php', function(data){
				document.getElementById('data_div').innerHTML = data;
				document.getElementById('calculator').innerHTML = 'hide calculator';
				document.cookie = 'calculator=1';
			});	
		} else {
			document.getElementById('data_div').innerHTML = '';
			document.getElementById('calculator').innerHTML = 'show calculator';
			document.cookie = 'calculator=0';
		}
	});
});

function showCalculatorAtPageLoad() {
	if(GetCookie('calculator') == 1) {
		$.get('calculator.php', function(data){
			document.getElementById('data_div').innerHTML = data;
			document.getElementById('calculator').innerHTML = 'hide calculator';
		});
	}
}

function showHideRunden(rID) {
	if(rID != 'X') {
    	$.get("getIsRR.php?id=" + document.getElementById('rID').value, function(data){
			//alert(data);
    	
    		if(data != 0) $('#rr1').fadeIn('fast');
         	else $("#rr1").fadeOut('fast');

         	if(data != 0) $('#rr2').fadeIn('fast');
         	else $("#rr2").fadeOut('fast');

         	if(data != 0) $('#rr3').fadeIn('fast');
         	else $('#rr3').fadeOut('fast');
     
		});
	}
}

function updateSumRunden(a, b) {
	if(a == '') { a = 0 }
	var c = parseInt(a) + parseInt(b);
	//alert(c);
	document.getElementById('sumRunden').innerHTML = '<b>' + c + ' Runden</b>';
}


function getCookieVal(offset) { 
	var endstr = document.cookie.indexOf (";", offset); 
	if (endstr == -1) { endstr = document.cookie.length; }
	return unescape(document.cookie.substring(offset, endstr));
}


function GetCookie(name) { 
	var arg = name + "="; 
	var alen = arg.length; 
	var clen = document.cookie.length; 
	var i = 0; 
	while (i < clen) {
		var j = i + alen; 
		if (document.cookie.substring(i, j) == arg) { return getCookieVal (j); }
		i = document.cookie.indexOf(" ", i) + 1; 
		if (i == 0) { break; }
	} 
	return null;
}


function calculateAge() {
	var l1 = parseInt(document.getElementById('l1').value);
	var l2 = parseInt(document.getElementById('l2').value);
	var l3 = parseInt(document.getElementById('l3').value);
	var jahr = parseInt(document.getElementById('jahr').value);
	
	if(l1 > 1800 && l2 > 1800 && l3 > 1800) {
		var j = jahr * 3;
		var x = l1 + l2 + l3;
	} else if (l1 > 1800 && l2 > 1800 && l3 < 1800) {
		var j = jahr * 2;
		var x = l1 + l2;
	} else if (l1 > 1800 && l2 < 1800 && l3 < 1800) {
		var j = jahr;
		var x = l1;
	}

	var a = j - x;
	var jg = jahr - a;
	document.getElementById('calcResultAlter').innerHTML = a;
	document.getElementById('calcResultJg').innerHTML = jg;
}

// beim laden wird die Klasse neu berechnet und eingeblendet
xajax_getKlasse(document.getElementById('jg').value, document.getElementById('geschlecht').value, document.getElementById('rID').value, 1);

// beim laden wird die Klasse und die Rundenzahl neu berechnet und eingeblendet
updateSumRunden(document.getElementById('manRunden').value, document.getElementById('autRunden').value);
document.getElementById('stnr').focus();

// beim laden wird die Rundenanzeige eingeblendet, wenn es sich um ein Rundenrennen handelt
showHideRunden(document.getElementById('rID').value);

showCalculatorAtPageLoad();
