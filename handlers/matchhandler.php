<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/match.php';
require_once 'handlers/clanhandler.php';

/*
 * EPIC WARNING:
 * 
 * participantOfMatch should only have a clan linked if the clan is ready to play that match
 */

class MatchHandler {
    //State of the "participantOf" table responsible for the participants of a match
    const participantof_state_clan = 0;
    const participantof_state_winner = 1;
    const participantof_state_looser = 2;

    public static function getMatch($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` 
                                 WHERE `id` = \'' . $id . '\';');
        
        $mysql->close();

        return $result->fetch_object('Match');
        /*
        if ($row) {
            return new Match($row['id'], 
                             $row['scheduledTime'], 
                             $row['connectDetails'], 
                             $row['winner'], 
                             $row['state'], 
                             $row['compoId'],
                             $row['bracketOffset'],
                             $row['chat']);
        }*/
    }

    public static function createMatch($scheduledTime, $connectData, $compo, $bracketOffset, $chatId, $bracket) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_matches . '` (`scheduledTime`, `connectDetails`, `state`, `winner`, `compoId`, `bracketOffset`, `chat`, `bracket`) 
            VALUES (\'' . $mysql->real_escape_string($scheduledTime) . '\', 
                    \'' . $mysql->real_escape_string($connectData) . '\', 
                    \'' . Match::STATE_READYCHECK . '\',
                    \'0\', 
                    \'' . $mysql->real_escape_string($compo->getId()) . '\',
                    \'' . $mysql->real_escape_string($bracketOffset) . '\',
                    \'' . $mysql->real_escape_string($chatId) . '\',
                    \'' . $mysql->real_escape_string($bracket) . '\');');

        $fetchNewestResultId = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_matches . '` 
                                              ORDER BY `id` 
                                              DESC LIMIT 1;');

        $row = $fetchNewestResultId->fetch_array();

        $mysql->close();

        return self::getMatch($row['id']);
    }

    public static function addMatchParticipant($type, $participantId, $match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_participantOfMatch . '` (`type`, `participantId`, `matchId`) 
                       VALUES (\'' . $mysql->real_escape_string($type) . '\', 
                               \'' . $mysql->real_escape_string($participantId) . '\', 
                               \'' . $mysql->real_escape_string($match->getId()) . '\');');

        if($type != self::participantof_state_clan && $type != self::participantof_state_looser) {
            $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_matchrelationships . '` (`fromCompo`, `toCompo`) 
                VALUES (\'' . $mysql->real_escape_string($participantId) . '\', \'' . $mysql->real_escape_string($match->getId()) . '\');');
        }

        $mysql->close();
    }

    public static function getPendingMatches($compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` 
                                 WHERE `compoId` = ' . $mysql->real_escape_string( $compo->getId() ) . ';');

        $mysql->close();

        $matchList = array();

        while ($object = $result->fetch_object('Match')) {
            if ($object->getScheduledTime() > time()) {
                array_push($matchList, $object);
            } else if(!self::isReady($object)) {
                array_push($matchList, $object);
            }
        }

        return $matchList;
    }

    public static function getCurrentMatches($compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        //Picks matches that "should" be running.
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` 
                                 WHERE `compoId` = \'' . $mysql->real_escape_string( $compo->getId() ) . '\'
                                 AND `winner` = \'0\' 
                                 AND `scheduledTime` < \'' . time() . '\';');

        $mysql->close();

        $matchList = array();

        while ($object = $result->fetch_object('Match')) {
            if (self::isReady($object)) {
                array_push($matchList, $object);
            }
        }

        return $matchList;
    }

    public static function getFinishedMatches($compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        //Picks matches that "should" be running.
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` 
                                 WHERE `compoId` = ' . $mysql->real_escape_string( $compo->getId() ) . ' 
                                 AND `winner` != 0;');

        $mysql->close();

        $matchList = array();

        while ($object = $result->fetch_object('Match')) {
            array_push($matchList, $object);
        }

        return $matchList;
    }

    public static function getMatchForClan($clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT `matchId` FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                                 WHERE `type` = \'' . Settings::compo_match_participant_type_clan . '\' 
                                 AND `participantId` = \'' . $mysql->real_escape_string($clan->getId()) . '\';');

        $mysql->close();
        
        while ($row = $result->fetch_array()) {
             $match = self::getMatch($row['matchId']);

             if ($match->getWinner() == 0 && $match->getScheduledTime() < time()) {
                 return $match;
             }
        }
    }

    public static function hasClanMatch($clan) {
        return null !== self::getMatchForClan($clan);
    }

    // Unstable if user has multiple matches happening
    public static function getMatchForUser($user, $event) {
        $clans = ClanHandler::getClansForUser($user, $event);
        
        foreach ($clans as $clan) {
            $match = self::getMatchForClan($clan);
            
            if (isset($match)) {
                return $match;
            }
        }
    }

    public static function setWinner($match, $clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        // Set winner of match
        $mysql->query('UPDATE `' . Settings::db_table_infected_compo_matches . '` 
                       SET `winner` = \'' . $mysql->real_escape_string($clan->getId()) . '\'
                       WHERE `id` = \'' . $mysql->real_escape_string($match->getId()) . '\';');

        // Update match results
        //First, get list of matches we want to change
        $toWinList = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` WHERE `type` = 1 AND `participantId` = ' . $mysql->real_escape_string($match->getId()) . ';');

        while($row = $toWinList->fetch_array()) {
            $mysql->query('UPDATE `' . Settings::db_table_infected_compo_participantOfMatch . '` SET `type` = 0, `participantId` = ' . $mysql->real_escape_string($clan->getId()) . ' WHERE `id` = ' . $row['id'] . ';');

            $checkingMatchId = MatchHandler::getMatch($row['matchId']);
            ChatHandler::addClanMembersToChat(ChatHandler::getChat($checkingMatchId->getChat()), $clan);
        }
        /*
        $mysql->query('UPDATE `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                            SET `type` = 0, `participantId` = ' . $mysql->real_escape_string($clan->getId()) . ' 
                            WHERE `type` = 1 
                            AND `participantId` = ' . $mysql->real_escape_string($match->getId()) . ';');
        */
        $looser = null;

        $participants = self::getParticipants($match);

        foreach ($participants as $participant) {
            if ($participant->getId() != $clan->getId()) {
                $looser = $participant;
            }
        }

        $toLooseList = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` WHERE `type` = 2 AND `participantId` = ' . $mysql->real_escape_string($match->getId()) . ';');

        while($row = $toLooseList->fetch_array()) {
            $mysql->query('UPDATE `' . Settings::db_table_infected_compo_participantOfMatch . '` SET `type` = 0, `participantId` = ' . $mysql->real_escape_string($clan->getId()) . ' WHERE `id` = ' . $row['id'] . ';');

            $checkingMatchId = MatchHandler::getMatch($row['matchId']);
            ChatHandler::addClanMembersToChat(ChatHandler::getChat($checkingMatchId->getChat()), $clan);
        }

        /*$mysql->query('UPDATE `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                            SET `type` = 0, `participantId` = ' . $mysql->real_escape_string($looser->getId()) . ' 
                            WHERE `type` = 2 AND `participantId` = ' . $mysql->real_escape_string($match->getId()) . ';');*/
    
        $mysql->close();
    }

    public static function getParticipants($match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT `participantId` FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                                 WHERE `matchId` = \'' . $mysql->real_escape_string($match->getId()) . '\' 
                                 AND `type` = \'' . Settings::compo_match_participant_type_clan . '\';');
        
        $mysql->close();

        $clanList = array();

        while ($row = $result->fetch_array()) {
            array_push($clanList, ClanHandler::getClan($row['participantId']) );
        }

        return $clanList;
    }

    public static function getParticipantString($match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                                 WHERE `matchId` = \'' . $mysql->real_escape_string($match->getId()) . '\';');

        $mysql->close();

        $stringArray = array();

        while ($row = $result->fetch_array()) {
            if ($row['type'] == Settings::compo_match_participant_type_match_winner) {
                array_push($stringArray, "Winner of match " . $row['participantId']);
            } else if ($row['type'] == Settings::compo_match_participant_type_match_looser) {
                array_push($stringArray, "Looser of match " . $row['participantId']);
            } else if ($row['type'] == Settings::compo_match_participant_type_clan) {
                $clan = ClanHandler::getClan($row['participantId']);
                array_push($stringArray, $clan->getName() . ' - ' . $clan->getTag() . " (id " . $clan->getId() . ")");
            } 
        }

        return $stringArray;
    }

    public static function getParticipantTags($match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                                 WHERE `matchId` = \'' . $mysql->real_escape_string($match->getId()) . '\';');

        $mysql->close();

        $stringArray = array();

        while ($row = $result->fetch_array()) {
            if ($row['type'] == Settings::compo_match_participant_type_match_winner || $row['type'] == Settings::compo_match_participant_type_match_looser) {
                array_push($stringArray, "TBA");
            } else if ($row['type'] == Settings::compo_match_participant_type_clan) {
                $clan = ClanHandler::getClan($row['participantId']);
                array_push($stringArray, $clan->getTag());
            } 
        }

        return $stringArray;
    }

    public static function getParticipantsJson($match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                                 WHERE `matchId` = \'' . $mysql->real_escape_string($match->getId()) . '\';');

        $mysql->close();

        $jsonArray = array();

        while ($row = $result->fetch_array()) {
            if ($row['type'] == Settings::compo_match_participant_type_clan) {
                $clan = ClanHandler::getClan($row['participantId']);
                array_push($jsonArray, array("type" => $row['type'], "value" => $clan->getName() . ' - ' . $clan->getTag() . ""));
            } else {
                array_push($jsonArray, array("type" => $row['type'], "value" => $row['participantId']));
            }
        }

        return $jsonArray;
    }

    public static function getParents($match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT `fromCompo` FROM `' . Settings::db_table_infected_compo_matchrelationships . '` WHERE `toCompo` = \'' . $mysql->real_escape_string($match->getId()) . '\';');

        $mysql->close();

        $parentArray = array();

        while($row = $result->fetch_array()) {
            array_push($parentArray, self::getMatch($row['fromCompo']));
        }

        return $parentArray;
    }

    //Checks if the match can run(If we have enough participants. Returns false if we have to wait for earlier matches to complete)
    public static function isReady($match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT `type` FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                                 WHERE `matchId` = \'' . $mysql->real_escape_string($match->getId()) . '\';');

        $mysql->close();

        $hasParticipants = false;

        while ($row = $result->fetch_array()) {
            $hasParticipants = true;
            
            if ($row['type'] == Settings::compo_match_participant_type_match_winner || 
                $row['type'] == Settings::compo_match_participant_type_match_looser) {
                return false;
            }
        }

        return $hasParticipants;
    }

    //Used in the ready check
    public static function isUserReady($user, $match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_readyusers . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                 AND `matchId` = \'' . $mysql->real_escape_string($match->getId()) . '\';');
    
        $row = $result->fetch_array();

        $mysql->close();
        
        return $row ? true : false;
    }

    public static function acceptMatch($user, $match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_readyusers . '` (`userId`, `matchId`) 
                                 VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                                         \'' . $mysql->real_escape_string($match->getId()) . '\');');

        $mysql->close();

        $mysql->close();
    }

    public static function allHasAccepted($match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                                 WHERE `type` = \'0\'
                                 AND `matchId` = \'' . $mysql->real_escape_string($match->getId()) . '\';');

        // Iterate through clans
        while ($row = $result->fetch_array()) {
            $users = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                    WHERE `clanId` = \'' . $row['participantId'] . '\';');
            
            while($userRow = mysqli_fetch_array($users)) {
                $userCheck = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_readyusers . '` 
                                            WHERE `userId` = \'' . $userRow['userId'] . '\'
                                            AND `matchId` = \'' . $mysql->real_escape_string($match->getId()) . '\';');
                
                $row = mysqli_fetch_array($userCheck);
                
                if (!$row) {
                    return false;
                }

            }
        }

        $mysql->close();

        return true;
    }

    public static function getMatchesForCompo($compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` 
                                 WHERE `compoId` = \'' . $compo->getId() . '\';');

        $mysql->close();

        $matchList = array();

        while ($object = $result->fetch_object('Match')) {
            array_push($matchList, $object);
        }

        return $matchList;
    }
}
?>