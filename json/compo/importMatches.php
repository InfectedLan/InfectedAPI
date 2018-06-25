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
require_once 'secret.php';
require_once 'localization.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/chathandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/serverhandler.php';
require_once 'handlers/compopluginhandler.php';

$result = false;
$message = null;
$data = null;

if (isset($_GET['id']) &&
  isset($_GET['api_key'])) {
  $compo = CompoHandler::getCompo($_GET['id']);

  if ($compo != null) {
    $plugin = CompoPluginHandler::getPluginObjectOrDefault($compo->getPluginName());

    if (defined('Secret::api_key')) {
      if (Secret::api_key == $_GET['api_key']) {
        $data = file_get_contents('php://input');
        $json = json_decode($data);
        //Get clans that are qualified for the compo
        $participatingClans = ClanHandler::getQualifiedClansByCompo($compo);
        //Step one: Delete matches that aren't mentioned
        $existingMatches = MatchHandler::getMatchesByCompo($compo);
        $updateList = [];

        foreach($existingMatches as $match) {
          $metadata = MatchHandler::getMetadata($match);
          //If this match is not touched by toornament at all
          if(!isset($metadata['toornamentId'])) {
            MatchHandler::deleteMatch($match);
          }
          //If this match does not match one of the mentioned matches
          $exists = false;

          foreach ($json as $jsonMatch) {
            if ($metadata['toornamentId'] == $jsonMatch->toornamentId) {
              $exists = true;
              break;
            }
          }

          if (!$exists) {
            MatchHandler::deleteMatch($match);
          } else {
            $updateList[] = $match;
          }
        }

        //Step 2: Iterate through sendt servers and create them or smth
        foreach ($json as $jsonMatch) {
            //echo "creating " . print_r($jsonMatch);
            $allreadyExists = false;
            $existingMatch = null;

            foreach($existingMatches as $match) {
              $metadata = MatchHandler::getMetadata($match);

              if ($metadata['toornamentId'] == $jsonMatch->toornamentId) {
                $existingMatch = $match;
                $allreadyExists = true;
                break;
              }
            }

            if (!$allreadyExists) {
        			if (count($jsonMatch->participants)!=2) {
        			  echo "WARNING: Skipping match due to not enough participants\n";
        			} else {
        			  $chat = ChatHandler::createChat("match-chat", "Match chat");
        			  $match = MatchHandler::createMatch(time(), "connect " . $jsonMatch->ip . ";password " . $jsonMatch->password, $compo, 0, $chat, 0);
        			  MatchHandler::setMetadata($match, "toornamentId", $jsonMatch->toornamentId);

                //Add participants
        			  foreach($jsonMatch->participants as $participant) {
        				  foreach($participatingClans as $clan) {
        				    //echo "Comparing \"" . substr(htmlspecialchars_decode($clan->getName()), 0, 21) . "\" vs \"" . $participant . "\"<br />\n";

                    if (substr(htmlspecialchars_decode($clan->getName()), 0, 21) == $participant) {
        				      //echo "is match\n";
        					    MatchHandler::addMatchParticipant(MatchHandler::PARTICIPANTOF_STATE_CLAN, $clan->getId(), $match);
        				    }
        				  }
        			  }
        			}
            } else {
      		      echo '<br />Match ' . $jsonMatch->toornamentId . ' allready exists, updating details with ip ' . $jsonMatch->ip . ' and password ' . $jsonMatch->password . '<br />\n';
      		      print_r($jsonMatch);
      		      echo '\n<br />';
                MatchHandler::updateConnectDetails($existingMatch, 'connect ' . $jsonMatch->ip . ';password ' . $jsonMatch->password);
                //TODO STEPIN SUPPORT
            }
        }

        $result = true;
      } else {
        $message = 'Invalid API key!';
      }
    } else {
      $message = 'Api \'secret\' key missing from secret.php. Please add \'api_key\' to secret.php';
    }
  } else {
    $message = Localization::getLocale('this_compo_does_not_exist');
  }
} else {
  $message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
}

header('Content-Type: text/plain');
if($result) {
    echo json_encode(array('result' => $result), JSON_PRETTY_PRINT);
} else {
    echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
}

Database::cleanup();
?>
