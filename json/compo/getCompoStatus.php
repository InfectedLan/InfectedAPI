<?php
/**
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

require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/invitehandler.php';
require_once 'handlers/matchhandler.php';

$result = false;
$message = null;
$compoStatusArray = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	$clanList = array();
	$inviteList = array();
	
	foreach (ClanHandler::getClansByUser($user) as $clan) {
		$compo = $clan->getCompo();

		$compoData = array('name' => $compo->getName(), 
						   'tag' => $compo->getTag());

		$clanData = array('id' => $clan->getId(), 
						  'name' => $clan->getName(), 
						  'tag' => $clan->getTag(), 
						  'compo' => $compoData);

		array_push($clanList, $clanData);
	}

	foreach (InviteHandler::getInvitesByUser($user) as $invite) {
		$clan = $invite->getClan();
		$compo = $clan->getCompo();

		$compoData = array('name' => $compo->getName(), 
						   'tag' => $compo->getTag());

		$invitedClanData = array('name' => $clan->getName(), 
								 'tag' => $clan->getTag(), 
								 'id' => $clan->getId());

		$inviteData = array('id' => $invite->getId(), 
						    'compo' => $compoData, 
						    'clanData' => $invitedClanData);

		array_push($inviteList, $inviteData);
	}

	$compoStatusList = array('clans' => $clanList, 
							  'invites' => $inviteList);

	$match = MatchHandler::getMatchByUser($user);

	if ($match != null) {
		$compoStatusList['match'] = array('id' => $match->getId());
	}

	$result = true;
	
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

if ($result) {
	echo json_encode(array('result' => $result, 'data' => $compoStatusList));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>