<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'database.php';
require_once 'localization.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/avatarhandler.php';
require_once 'handlers/voteoptionhandler.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/votehandler.php';
require_once 'objects/match.php';

$result = false;
$message = null;
$matchData = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id']) &&
		is_numeric($_GET['id'])) {
		$match = MatchHandler::getMatch($_GET['id']);

		if ($match != null) {
			if ($user->hasPermission('*') ||
				$user->hasPermission('event.compo') ||
				$match->isParticipant($user)) {

				$matchData['state'] = $match->getState();
				$matchData['ready'] = $match->isReady();
				$matchData['compoId'] = $match->getCompo()->getId();
				$matchData['currentTime'] = time();
				$matchData['startTime'] = $match->getScheduledTime();
				$matchData['chatId'] = $match->getChat()->getId();

				if ($match->getState() == Match::STATE_READYCHECK &&
					$match->isReady()) {
					$readyData = [];

					foreach (MatchHandler::getParticipantsByMatch($match) as $clan) {
						$memberData = [];

						foreach ($clan->getMembers() as $member) {
							$avatarFile = null;

							if ($member->hasValidAvatar()) {
								$avatarFile = $member->getAvatar()->getThumbnail();
							} else {
								$avatarFile = AvatarHandler::getDefaultAvatar($member);
							}

							$memberReadyStatus = ['userId' => $member->getId(),
																    'nick' => $member->getNickname(),
																    'avatarUrl' => $avatarFile,
																    'ready' => MatchHandler::isUserReady($member, $match)];

							$memberData[] = $memberReadyStatus;
						}

						$clanData = ['clanName' => $clan->getName(),
										     'clanTag' => $clan->getTag(),
										  	 'members' => $memberData];

						$readyData[] = $clanData;
					}

					$matchData['readyData'] = $readyData;
					$result = true;
				} else if ($match->getState() == Match::STATE_CUSTOM_PREGAME &&
						   $match->isReady()) {
					$banData = [];
					$bannableMapsArray = [];

					foreach (VoteOptionHandler::getVoteOptionsByCompo($match->getCompo()) as $voteOption) {
						$optionData = [];
						$optionData['name'] = $voteOption->getName();
						$optionData['thumbnailUrl'] = $voteOption->getThumbnailUrl();
						$optionData['id'] = $voteOption->getId();
						$optionData['isBanned'] = VoteOptionHandler::isVoted($voteOption, $match);
						$bannableMapsArray[] = $optionData;
					}

					$banData['options'] = $bannableMapsArray;
					$numBanned = VoteHandler::getNumBanned($match->getId());
					$banData['turn'] = VoteHandler::getCurrentBanner($numBanned, $match);

					$clanList = [];

					foreach (MatchHandler::getParticipants($match) as $clan) {
						$clanData = ['clanName' => $clan->getName(),
										  	 'clanTag' => $clan->getTag()];

						$memberData = [];

						foreach ($clan->getMembers() as $member) {
							$userData = ['userId' => $member->getId(),
											  	 'nick' => $member->getNickname(),
											  	 'chief' => $member->equals($clan->getChief())];

							$memberData[] = $userData;
						}

						$clanData['members'] = $memberData;
						$clanList[] = $clanData;
					}

					$banData['clans'] = $clanArray;

					$matchData['banData'] = $banData;
					$result = true;
				} else if ($match->getState() == Match::STATE_JOIN_GAME &&
					$match->isReady()) {
					$gameData = [];
					$gameData['connectDetails'] = $match->getConnectDetails();

					$clanList = [];

					foreach (MatchHandler::getParticipantsByMatch($match) as $clan) {
						$clanData = [];
						$clanData['clanName'] = $clan->getName();
						$clanData['clanTag'] = $clan->getTag();

						$memberData = [];

						foreach ($clan->getMembers() as $member) {
							$userData = [];

							$userData['userId'] = $member->getId();
							$userData['nick'] = $member->getNickname();
							$userData['chief'] = $member->equals($clan->getChief());

							$memberData[] = $userData;
						}

						$clanData['members'] = $memberData;
						$clanList[] = $clanData;
					}

					$gameData['clans'] = $clanList;
					$compo = $match->getCompo();

					if ($compo->getId() == 5) { // Only CS:GO TODO: Change to plugin handled
						foreach (VoteOptionHandler::getVoteOptionsByCompo($compo) as $option) {
							if (!VoteOptionHandler::isVoted($option, $match)) {
								$mapData = [];

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
				$message = Localization::getLocale('you_do_not_have_permission_to_do_that');
			}
		} else {
			$message = Localization::getLocale('this_match_does_not_exist');
		}
	} else {
		$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: application/json');

if ($result) {
	echo json_encode(['result' => $result, 'matchData' => $matchData], JSON_PRETTY_PRINT);
} else {
	echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}

Database::cleanup();
?>
