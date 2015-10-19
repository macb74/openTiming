$(document).ready(function(){
	// show content div
    $('a#showInDiv').click(function(){
		// das link-ziel jeweils auslesen & zwischenspeichern
		var pageToLoad = 'index.php?' + $(this).attr("href");
 		
		// dem div 'content' den inhalt der aufgerufenen seite zuweisen
		$("#data_div").load(pageToLoad);
 
		// wichtig! sonst wird der a-link im browser aufgerufen!
		return false;
    });
    
    
    // on checkbox
    $('.chkboxtable').change(
    	    function(){
    	        if (this.checked) {
    	            ///alert('checked');
    	            var pageToLoad = 'index.php?' + $(this).attr("value") + '&action=add';
    	        } else {
    	            var pageToLoad = 'index.php?' + $(this).attr("value") + '&action=remove';   	        	
    	        }
    	        $("#data_div").load(pageToLoad);
    });
    
    
    $(".manzeit").click(function(){
    	alert('checked');
//    	var pageToLoad = 'index.php?' + $(this).attr("href");
//    	$("#data_div").load(pageToLoad);       
    	return false;
    });
    
    
    // Lock Race Button
    $('[id^=lock_href]').click(function(){
		var url = 'index.php?' + $(this).attr("href");
		$.get( url, function(data){
			setLock(data);
		});
		return false;
    });
    
});

function clearDiv() {
	document.getElementById('data_div').innerHTML = '';
}


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

