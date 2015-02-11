<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/matchhandler.php';
require_once 'objects/match.php';
require_once 'handlers/votehandler.php';
require_once 'handlers/voteoptionhandler.php';

$result = false;
$message = null;
$matchData = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if(isset($_GET['id'])) {
		$match = MatchHandler::getMatch($_GET['id']);

		if ($user->hasPermission('*') ||
			$user->hasPermission('event.compo') ||
			$match->isParticipant($user)) {
			$matchData['state'] = $match->getState();
			$matchData['ready'] = $match->isReady();
			$matchData['compoId'] = $match->getCompoId();
			$matchData['currentTime'] = time(); //Used for synchronizing time
			$matchData['startTime'] = $match->getScheduledTime();
			$matchData['chatId'] = $match->getChat();
			
			if ($match->getState() == Match::STATE_READYCHECK && $match->isReady()) {
				$readyData = array();

				$participants = MatchHandler::getParticipants($match);

				foreach($participants as $clan) {
					$members = $clan->getMembers();

					$clanData = array();
					$clanData['clanName'] = $clan->getName();
					$clanData['clanTag'] = $clan->getTag();

					$memberData = array();

					foreach($members as $member) {
						$memberReadyStatus = array();

						$memberReadyStatus['userId'] = $member->getId();
						$memberReadyStatus['nick'] = $member->getNickname();

						$avatarFile = null;		
						if ($member->hasValidAvatar()) {
							$avatarFile = $member->getAvatar()->getThumbnail();
						} else {
							$avatarFile = AvatarHandler::getDefaultAvatar($member);
						}

						$memberReadyStatus['avatarUrl'] = $avatarFile;
						$memberReadyStatus['ready'] = MatchHandler::isUserReady($member, $match);

						array_push($memberData, $memberReadyStatus);
					}

					$clanData['members'] = $memberData;

					array_push($readyData, $clanData);
				}

				$matchData['readyData'] = $readyData;
				$result = true;
			} else if($match->getState() == Match::STATE_CUSTOM_PREGAME && $match->isReady()) {
				//As of now, it is safe to assume only CS:GO sees this.
				$voteOptions = VoteOptionHandler::getVoteOptionsForCompo(CompoHandler::getCompo($match->getCompoId()));
				$participants = MatchHandler::getParticipants($match);

				$banData = array();
				$bannableMapsArray = array();

				foreach($voteOptions as $voteOption) {
					$optionData = array();
					$optionData['name'] = $voteOption->getName();
					$optionData['thumbnailUrl'] = $voteOption->getThumbnailUrl();
					$optionData['id'] = $voteOption->getId();
					$optionData['isBanned'] = VoteOptionHandler::isVoted($voteOption, $match);
					array_push($bannableMapsArray, $optionData);
				}

				$banData['options'] = $bannableMapsArray;
				$numBanned = VoteHandler::getNumBanned($match->getId());
				$banData['turn'] = VoteHandler::getCurrentBanner($numBanned);

				$clanArray = array();

				foreach($participants as $clan) {
					$members = $clan->getMembers();

					$clanData = array();
					$clanData['clanName'] = $clan->getName();
					$clanData['clanTag'] = $clan->getTag();

					$memberData = array();

					foreach($members as $member) {
						$userData = array();

						$userData['userId'] = $member->getId();
						$userData['nick'] = $member->getNickname();
						$userData['chief'] = ($member->getId() == $clan->getChief());

						array_push($memberData, $userData);
					}

					$clanData['members'] = $memberData;

					array_push($clanArray, $clanData);
				}

				$banData['clans'] = $clanArray;

				$matchData['banData'] = $banData;
				$result = true;
			} else if($match->getState() == Match::STATE_JOIN_GAME && $match->isReady()) {
				$gameData = array();
				$gameData['connectDetails'] = $match->getConnectDetails();

				$clanArray = array();
				$participants = MatchHandler::getParticipants($match);

				foreach($participants as $clan) {
					$members = $clan->getMembers();

					$clanData = array();
					$clanData['clanName'] = $clan->getName();
					$clanData['clanTag'] = $clan->getTag();

					$memberData = array();

					foreach($members as $member) {
						$userData = array();

						$userData['userId'] = $member->getId();
						$userData['nick'] = $member->getNickname();
						$userData['chief'] = ($member->getId() == $clan->getChief());

						array_push($memberData, $userData);
					}

					$clanData['members'] = $memberData;

					array_push($clanArray, $clanData);
				}

				$gameData['clans'] = $clanArray;
				//Get map+
				$compo = CompoHandler::getCompo($match->getCompoId());
				if($compo->getId() == 1) { //Only CS:GO
					
					$options = VoteOptionHandler::getVoteOptionsForCompo($compo);
					foreach($options as $option) {
						if(!VoteOptionHandler::isVoted($option, $match)) {
							$mapData = array();

							$mapData['name'] = $option->getName();
							$mapData['thumbnail'] = $option->getThumbnailUrl();

							$gameData['mapData'] = $mapData;
							break;
						}
					}
				}

				$matchData['gameData'] = $gameData;
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