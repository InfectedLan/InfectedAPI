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

function DataSource(matchId) {
    this.data = [];
    $.getJSON("../api/json/match/getMatches.php?id=" + matchId, function(data) {
	
    });
}

function Bracket(matchId, typeId) { //typeId is metadata set by bracket creator
    this.matches = [];
    this.render = function(callback) {

    }

    //Load data
}
