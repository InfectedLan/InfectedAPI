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

function DataSource(compoId) {
    this.data = [];
    this.compoId = compoId;
    this.derivedBrackets = [];
    var me = this;
    this.refresh = function() {
	$.getJSON("../api/json/match/getMatches.php?id=" + compoId, function(data) {
	    if(data.result) {
		me.data = data;
	    } else {
		error("Noe gikk galt da vi hentet match-dataen: " + data.message);
	    }
	});
	for(var i = 0; i < me.derivedBrackets.length; i++) {
	    me.derivedBrackets[i].updateData(me.data);
	}
    }
    this.derive = function(regex) {
	var bracket = new Bracket(compoId, regex);
	me.derivedBrackets.push(bracket);
	return bracket;
    }

    this.refresh();
}

function Bracket(compoId, regex) { //typeId is metadata set by bracket creator
    this.matches = [];
    this.render = function(callback) {

    }

    this.updateData = function(matchData) {
	this.matches = [];
	for(var i = 0; i < matchData.length; i++) {
	    if(matchData[i].metadata.tag != null && matchData[i].metadata.tag.match(regex) != null) {
		this.matches.push(matchData[i]);
	    }
	}
    }

    //Load data
}
