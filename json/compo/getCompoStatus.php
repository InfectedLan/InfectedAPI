<?php
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