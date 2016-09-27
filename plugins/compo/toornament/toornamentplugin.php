<?php
/*
* This file is part of InfectedCrew.
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
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
*/
require_once 'handlers/matchhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/compohandler.php';
class ToornamentPlugin {
    public function getCustomMatchInformation(Match $match) { //Called for each current match on the crew page
        return null;
    }
    public function hasVoteScreen() {
	return false;
    }
    public function getTurnArray(Match $match) {
	return [0, 1, 0, 1, 1, 0, 2];
    }
    public function getTurnMask(Match $match) {
	return [0, 0, 0, 0, 0, 0, 2];
    }
    public function onMatchFinished(Match $match) {
	
    }
    public function getToornamentParticipantData(Clan $clan) {
	$clanData = [];
	$clanData["name"] = $clan->getName();
	$compo = CompoHandler::getCompoByClan($clan);

	$lineup = [];
	$members = ClanHandler::getPlayingClanMembers($clan);
	foreach($members as $member) {
	    $memberData = [];
	    $memberData["name"] = $member->getCompoDisplayName();
	    if($compo->requiresSteamId()) {
		$privateFields = [];
		$steamIdField = [];
		$steamIdField["type"] = "steam_player_id";
		$steamIdField["label"] = "Steam ID";
		$steamIdField["value"] = $member->getSteamId();
		$privateFields[] = $steamIdField;
		$memberData["custom_fields_private"] = $privateFields;
	    }
	    
	    $lineup[] = $memberData;
	}
	$clanData["lineup"] = $lineup;
	
	return $clanData;
    }
}
?>