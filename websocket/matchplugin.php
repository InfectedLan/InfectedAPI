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
    private $upcomingMatches;
    
    function __construct(Server $server) {
        parent::__construct($server);
        $server->registerIntent("subscribeMatches", $this);
        $server->registerPlugin($this);

        $this->server = $server;
        $this->subscribedUsers = new SplObjectStorage();
        $this->lastUpdate = 0;
        $this->upcomingMatches = array();
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
            break;
        }
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
                
                //Remove the match from the list as we have used it
                unset($this->upcomingMatches[$key]);
            }
        }
        //Check for new matches to inform about starting
        if(time()-$this->lastUpdate > 60) {
            $matches = MatchHandler::getUpcomingMatches(60);
            if(count($matches) > 0) {
                echo "Scheduling " . count($matches) . " for the next 60 seconds\n";
                $this->upcomingMatches = $matches;
            }
            $this->lastUpdate = time();
        }
    }

    public function sendMatchData(WebSocketUser $connection, Match $match) {
        $this->server->send($connection, '{"intent": "matchUpdate", "data": [' . json_encode($match->getJsonableData()) . ']}'); //Pretty snazzy, sends a shitload of info in 1-2-3. This is what killed the servers.
    }
}
?>