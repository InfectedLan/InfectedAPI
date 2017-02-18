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
require_once 'objects/compoplugin.php';
require_once 'objects/match.php';
class CsgoToornamentPlugin extends CompoPlugin {
    public function getCustomMatchInformation(Match $match) { //Called for each current match on the crew page
        if($match->getState() == Match::STATE_CUSTOM_PREGAME) {
            $options = VoteOptionHandler::getVoteOptionsByCompo($match->getCompo());
            $votedCount = 0;
            foreach($options as $option) {
                if(VoteOptionHandler::isVoted($option, $match)) {
                    $votedCount++;
                }
            }
            return ["Maps banned" => $votedCount . " av " . count($options), "Connection details" => $match->getConnectDetails()];
        } else if($match->getState() == Match::STATE_JOIN_GAME) {
            $options = VoteOptionHandler::getVoteOptionsByCompo($match->getCompo());
            $votedCount = 0;
            foreach($options as $option) {
                if(!VoteOptionHandler::isVoted($option, $match)) {
                    return ["Selected map" => $option->getName(), "Connection details" => $match->getConnectDetails()];
                }
            }
        } else {
	    return ["Map vote status" => "Venter på vote", "Connection details" => $match->getConnectDetails()];
	}
        return null;
    }
    public function hasVoteScreen() {
	return true;
    }
    public function getTurnArray(Match $match) {
	if($match->getId() == 226/* || $match->getId() == 225 || $match->getId() == 221*/) {
	    return [0, 1, 1, 0, 1, 0, 2];
	}
	return [0, 1, 0, 1, 1, 0, 2];
    }
    public function getTurnMask(Match $match) {
	if($match->getId() == 226/* || $match->getId() == 225 || $match->getId() == 221*/) {
	    return [0, 0, 1, 1, 0, 0, 2];
	}
	return [0, 0, 0, 0, 0, 0, 2];
    }
    public function onMatchFinished(Match $match) {

    }
    public function getToornamentOauthToken() {
	$curlSess = curl_init();
	curl_setopt($curlSess, CURLOPT_URL,"https://api.toornament.com/oauth/v2/token");
	curl_setopt($curlSess, CURLOPT_POST, 1);
	curl_setopt($curlSess, CURLOPT_POSTFIELDS, "grant_type=client_credentials&client_id=" . Secret::toornamentClientId . "&client_secret=" . Secret::toornamentClientSecret);
	curl_setopt($curlSess, CURLOPT_RETURNTRANSFER, true);

	$output = curl_exec($curlSess);

	curl_close($curlSess);

	return json_decode($output)->access_token;
    }
    public function getToornamentParticipantData(Clan $clan) {
	$clanData = [];
	$clanData["name"] = $clan->getName();
	$compo = CompoHandler::getCompoByClan($clan);

	$lineup = [];
	$members = ClanHandler::getPlayingClanMembers($clan);
	foreach($members as $member) {
	    $memberData = [];
	    $memberData["name"] = $member->getNickname();
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