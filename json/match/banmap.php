<?php
require_once 'session.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/votehandler.php';
require_once 'handlers/voteoptionhandler.php';

$result = false;
$message = null;
$clanId = 0;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id']) && isset($_GET['matchId']) ) {
		$match = MatchHandler::getMatch($_GET['matchId']);
		
		if (isset($match)) {
			$numBanned = VoteHandler::getNumBanned($match->getId());
			$turn = VoteHandler::getCurrentBanner($numBanned);
			
			if ($turn != 2) {
				$participants = MatchHandler::getParticipants($match);
				$clan = $participants[$turn];
				
				if ($user->equals($clan->getChief())) {
					$voteOption = VoteOptionHandler::getVoteOption($_GET['id']);
					
					if ($voteOption != null) {
						if ($voteOption->getCompo()->equals($match->getCompo())) {
							VoteHandler::banMap($voteOption, $match->getId());
							//Check if state should be switched
							$numBanned = VoteHandler::getNumBanned($match->getId());
							
							if ($numBanned == 6) {
								$match->setState(2);
							}

							$result = true;
						} else {
							$message = 'Dette mappet er ikke for denne compoen!';
						}
					} else {
						$message = 'Mappet finnes ikke!';
					}
				} else {
					$message = 'Du har ikke lov til å banne nå!';
				}
			} else {
				$message = 'Matchen holder på å starte!';
			}
		} else {
			$message = 'Matchen finnes ikke.';
		}
	} else {
		$message = 'Felt mangler!';
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