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
require_once 'handlers/userhandler.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/compopluginhandler.php';
require_once 'handlers/clanhandler.php';

$result = false;
$message = null;
$data = null;

if (Session::isAuthenticated()) {
  $user = Session::getCurrentUser();

  if (isset($_GET['id'])) {
    $clan = ClanHandler::getClan($_GET['id']);

    if ($clan != null) {
      //Return some info on the clan
      $data = [];
	    $data['name'] = $clan->getName();
	    $data['tag'] = $clan->getTag();
	    $data['chief'] = $clan->getChiefId();
	    $compo = CompoHandler::getCompoByClan($clan);
	    $data['compo'] = $compo->getId();
	    $data['qualified'] = ClanHandler::isQualified($clan, $compo);
	    $data['playingMembers'] = [];
	    $playing = ClanHandler::getPlayingClanMembers($clan);

      foreach($playing as $person) {
		    $personData = ['id' => $person->getId(),
						           'displayName' => $person->getDisplayName()];

    		if ($compo->requiresSteamId() && $clan->getChiefId() == $user->getId()) {
    		  $personData['hasLinkedSteam'] = $person->getSteamId() !== null;
    		}

    		$data['playingMembers'][] = $personData;
	    }

	    $data['stepinMembers'] = [];
	    $stepin = ClanHandler::getStepinClanMembers($clan);

	    foreach($stepin as $person) {
    		$personData = ['id' => $person->getId(),
    						       'displayName' => $person->getDisplayName()];

    		if ($compo->requiresSteamId() && $clan->getChiefId() == $user->getId()) {
    		    $personData['hasLinkedSteam'] = $person->getSteamId() !== null;
    		}

    		$data['stepinMembers'][] = $personData;
	    }

	    $data['invitedMembers'] = [];
	    $invited = InviteHandler::getInvitesByClan($clan);

	    foreach($invited as $invitee) {
    		$inviteeUser = $invitee->getUser();
    		$personData = ['displayName' => $inviteeUser->getDisplayName()];

    		if ($clan->getChiefId() == $user->getId()) {
  		    $personData['inviteId'] = $invitee->getId();

          if ($compo->requiresSteamId()) {
  			    $personData['hasLinkedSteam'] = $inviteeUser->getSteamId() !== null;
  		    }
		    }

		    $data['invitedMembers'][] = $personData;
	    }

      $result = true;
    } else {
      $message = Localization::getLocale('this_clan_does_not_exist');
    }
  } else {
    $message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
  }
} else {
  $message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: application/json');

if ($result) {
  echo json_encode(['result' => $result, 'data' => $data], JSON_PRETTY_PRINT);
} else {
  echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}

Database::cleanup();
?>
