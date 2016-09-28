$(document).ready(function() {
    $("#toornamentSendBtn").on('click', function() {
	//alert("sending: " + '../api/json/compo/toornamentExport.php?' + $("#toornamentForm").serialize());
	$("#toornamentSendArea").fadeOut(300, function() {
	    $("#toornamentLoadingArea").fadeIn(300);
	});
	$.getJSON('../api/json/compo/toornamentExport.php?' + $("#toornamentForm").serialize(), function(data){
	    if(data.result == true) {
		info("Success!");
		$("#toornamentSendArea").fadeIn(300);
	    } else {
		error(data.message);
	    }
	});
    });
});
