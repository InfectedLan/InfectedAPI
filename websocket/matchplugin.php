<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
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
require_once 'server.php';
require_once 'websocketplugin.php';
require_once 'handlers/matchhandler.php';
require_once 'objects/user.php';
require_once 'objects/match.php';

class MatchPlugin extends WebSocketPlugin {
    private $server;
    private $subscribedUsers;
    private $lastUpdate;
    private $upcomingMatches; //Matches that will start soon

    private $watchingMatches; //Matches that we are watching
    
    function __construct(Server $server) {
        parent::__construct($server);
        $server->registerIntent("subscribeMatches", $this);
	$server->registerIntent("voteMap", $this);
	$server->registerIntent("ready", $this);
        $server->registerPlugin($this);

        $this->server = $server;
        $this->subscribedUsers = new SplObjectStorage();
        $this->lastUpdate = 0;
        $this->upcomingMatches = array();
	$this->watchingMatches = array();
    }

    public function handleIntent($intent, $args, WebSocketUser $connection) {
        switch($intent) {
        case "subscribeMatches":
            if($this->server->isAuthenticated($connection)) {
                $user = $this->server->getUser($connection);
                $this->subscribedUsers[$connection] = $user;

                //If the user is in a match, tell that user.
                $match = MatchHandler::getMatchByUser($user);
                if($match != null && $match->isReady()) {
                    $this->sendMatchData($connection, $match);
                }
            }
	    return true;
            break;
	case "voteMap":
	    if($this->server->isAuthenticated($connection)) {
		$user = $this->server->getUser($connection);
		$match = MatchHandler::getMatch($args[0]);
		
		$result = $this->attemptMapVote($args[1], $match, $user);
		if($result != true) {
		    $this->server->send($connection, '{"intent": "error", "data": ' . json_encode(array($result)) . '}');
		} else {
		    $this->handleMatchUpdate($match);
		}
	    }
	    return true;
	    break;
	case "ready":
	    if($this->server->isAuthenticated($connection)) {
		$user = $this->server->getUser($connection);
		$match = MatchHandler::getMatch($args[0]);
		
		$result = $this->attemptAcceptMatch($match, $user);
		if($result != true) {
		    $this->server->send($connection, '{"intent": "error", "data": ' . json_encode(array($result)) . '}');
		} else {
		    $this->handleMatchUpdate($match);
		}
	    }
	    return true;
	    break;
        }
    }

    public function attemptAcceptMatch(Match $match, User $user) {
	if ($match != null) {
	    if ($match->isParticipant($user)) {
		MatchHandler::acceptMatch($user, $match);
		
		if (MatchHandler::allHasAccepted($match)) {
		    $plugin = CompoPluginHandler::getPluginObjectOrDefault($compo->getPluginName());

		    if ($plugin->hasVoteScreen()) {
			$match->setState(Match::STATE_CUSTOM_PREGAME);
		    } else {
			$match->setState(Match::STATE_JOIN_GAME);
		    }
		}

		return true;
	    } else {
		return Localization::getLocale('you_are_not_a_participant_of_this_match');
	    }
	} else {
	    return Localization::getLocale('this_match_does_not_exist');
	}
    }

    public function attemptMapVote($optionId, Match $match, $user) {
	$numBanned = VoteHandler::getNumBanned($match->getId());
	$turn = VoteHandler::getCurrentBanner($numBanned);

	if ($match != null) {
	    if ($turn != 2) {
		$participants = MatchHandler::getParticipantsByMatch($match);
		$clan = $participants[$turn];
				
		if ($user->equals($clan->getChief())) {
		    $voteOption = VoteOptionHandler::getVoteOption($_GET['id']);
					
		    if ($voteOption != null) {
			$compo = $voteOption->getCompo();
			if ($compo->equals($match->getCompo())) {
			    VoteHandler::banMap($voteOption, $match->getId());
			    $options = VoteOptionHandler::getVoteOptionsByCompo($compo);
							
			    if ($numBanned == count($options)-1) {
				$match->setState(2);
			    }

			    return true;
			}
		    } else {
			return Localization::getLocale('this_map_does_not_exist');
		    }
		}  else {
		    return Localization::getLocale('you_do_not_have_permission_to_do_that');
		}
	    } else {
		return Localization::getLocale('this_match_is_currently_starting');
	    }
	} else {
	    return Localization::getLocale('this_match_does_not_exist');
	}
    }

    public function handleMatchUpdate(Match $match) {
	//TODO
	$matchData = null;
	$matchKey = null;
	foreach($this->watchingMatches as $key => $data) {
	    if($data["id"] == $match->getId()) {
		$matchData = $data["id"];
		$matchKey = $key;
	    }
	}
	//For now, we are just sending everything as we need all the data anyways
	if($matchData["state"] != $match->getState() || $matchData["stateProgress"] != self::calculateStateProgress($match)) {
	    $clans = MatchHandler::getParticipantsByMatch($match);
	    $people = array();
	    foreach($clans as $clan) {
		$members = clanHandler::getPlayingClanMembers($clan);
		array_merge($people, $members);
	    }
	    foreach($people as $person) {
		foreach($this->subscribedUsers as $connection => $user) {
		    if($user->getId() == $person->getId()) {
			sendMatchData($connection, $match);
			break;
		    }
		}
	    }
	    $matchData["state"] = $match->getState();
	    $matchData["stateProgress"] = self::calculateProgress($match);
	    $this->watchingMatches[$key] = $matchData;
	}
	
    }

    public function calculateStateProgress(Match $match) {
	if($match->getState() == Match::STATE_CUSTOM_PREGAME) {
	    return VoteHandler::getNumBanned($match->getId());
	} else if($match->getState() == Match::STATE_READYCHECK) {
	    return MatchHandler::getReadyCount($match);
	}
	return 0;
    }

    public function onConnect(WebSocketUser $connection) {

    }

    public function onDisconnect(WebSocketUser $connection) {
        unset($this->subscribedUsers[$connection]);
    }

    public function tick() {
        //Iterate though matches starting in the next 60 seconds, to find out if we want to notify
        foreach($this->upcomingMatches as $key => $match) {
            if($match->getScheduledTime() <= time()) {
                $clans = MatchHandler::getParticipantsByMatch($match);
                $people = array();
                foreach($clans as $clan) {
                    $members = ClanHandler::getPlayingClanMembers($clan);
                    $people = array_merge($people, $members);
                }
                //We have everyone involved in the match, iterate through subscribed people and notify them
                foreach($people as $person) {
                    foreach($this->subscribedUsers as $connection => $user) {
                        if($user->getId() == $person->getId()) {
                            sendMatchData($connection, $match);
                            break;
                        }
                    }
                }
                //Add to "current matches"
		$this->watchingMatches[] = ["id" => $match->getId(), "state" => $match->getState(), "stateProgress" => 0];
                //Remove the match from the list as we have used it
                unset($this->upcomingMatches[$key]);
            }
        }
	//Things we check every now and then for compatability reasons
        if(time()-$this->lastUpdate > 60) {
	    //Check for new matches to inform about starting
            $matches = MatchHandler::getUpcomingMatches(60);
            if(count($matches) > 0) {
                echo "Scheduling " . count($matches) . " for the next 60 seconds\n";
                $this->upcomingMatches = $matches;
            }
	    //Check if matches we are currently watching are done
	    foreach($this->watchingMatches as $key => $matchData) {
		$match = MatchHandler::getMatch($matchData["id"]);
		if(!isset($match)) {
		    echo "Match " . $match->getId() . " dissapeared! Removing\n";
		    unset($this->watchingMatches[$key]);
		    continue;
		}
		if($match->getWinnerId() != 0) {
		    echo "Match " . $match->getId() . " is done! Removing\n";
		    unset($this->watchingMatches[$key]);
		    continue;
		}
		if($match->getState() != $matchData["state"]) {
		    $this->handleMatchUpdate($match);
		    echo "Match " . $match->getId() . " got an outside state change\n";
		    continue;
		}
		if($this->calculateStateProgress($match) != $matchData["stateProgress"]) {
		    $this->handleMatchUpdate($match);
		    echo "Match " . $match->getId() . " got an outside progress change\n";
		    continue;
		}
	    }
            $this->lastUpdate = time();
        }
    }

    /*
     * Sends all match data. Not the fastest.
     */
    public function sendMatchData(WebSocketUser $connection, Match $match) {
	if($match == null) {
	    $this->server->send($connection, '{"intent": "matchUpdate", "data": []}'); //If there is no match, just send an empty array to indicate the match is over.
	} else {
	    $this->server->send($connection, '{"intent": "matchUpdate", "data": [' . json_encode($match->getJsonableData()) . ']}'); //Pretty snazzy, sends a shitload of info in 1-2-3. This is what killed the servers.
	}
    }
}
?>