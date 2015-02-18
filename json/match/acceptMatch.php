<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/matchhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_GET['id'])) {
		$match = MatchHandler::getMatch($_GET['id']);
		
		if ($match != null) {
			if ($match->isParticipant($user)) {
				MatchHandler::acceptMatch($user, $match);
				
				if (MatchHandler::allHasAccepted($match)) {
					if ($match->getCompo()->getId() == 3) {
						$match->setState(1);
					} else {
						$match->setState(2);
					}
				}

				$result = true;
			} else {
				$message = '<p>Du er ikke med i denne matchen!</p>';
			}
		} else {
			$message = '<p>Denne matchen finnes ikke.</p>';
		}
	} else {
		$message = '<p>Vi mangler felt.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>