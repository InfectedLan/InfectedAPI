<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/invitehandler.php';

$result = false;
$message = null;
$compoStatusArray = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	$clanArray = array();

	$clans = ClanHandler::getClansForUser($user);

	foreach($clans as $clan) {
		$compo = ClanHandler::getCompo($clan);
		$compoData = array("name" => $compo->getName(), "tag" => $compo->getTag());
		$clanData = array("id" => $clan->getId(), "name" => $clan->getName(), "tag" => $clan->getTag(), "compo" => $compoData);
		array_push($clanArray, $clanData);
	}

	$inviteArray = array();
	$invites = InviteHandler::getInvitesForUser($user);

	foreach($invites as $invite) {
		$clan = ClanHandler::getClan($invite->getClanId());
		$compo = ClanHandler::getCompo($clan);
		$compoData = array("name" => $compo->getName(), "tag" => $compo->getTag());
		$invitedClanData = array("name" => $clan->getName(), "tag" => $clan->getTag(), "id" => $clan->getId());

		$inviteData = array('id' => $invite->getId(), 'compo' => $compoData, 'clanData' => $invitedClanData);
		array_push($inviteArray, $inviteData);
	}

	$compoStatusArray = array('clans' => $clanArray, 'invites' => $inviteArray);

	$result = true;
	
} else {
	$message = 'Du er ikke logget inn.';
}

if($result) {
	echo json_encode(array('result' => $result, 'data' => $compoStatusArray));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>