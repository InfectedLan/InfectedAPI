var seatmapData = null;
function downloadAndRenderSeatmap(target, seatHandlerFunction, callback)
{
	$.getJSON('../api/json/seatmap/seatmapAvailability.php?id=' + seatmapId, function(data){
		if(data.result)
		{
			seatmapData = data;
			renderSeatmap(target, seatHandlerFunction, callback);
		}
		else
		{
			$("#seatmapCanvas").html('<i>En feil oppstod under håndteringen av seatmappet...</i>');
		}
  	});
}
//Target is required, seatHandlerFunction isnt
function renderSeatmap(target, seatHandlerFunction, callback) {
	//Render seatmap
	$(target).html('');
	$(target).css('background-image', 'url("../api/content/seatmapBackground/' + seatmapData.backgroundImage + '")');
	for(var i = 0; i < seatmapData.rows.length; i++)
	{
		var returnData = [];

		returnData.push('<div class="row" style="top: ' + seatmapData.rows[i].y + 'px; left: ' + seatmapData.rows[i].x + 'px;" id="row' + seatmapData.rows[i].id + '">');
		for(var s = 0; s < seatmapData.rows[i].seats.length; s++)
		{
			var title = "Ledig sete";
			if(seatmapData.rows[i].seats[s].occupied)
			{
				title = 'Reservert av ' + seatmapData.rows[i].seats[s].occupiedTicket.owner;
			}
			//Run the seat handler function if set
			var customClass = seatmapData.rows[i].seats[s].occupied ? "taken" : "free";
			if(typeof seatHandlerFunction !== "undefined")
			{
				var customClassCheck = seatHandlerFunction( seatmapData.rows[i].seats[s].id, 
															'#seat' + seatmapData.rows[i].seats[s].id, 
															seatmapData.rows[i].seats[s].occupied,
															seatmapData.rows[i].seats[s].occupiedTicket);
				//Check if we got anything of use
				if(typeof seatHandlerFunction !== "undefined")
				{
					customClass = customClassCheck;
				}
			}
			returnData.push('<div title="' + title + '" class="seat ' + customClass + '" id="seat' + seatmapData.rows[i].seats[s].id + '">');
			//Push rest of stuff
			returnData.push(seatmapData.rows[i].seats[s].humanName.split(" ").join("<br />"));
			returnData.push('</div>');
		}
		returnData.push('</div>');
		$(target).append(returnData.join(""));
	}
	callback();
}