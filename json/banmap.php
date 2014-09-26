<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/votehandler.php';
require_once 'handlers/voteoptionhandler.php';

$result = false;
$message = null;
$clanId = 0;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if(isset($_GET['id']) && isset($_GET['matchId']) ) {
		$match = MatchHandler::getMatch($_GET['matchId']);
		if(isset($match)) {
			$numBanned = VoteHandler::getNumBanned($match->getId());
			$turn = VoteHandler::getCurrentBanner($numBanned);
			if($turn != 2) {
				$participants = MatchHandler::getParticipants($match);
				$clan = $participants[$turn];
				if($clan->getChief() == $user->getId()) {
					$voteOption = VoteOptionHandler::getVoteOption($_GET['id']);
					if(isset($voteOption)) {
						if($voteOption->getCompoId() == $match->getCompoId()) {
							VoteHandler::banMap($voteOption, $match->getId());
							$result = true;
						} else {
							$message = "Dette mappet er ikke for denne compoen!";
						}
					} else {
						$message = "Mappet finnes ikke!";
					}
				} else {
					$message = "Du har ikke lov til å banne nå!";
				}
			} else {
				$message = "Matchen holder på å starte!";
			}
		} else {
			$message = "Matchen finnes ikke";
		}
	} else {
		$message = "Felt mangler!";
	}
} else {
	$message = 'Du er ikke logget inn.';
}

if($result) {
	echo json_encode(array('result' => $result, 'clanId' => $clanId));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>