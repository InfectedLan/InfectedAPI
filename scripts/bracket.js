var bracketList = [];
//A few settings...
var bracketHeight = 200;
var bracketHeightMargin = 20;
var bracketTopExtraMargin = 50;

var bracketWidth = 300;
var bracketWidthMargin = 20;

$(document).ready(function(){
	setInterval(1000*60, updateBrackets);
});

function createBracketRenderer(divId, compoId) {
	bracketList.push({"divId": divId, "compoId": compoId});
	$("#" + divId).html('<div class="winnersBracket"></div><div class="loosersBracket"></div>');
	console.log("Creating bracket renderer for " + divId + ", compo: " + compoId);
}

function updateBrackets() {
	for(var i = 0; i < bracketList.length; i++) {
		if($("#" + bracketList[i].divId).length == 0) {
			console.log("Bracket in div " + bracketList[i].divId + " is gone! Removing...");
			bracketList.splice(i, 1);
			i--;
		} else {
			$.getJSON('../api/json/compo/getMatchesForCompo.php?id=' + bracketList[i].compoId, (function() {
				var currentBracket = bracketList[i];
				return function(data) {
					//This script assumes a few things for simplicity:
					// * Matches are ordered based on time
					// * Matches are ordered based on parent ordering
					if(data.result == true) {
						//Count offset we are currently on
						var currentOffset = 1;
						var offsetCount = 0;
						var cachedParentYPositions = [[], []];
						for(var x = 0; x < data.data.length; x++) {
							//This is how we get how many we have pushed for the current offset
							if(data.data[x].bracketOffset != currentOffset) {
								currentOffset = data.data[x].bracketOffset;
								offsetCount = 0;
								$("#" + currentBracket.divId).find("." + (data.data[x].bracket == 1 ? "winnersBracket" : "loosersBracket")).append(
									'<div class="bracket_time">' + (data.data[x].bracket == 1 ? "HB runde " : "LB runde ") + (data.data[x].bracketOffset+1) + 
									'<br />' + data.data[x].startTime + 
									'</div>'
								);
							} else {
								offsetCount++;
							}
							//Find positions
							var xPos = ( data.data[x].bracketOffset * (bracketWidth + bracketWidthMargin) ) + bracketWidthMargin;
							var yPos = 0;
							if(data.data[x].parents.length == 0) { //If we are first iteration, we don't have any parents to check position to
								yPos = ( offsetCount * (bracketHeight + bracketHeightMargin) ) + bracketHeightMargin + ( Math.floor( offsetCount/2) * bracketHeightMargin ) + bracketTopExtraMargin;
							} else {
								//Find y pos based on parent data
								var numParents = 0;
								for(var y = 0; y < data.data[x].parents.length; y++) {
									//Search cache for prior entries
									for(var z = 0; z < cachedParentYPositions[data.data[x].bracket].length; z++) {
										if(cachedParentYPositions[data.data[x].bracket][z].matchId == data.data[x].parents[y]) {
											yPos += cachedParentYPositions[data.data[x].bracket][z].matchY;
											numParents++;
											break;
										}
									}
								}
								yPos = yPos / numParents;
							}
							//Cache ypos for later use
							cachedParentYPositions[data.data[x].bracket].push({"matchId": data.data[x].matchId, "matchY": yPos});
							//Spawn a div
							$("#" + currentBracket.divId).find("." + (data.data[x].bracket == 1 ? "winnersBracket" : "loosersBracket")).append(
								'<div class="bracket" style="top: ' +  yPos + 'px; left: ' + xPos + 'px;">' + 
									'<div class="bracket_title">Matchid: ' + data.data[x].matchId + '</div>' + 
									'<div class="bracket_participant">' +  data.data[x].participants[0] +'</div>' + 
									'<div class="bracket_participant">' +  data.data[x].participants[1] +'</div>' + 
								'</div>';
							);
						}
					} else {
						error("Det skjedde en feil under henting av brackets: " + data.message;)
					}
				};
			})();
		}
	}
}