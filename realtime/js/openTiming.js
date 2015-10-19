$(document).ready(function(){
    $('a#showInDiv').click(function(){
		// das link-ziel jeweils auslesen & zwischenspeichern
		var pageToLoad = 'index.php?' + $(this).attr("href");
 		
		// dem div 'content' den inhalt der aufgerufenen seite zuweisen
		$("#data_div").load(pageToLoad);
 
		// wichtig! sonst wird der a-link im browser aufgerufen!
		return false;
    });
});

function clearDiv() {
	document.getElementById('data_div').innerHTML = '';
}



$(document).ready(function(){
    $('[id^=lock_href]').click(function(){
		var url = 'index.php?' + $(this).attr("href");
		$.get( url, function(data){
			setLock(data);
		});
		return false;
    });
});

function setLock(data) {
	var lockArray;
	lockArray = data.split(";");

	if (lockArray[0] == 0) {
		jQuery("#lock_img_" + lockArray[1]).attr("src", "img/offen.png" );
		jQuery("#lock_img_" + lockArray[1]).attr("alt", "offen" );
		jQuery("#lock_href_" + lockArray[1]).attr("href", "jqRequest&func=lockRace&lid=" + lockArray[1] + "&lock=1");
	} else {
		jQuery("#lock_img_" + lockArray[1]).attr("src", "img/geschlossen.png" );
		jQuery("#lock_img_" + lockArray[1]).attr("alt", "geschlossen" );
		jQuery("#lock_href_" + lockArray[1]).attr("href", "jqRequest&func=lockRace&lid=" + lockArray[1] + "&lock=0");
	}	
}