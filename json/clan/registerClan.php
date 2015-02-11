<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';

$result = false;
$message = null;
$clanId = 0;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->isEligibleForCompos()) {
		
		if (isset($_GET['name']) &&
			isset($_GET['tag']) &&
			isset($_GET['compo']) ) {
			$clanId = ClanHandler::registerClan($_GET['name'], $_GET['tag'], $_GET['compo'], $user);

			$result = true;

		} else {
			$message = "Mangler felt!";
		}
	} else {
		$message = "Du kan ikke lage en clan! Eier du en infected-billett?";
	}
} else {
	$message = 'Du er ikke logget inn.';
}

if ($result) {
	echo json_encode(array('result' => $result, 'clanId' => $clanId));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>