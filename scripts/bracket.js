/*
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

var bracketList = [];
//A few settings...
var bracketHeight = 47;
var bracketHeightMargin = 20;
var bracketTopExtraMargin = 35;

var bracketWidth = 110;
var bracketWidthMargin = 20;

var participant_type_clan = 0;
var participant_type_winner = 1;
var participant_type_looser = 2;
var participant_type_stepin = 3;

function DataSource(compoId) {
    this.data = [];
    this.clans = [];
    this.compoId = compoId;
    this.derivedBrackets = [];
    var me = this;
    this.refresh = function() {
	var progress = 0;
	$.getJSON("../api/json/clan/getClans.php?id=" + compoId, function(data){
	    if(data.result) {
		progress++;
		me.clans = data.data;
		if(progress>1) {
		    for(var i = 0; i < me.derivedBrackets.length; i++) {
			me.derivedBrackets[i].updateData(me.data, me.clans);
		    }
		}
	    } else {
		error(data.message);
	    }
	});
	$.getJSON("../api/json/match/getMatches.php?id=" + compoId, function(data) {
	    if(data.result) {
		progress++;
		me.data = data.data;
		if(progress>1) {
		    for(var i = 0; i < me.derivedBrackets.length; i++) {
			me.derivedBrackets[i].updateData(me.data, me.clans);
		    }
		}
	    } else {
		error("Noe gikk galt da vi hentet match-dataen: " + data.message);
	    }
	});
    }
    this.derive = function(divId, regex, bracketWidth, bracketHeight, customRenderer, onRenderFinished, onOffsetRender,  onOffsetHeaderRender, customTimeFormatter) {
	var bracket = new Bracket(compoId, divId, regex, bracketWidth, bracketHeight, customRenderer, onRenderFinished, onOffsetRender, onOffsetHeaderRender, customTimeFormatter);
	bracket.updateData(this.data);
	this.derivedBrackets.push(bracket);
	return bracket;
    }

    this.refresh();
}

function Bracket(compoId, divId, regex, bracketWidth, bracketHeight, customRenderer, onRenderFinished, onOffsetRender, onOffsetHeaderRender, customTimeFormatter) { //compoId is metadata set by bracket creator
    
    this.getParticipantString = function(participant, clans) {
	if(participant.type == participant_type_clan) {
	    for(var i = 0; i < clans.length; i++) {
		if(clans[i].id == participant.participantId) {
		    return clans[i].tag;
		}
	    }
	} else if(participant.type == participant_type_winner) {
	    return "TBD";
	} else if(participant.type == participant_type_looser) {
	    return "TBD";
	} else if(participant.type == participant_type_walkover) {
	    return "Walkover";
	}
    }
    this.bracketWidth = typeof bracketWidth !== 'undefined' ? bracketWidth : 110;
    this.bracketHeight = typeof bracketHeight !== 'undefined' ? bracketHeight : 47;
    this.customRenderer = typeof customRenderer === 'function' ? customRenderer : function(match, clans) {
	var html = [];
	//html.push('<div class="bracket_header">Match id ' + match.id + '</div>');
	
	for(var i = 0; i < match.participants.length; i++) {
	    html.push('<div class="bracket_participant">' +  this.getParticipantString(match.participants[i], clans) +'</div>');
	}
	return html.join("");
    };
    this.onRenderFinished = typeof onRenderFinished === 'function' ? onRenderFinished : function() {

    };
    this.customTimeFormatter = typeof customTimeFormatter === 'function' ? customTimeFormatter : function(time, offsetId) {
	return time;
    };
    this.onOffsetRender = typeof onOffsetRender === 'function' ? onOffsetRender : function() {

    };
    this.onOffsetHeaderRender = typeof onOffsetHeaderRender === 'function' ? onOffsetHeaderRender : function() {

    };
    
    
    this.divId = divId;
    
    this.matches = [];
    this.clans = [];

    this.render = function() {
	//For this, we will use a three part strategy:
	//1. Sort by bracket offset
	//2. Iterate from final match and backwards, sort by what child uses it
	//3. Iterate from start and forwards, find position relative to parents.
	var offsets = [];

	for(var i = 0; i < this.matches.length; i++) {
	    var offset = this.matches[i].bracketOffset;
	    var foundOffset = false;
	    console.log("Looking for offset " + offset);
	    for(var x = 0; x < offsets.length; x++) {
		if(offsets[x].offset == offset) {
		    console.log("Found offset!");
		    offsets[x].items.push(this.matches[i]);
		    foundOffset = true;
		    break;
		}
	    }
	    if(!foundOffset) {
		console.log("Did not find offset. Looking for where to insert...");
		var positionTarget = offsets.length;
		for(var x = 0; x < offsets.length; x++) {
		    if(offsets[x].offset > offset) {
			console.log("Insert position: " + x);
			positionTarget = x;
			break;
		    }
		}
		offsets.splice(positionTarget, 0, {offset: offset, scheduledTime: this.matches[i].scheduledTime, items: [this.matches[i]]});
	    }
	}

	console.log("Generated offset table: ");
	console.log(offsets);

	//Iterate from final match and back
	var priorityTable = []; //FIFO table that prioritizes position(Sorting 101!)
	var newOffsets = [];
	//Initialize empty new offset table
	for(var i = 0; i < offsets.length; i++) {
	    newOffsets.push({offset: offsets[i].offset, scheduledTime: offsets[i].scheduledTime, items: []});
	}
	//Iterate forwards to sort stuff.
	for(var i = 0; i < offsets.length; i++) {
	    var nonSorted = [];
	    for(var x = 0; x < offsets[i].items.length; x++) {
		var item = offsets[i].items[x];
		var wasPriorityMatch = false;
		for(var y = 0; y < priorityTable.length; y++) {
		    if(priorityTable[y] == item.id) {
			wasPriorityMatch = true;
			newOffsets[i].items.push(item);
			priorityTable.splice(y, 1);
			for(var z = 0; z < item.children.length; z++) {
			    priorityTable.push(item.children[z]);
			}
			break;
		    }
		}
		if(!wasPriorityMatch) {
		    nonSorted.push(item);
		}
	    }
	    //Add all non sorted items to the new offset list
	    for(var x = 0; x < nonSorted.length; x++) {
		var item = nonSorted[x];
		newOffsets[i].items.push(item);
		for(var z = 0; z < item.children.length; z++) {
		    priorityTable.push(item.children[z]);
		}
	    }
	}
	//This should be the complete sorted list
	console.log("Sorted matches. Got this new list: ");
	console.log(newOffsets);
	offsets = newOffsets;

	//Now, for the fun stuff. Iterate forwards, and set the y coordinate
	for(var i = 0; i < offsets.length; i++) {
	    var lastPosition = 0;
	    for(var x = 0; x < offsets[i].items.length; x++) {
		var item = offsets[i].items[x];
		if(item.parents.length > 0) { //These should be first in the array if we did our job properly
		    //Find each parent. Has to be at offset smaller then us.
		    var parentYTotal = 0;
		    var parentCount = 0;
		    for(var y = 0; y < i; y++) {
			for(var z = 0; z < offsets[y].items.length; z++) {
			    for(var a = 0; a < item.parents.length; a++) {
				if(offsets[y].items[z].id == item.parents[a]) {
				    parentYTotal += offsets[y].items[z].y;
				    parentCount++;
				}
			    }
			}
		    }
		    //Did we find any parents?
		    if(parentCount == 0) {
			item.y = lastPosition;
			lastPosition += (this.bracketHeight*1.2);
		    } else {
			item.y = parentYTotal/parentCount;
			lastPosition = item.y + (this.bracketHeight*1.2);
		    }
		} else {
		    item.y = lastPosition;
		    lastPosition += (this.bracketHeight*1.2);
		}
	    }
	}
	console.log("Finished generating y coordinates");
	console.log(offsets);

	//Render!
	var html = [];
	for(var i = 0; i < offsets.length; i++) {
	    html.push('<div class="bracketOffset" width="' + (this.bracketWidth*1.2) + '">');
	    html.push('<div class="bracketOffsetHeader">');
	    var date = new Date(offsets[i].scheduledTime*1000);
	    html.push(this.customTimeFormatter(date.toLocaleString(), i));
	    html.push('</div>');
	    for(var x = 0; x < offsets[i].items.length; x++) {
		var yMargin = 0;
		if(x != 0) {
		    yMargin = offsets[i].items[x].y - offsets[i].items[x-1].y - this.bracketHeight;
		} else {
		    yMargin = offsets[i].items[x].y;
		}
		html.push('<div class="bracket" style="margin-top: ' + yMargin + 'px; width: ' +this.bracketWidth  + 'px; height: ' + this.bracketHeight + 'px;">');
		html.push(this.customRenderer(offsets[i].items[x], this.clans));
		html.push('</div>');
	    }
	    html.push(this.onOffsetRender(offsets, i));
	    html.push('</div>');
	}
	console.log(html.join(""));
	$("#" + this.divId).html(html.join(""));
	this.onRenderFinished(offsets);
	this.generatedOffsets = offsets;
    }

    this.updateData = function(matchData, clanData) {
	this.matches = [];
	this.clans = clanData;
	for(var i = 0; i < matchData.length; i++) {
	    console.log("Iteration " + i );
	    console.log(matchData[i]);
	    if( (typeof matchData[i].metadata.tag === 'undefined' && " ".match(regex)) || matchData[i].metadata.tag.match(regex) != null) {
		this.matches.push(matchData[i]);
	    }
	}

	this.render();
    }

    //Load data
}
