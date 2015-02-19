<?php
require_once 'session.php';
require_once 'handlers/componhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;
$clanId = 0;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->isEligibleForCompos()) {
		if (isset($_GET['name']) &&
			isset($_GET['tag']) &&
			isset($_GET['compo'])) {
			$compo = CompoHandler::getCompo($_GET['compo']);

			if ($compo != null) {
				$clan = ClanHandler::createClan(EventHandler::getCurrentEvent(), $_GET['name'], $_GET['tag'], $compo, $user);
				$result = true;
			} else {
				$message = '<p>Compo\'en finnes ikke.</p>';
			}
		} else {
			$message = '<p>Mangler felt!</p>';
		}
	} else {
		$message = '<p>Du kan ikke lage en clan! Har du en billett?</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

if ($result) {
	echo json_encode(array('result' => $result, 'clanId' => $clan->getId()));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>