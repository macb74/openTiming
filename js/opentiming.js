/*
 *
 */

var lastChatID = null;
var maxChatID = null;
var cncm = null; //checkNewChatMessage loop

function selectUrkundeResult(num, id) {
  //var target = this;
  var jqxhr = $.get("ajaxRequest.php?func=setNumOfResults&id=" + id + "&num=" + num);
  jqxhr.success(function(data) {
    var prefix = "";
    if (num != 'ALL') {
      prefix = '&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    $('#num-of-results-' + id).html(num + prefix + '<span class="caret"></span>');
  });
}


function selectVeranstaltung(id) {
  var jqxhr = $.get("ajaxRequest.php?func=selectVeranstaltung&id=" + id);
  jqxhr.success(function(data) {
    $('[class^=veranstaltung]').removeClass('bold');
    $('.veranstaltung-' + id).addClass('bold');
    $('#page-header').html(data);
  });
}


function lockRace(id) {
  var jqxhr = $.get("ajaxRequest.php?func=lockRace&lid=" + id);
  jqxhr.success(function(data) {
    //console.log(data);
    if (data == 1) {
      $('#lock-' + id).removeClass('fa-unlock');
      $('#lock-' + id).addClass('fa-lock');
    } else {
      $('#lock-' + id).removeClass('fa-lock');
      $('#lock-' + id).addClass('fa-unlock');
    }
  });
}


function submitForm(form, redirect) {
  var result = '';

  formData = new FormData();
  params = $(form).serializeArray();

  $.each(params, function(i, val) {
    formData.append(val.name, val.value);
  });

  // wenn es ein file upload gibt
  if ($('[name="uploadFile"]').length > 0) {
    files = $(form).find('[name="uploadFile"]')[0].files;

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
      if (msg != 'ok') {
        redirect = false;
        $('.alert').html(msg);
        $('.alert').removeClass('hidden');
      } else {
    	
    	// Form leeren, wenn ein Teilnehmer gespeichert wurde
        if (form == '#editTeilnehmer') {
          clearForm();
        }
        
        // SESSION Variablen für Veranstaltung neu setzen.
        if (form == '#editVeranstaltung') {
        	$.each( params, function( key, value ) {
        		if(value.name == 'id') {
        	      	  selectVeranstaltung(value.value);
        		}
        	});
        }
        
      }
      
      if (redirect) {
        window.location.href = redirect;
      }
      
    }
  })

}


function clearForm() {
  $('#stnr').val('');
  $('#vorname').val('');
  $('#nachname').val('');
  $('#verein').val('');
  $('#klasse').val(' ');
  $('#vklasse').val(' ');
  $('#jahrgang').val('');
  $('#geschlecht').prop('selectedIndex', 0);
  $('#ort').val('');
  $('#att').val('');
  $('#zeit').val('00:00:00');
  $('#useManTime').prop( "checked", false );
  $('#disq').prop( "checked", false );
  $('.selectpicker').selectpicker('refresh');
}


function addKlasseZeile(id) {
  var jqxhr = $.get('ajaxRequest.php?func=addKlasse&id=' + id);
  jqxhr.success(function(data) {
    if (data == 'ok') {
      window.location.href = 'index.php?func=klasse&id=' + id;
    }
  });
}


function deleteKlasse(id, kid) {
  var jqxhr = $.get('ajaxRequest.php?func=deleteKlasse&id=' + id);
  jqxhr.success(function(data) {
    if (data == 'ok') {
      window.location.href = 'index.php?func=klasse&id=' + kid;
    }
  });
}


function deleteManReaderTime(id) {
  var jqxhr = $.get('ajaxRequest.php?func=deleteManReaderTime&id=' + id);
  jqxhr.success(function(data) {
    if (data == 'ok') {
      window.location.href = 'index.php?func=ziel.edit';
    } else {
      $('.alert').html(data);
      $('.alert').removeClass('hidden');
    }
  });
}

function deleteFullKlasse(id) {
  var jqxhr = $.get('ajaxRequest.php?func=deleteFullKlasse&id=' + id);
  jqxhr.success(function(data) {
    if (data == 'ok') {
      window.location.href = 'index.php?func=klasse';
    } else {
      $('.alert').html(data);
      $('.alert').removeClass('hidden');
    }
  });
}


function getKlasse(jg, sex, lid) {
  var url = "ajaxRequest.php?func=getKlasse&jg=" + jg + "&sex=" + sex + "&lid=" + lid;
  $.get(url, function(data) {
    setKlasse(data);
  });
}


function setKlasse(data) {
  var klasseArray;
  klasseArray = data.split(";");

  $("#klasse").val(klasseArray[0]);
  $("#vklasse").val(klasseArray[1]);
}


function showHideRunden(id) {
  console.log(id);
  if (id > 0) {
    $('#rundenrennen').removeClass('hidden');
  } else {
    $('#rundenrennen').addClass('hidden');
  }
}


function showContent(func, param) {
  $('.content-table').load('ajaxRequest.php?func=' + func + '&id=' + param);
}


function clearContent() {
  $.get("ajaxRequest.php?func=clearRaceId");
  $('.content-table').html('');
}


function doAuswertung(id) {
  $('#modal').modal();

  var jqxhr = $.getJSON("ajaxRequest.php?func=doAuswertung&id=" + id);
  jqxhr.success(function(data) {
    //console.log(data);

    $('#modal-body').html(data.message);

    // zeige die gerade berechneten Ergebnisse
    showContent('showErgebnisse', id);
    $('#finisher-' + id).html(data.finisher);
  });

}

function checkEinlaufListe(t) {
  var id = $(t).attr("id")
  var url = 'ajaxRequest.php?func=showEinlaufListe&id=' + id

  if (t.checked) {
    action = '&action=add';
  } else {
    action = '&action=remove';
  }
  $('.content-table').load(url + action);
}


function saveManZielzeit(t, action) {
  // Encode the String
  if (action == 'save') {
    var encodedTimeString = Base64.encode($('#zeit_' + $(t).attr("id")).val());
    var getURL = 'ajaxRequest.php?func=saveManZielzeit&time=' + encodedTimeString + '&id=' + $(t).attr("id") + '&action=save';
  } else {
    var getURL = 'ajaxRequest.php?func=saveManZielzeit&time=&id=' + $(t).attr("id") + '&action=del';
  }

  var pageToLoad = 'ajaxRequest.php?func=showEinlaufListe&id=0&action=none';
  var scrollToObject = '#zeit_' + $(t).attr("id");

  var jqxhr = $.get(getURL);

  jqxhr.done(function() {
    $(".content-table").load(pageToLoad, function() {
      $('html, body').animate({
        scrollTop: $(scrollToObject).offset().top - 50
      }, 1000);
    });
  });

  return false;
}

function showZielzeitAnalyse(t) {
  var encodedStartString = Base64.encode($('#startAnalyseTime_' + $(t).attr("id")).val());
  var encodedDuratonString = Base64.encode($('#duration_' + $(t).attr("id")).val());

  $('.content-table').load('ajaxRequest.php?func=showZielAnalyse&id=' + $(t).attr("id") + '&start=' + encodedStartString + '&duration=' + encodedDuratonString);
}

function clearModal() {
  var data = '<span class="text-muted">loading...</span>';
  $('.modal-body').html(data);
}

function closeChat() {
  setCookie('openTimingLastChatID', maxChatID, 1000);
  lastChatID = maxChatID;
  //console.log(maxChatID);
  $('#chat').css("display", "none");
  $('#chat-message').html('');
}

function checkNewChatMessage() {

  if (lastChatID == null) {
    lastChatID = getCookie('openTimingLastChatID');
  }

  firstTime = false;
  if (lastChatID == "" || lastChatID == 'null') {
    lastChatID = 1;
    firstTime = true;
  }

  var jqxhr = $.getJSON("ajaxRequest.php?func=chat&id=" + lastChatID);

  jqxhr.done(function(data) {
    if (data != "") {
      $.each(data, function(key, val) {
        if (key == 'id') {
          maxChatID = val;
          if (firstTime) {
            //console.log(firstTime);
            lastChatID = val;
            setCookie('openTimingLastChatID', val, 5);
          }
        }

        if (key == 'message' && firstTime == false && maxChatID != lastChatID) {
          $('#chat').css("display", "block");
          $('#chat-message').html(val);
        }
      });
      firstTime = false;
    }
  });

  cncm = setTimeout(function() {
    checkNewChatMessage();
  }, 10000);

}

function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires=" + d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function showHelpMessage(file) {
	$( '#modal2' ).modal();
	  var jqxhr = $.get("ajaxRequest.php?func=getHelpMessage&id=" + Base64.encode(file));
	  
	  jqxhr.done(function(data) {
		  console.log( data );
		  data = data.replace(/<table>/i, "<table class=\"table table-striped table-condensed table-vcenter\">");
		  $( '#modal2-body').html( data );
	  });
	
}