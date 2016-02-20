/*
 * 
 */

function selectUrkundeResult(num, id) {
	//var target = this;
	var jqxhr = $.get( "ajaxRequest.php?func=setNumOfResults&id=" + id + "&num=" + num);
	jqxhr.success(function( data ) {
		var prefix = "";
		if(num != 'ALL') { prefix = '&nbsp;&nbsp;&nbsp;&nbsp;'; }
		$( '#num-of-results-' + id ).html( num + prefix + '<span class="caret"></span>');
	});
}


function selectVeranstaltung( id ) {
	var jqxhr = $.get( "ajaxRequest.php?func=selectVeranstaltung&id=" + id );
	jqxhr.success(function( data ) {
		$( '[class^=veranstaltung]' ).removeClass( 'bold' );
		$( '.veranstaltung-' + id ).addClass( 'bold' );
		$( '#page-header' ).html( data );
	});
}


function lockRace( id ) {
	var jqxhr = $.get( "ajaxRequest.php?func=lockRace&lid=" + id);
	jqxhr.success(function( data ) {
		//console.log(data);
		if( data == 1 ) {
			$( '#lock-' + id ).removeClass( 'fa-unlock' );
			$( '#lock-' + id ).addClass( 'fa-lock' );
		} else {
			$( '#lock-' + id ).removeClass( 'fa-lock' );
			$( '#lock-' + id ).addClass( 'fa-unlock' );
		}
	});
}


function submitForm(form, redirect) {
	var result = '';	
	
	formData = new FormData();
	params = $( form ).serializeArray();
	
    $.each(params, function(i, val) {
        formData.append(val.name, val.value);
        console.log(val.name + ': ' + val.value);
    });
    
    // wenn es ein file upload gibt
	if ($('[name="uploadFile"]').length > 0) {
		files = $( form ).find('[name="uploadFile"]')[0].files;
    
	    $.each(files, function(i, file) {
	        formData.append('uploadFile-' + i, file);
	    });
	}
	
	$.ajax({
		type: "POST",
		url: "ajaxRequest.php",
		//data: $( form ).serialize(),
		data: formData,
        cache: false,
        contentType: false,
        processData: false,
		async: true,
		success: function(msg) {
			if(msg != 'ok') {
				redirect = false;
				$('.alert').html(msg);
				$('.alert').removeClass('hidden');
			} else {
				if(form == '#editTeilnehmer') {
					clearForm();
				}
			}
			console.log(redirect);
			if(redirect) { window.location.href = redirect; }
		}
	})
	
}


function clearForm() {
	$('#stnr').val('');
	$('#vorname').val('');
	$('#nachname').val('');
	$('#verein').val('');	
	$('#jahrgang').val('');
	$('#geschlecht').val('');
	$('#ort').val('');
	$('#zeit').val('00:00:00');
}


function addKlasseZeile( id ) {
	var jqxhr = $.get( 'ajaxRequest.php?func=addKlasse&id=' + id );
	jqxhr.success(function( data ) {
		if(data == 'ok') {
		window.location.href = 'index.php?func=klasse&id=' + id;
		}
	});
}


function deleteKlasse( id, kid ) {
	var jqxhr = $.get( 'ajaxRequest.php?func=deleteKlasse&id=' + id );
	jqxhr.success(function( data ) {
		if(data == 'ok') {
			window.location.href = 'index.php?func=klasse&id=' + kid;
		}
	});
}


function deleteManReaderTime(id) {
	var jqxhr = $.get( 'ajaxRequest.php?func=deleteManReaderTime&id=' + id );
	jqxhr.success(function( data ) {
		if(data == 'ok') {
			window.location.href = 'index.php?func=ziel.edit';
		} else {
			$('.alert').html(data);
			$('.alert').removeClass('hidden');
		}
	});
}

function deleteFullKlasse( id ) {
	var jqxhr = $.get( 'ajaxRequest.php?func=deleteFullKlasse&id=' + id );
	jqxhr.success(function( data ) {
		if(data == 'ok') {
			window.location.href = 'index.php?func=klasse';
		} else {
			$('.alert').html(data);
			$('.alert').removeClass('hidden');
		}
	});
}


function getKlasse(jg, sex, lid)
{
	var url = "ajaxRequest.php?func=getKlasse&jg=" + jg + "&sex=" + sex + "&lid=" + lid;
	$.get( url, function(data){
		setKlasse(data);
	});
}


function setKlasse(data)
{
	var klasseArray;
	klasseArray = data.split(";");
 
	$("#klasse").val(klasseArray[0]);
	$("#vklasse").val(klasseArray[1]);
}


function showHideRunden(id) {
	//console.log(id);
    if( id == 1) {
        $( '#rundenrennen' ).removeClass('hidden');
    } else {
        $( '#rundenrennen' ).addClass('hidden');
    }
}


function showContent( func, param ) {
	$( '.content-table' ).load( 'ajaxRequest.php?func=' + func + '&id=' + param );
}


function clearContent() {
	$.get( "ajaxRequest.php?func=clearRaceId" );
	$( '.content-table' ).html('');	
}


function doAuswertung( id ) {
	$('#modal').modal();
	
	var jqxhr = $.getJSON( "ajaxRequest.php?func=doAuswertung&id=" + id);
	jqxhr.success(function( data ) {
		//console.log(data);
		 
		$( '#modal-body' ).html( data.message );
		
		// zeige die gerade berechneten Ergebnisse
		showContent( 'showErgebnisse', id );
		$('#finisher-' + id).html( data.finisher );
	});
		
}

function checkEinlaufListe( t ) {
	var id = $( t ).attr("id")
	var url = 'ajaxRequest.php?func=showEinlaufListe&id=' + id
	
	if( t.checked ) {
		action = '&action=add';		
	} else {
		action = '&action=remove';		
	}
	$( '.content-table' ).load( url + action );
}


function saveManZielzeit( t, action ) {    	
    // Encode the String
	if( action == 'save') {
		var encodedTimeString = Base64.encode($('#zeit_' + $( t ).attr("id")).val());
		var getURL = 'ajaxRequest.php?func=saveManZielzeit&time=' + encodedTimeString + '&id=' + $( t ).attr("id") + '&action=save';
	} else {
		var getURL = 'ajaxRequest.php?func=saveManZielzeit&time=&id=' + $( t ).attr("id") + '&action=del';		
	}

	var pageToLoad = 'ajaxRequest.php?func=showEinlaufListe&id=0&action=none';
	var scrollToObject = '#zeit_' + $( t ).attr("id");

	var jqxhr = $.get( getURL );

	jqxhr.done( function() {
		$(".content-table").load(pageToLoad, function() {
			$('html, body').animate({
				scrollTop: $(scrollToObject).offset().top - 50
				}, 1000);
			});
		});

	return false;
}

function showZielzeitAnalyse( t ) {
	var encodedStartString = Base64.encode($('#startAnalyseTime_' + $( t ).attr("id")).val());
	var encodedDuratonString = Base64.encode($('#duration_' + $( t ).attr("id")).val());
	
	$( '.content-table' ).load( 'ajaxRequest.php?func=showZielAnalyse&id=' + $( t ).attr("id") + '&start=' + encodedStartString + '&duration=' + encodedDuratonString);
}

function clearModal() {
	var data = '<span class="text-muted">loading...</span>';
	$( '#modal-body' ).html( data );
}

