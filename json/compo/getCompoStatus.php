<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/invitehandler.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;
$compoStatusArray = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	$event = EventHandler::getCurrentEvent();

	//List clans
	$clanArray = array();
	$clanList = ClanHandler::getClansForUser($user, $event);

	foreach ($clanList as $clan) {
		$compo = ClanHandler::getCompo($clan);
		$compoData = array('name' => $compo->getName(), 
						   'tag' => $compo->getTag());

		$clanData = array('id' => $clan->getId(), 
						  'name' => $clan->getName(), 
						  'tag' => $clan->getTag(), 
						  'compo' => $compoData);

		array_push($clanArray, $clanData);
	}

	//List invites
	$inviteArray = array();
	$inviteList = InviteHandler::getInvitesForUser($user);

	foreach ($inviteList as $invite) {
		$clan = ClanHandler::getClan($invite->getClanId());
		$compo = ClanHandler::getCompo($clan);
		$compoData = array('name' => $compo->getName(), 
						   'tag' => $compo->getTag());

		$invitedClanData = array('name' => $clan->getName(), 
								 'tag' => $clan->getTag(), 
								 'id' => $clan->getId());

		$inviteData = array('id' => $invite->getId(), 
						    'compo' => $compoData, 
						    'clanData' => $invitedClanData);

		array_push($inviteArray, $inviteData);
	}

	$compoStatusArray = array('clans' => $clanArray, 
							  'invites' => $inviteArray);

	//Match
	$match = MatchHandler::getMatchForUser($user, $event);

	if ($match != null) {
		$compoStatusArray['match'] = array('id' => $match->getId());
	}

	$result = true;
	
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

if ($result) {
	echo json_encode(array('result' => $result, 'data' => $compoStatusArray));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>