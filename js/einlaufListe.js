$(document).ready(function(){
    $('.setmanzeit').click(function(){
    	
    	// Encode the String
    	var encodedTimeString = Base64.encode($('#zeit_' + $(this).attr("id")).val());
    	var getURL = 'index.php?' + $(this).attr("href") + '&time=' + encodedTimeString;
    	
		var pageToLoad = 'index.php?jqRequest&func=showEinlaufListe&lid=0&action=none';
		var scrollTo = '#zeit_' + $(this).attr("id");
    			
		var jqxhr = $.get( getURL );

		jqxhr.done( function() {
			$("#data_div").load(pageToLoad, function() {
				$('html, body').animate({
					scrollTop: $(scrollTo).offset().top
					}, 100);
				});
			});

		return false;
    });
});

