<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'localization.php';
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
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result, 'data' => $compoStatusList), JSON_PRETTY_PRINT);
} else {
	echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
}
?>