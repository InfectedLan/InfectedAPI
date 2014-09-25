<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/matchhandler.php';
require_once 'objects/match.php';

$result = false;
$message = null;
$matchData = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if(isset($_GET['id'])) {
		$match = MatchHandler::getMatch($_GET['id']);

		if($match->isParticipant($user)|| $user->hasPermission('*') || $user->hasPermission('functions.compoadmin')) {
			$matchData['state'] = $match->getState();
			$matchData['ready'] = $match->isReady();
			$matchData['currentTime'] = time(); //Used for synchronizing time
			$matchData['startTime'] = $match->getScheduledTime();
			if($match->getState() == Match::STATE_READYCHECK && $match->isReady()) {
				$readyData = array();

				$participants = MatchHandler::getParticipants($match);

				foreach($participants as $clan) {
					$members = $clan->getMembers();

					$clanData = array();
					$clanData['clanName'] = $clan->getName();
					$clanData['clanTag'] = $clan->getTag();

					foreach($members as $member) {
						$memberReadyStatus = array();

						$memberReadyStatus['userId'] = $member->getId();
						$memberReadyStatus['userDisplayName'] = $member->getDisplayName();

						$avatarFile = null;		
						if ($user->hasValidAvatar()) {
							$avatarFile = $user->getAvatar()->getThumbnail();
						} else {
							$avatarFile = AvatarHandler::getDefaultAvatar($user);
						}

						$memberReadyStatus['avatarUrl'] = $avatarFile;
						$memberReadyStatus['ready'] = MatchHandler::isUserReady($member, $match);

						$clanData['members'] = $memberReadyStatus;
					}

					array_push($readyData, $clanData);
				}

				$matchData['readyData'] = $readyData;
				$result = true;
			}
		} else {
			$message = "Du har ikke lov til å se på denne matchen!";
		}
	} else {
		$message = "Mangler felt!";
	}
} else {
	$message = 'Du er ikke logget inn.';
}

if($result) {
	echo json_encode(array('result' => $result, 'matchData' => $matchData));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>