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
        
        $row = $result->fetch_array();
        
        $mysql->close();
        
        if ($row) {
            return new Match($row['id'], 
                             $row['scheduledTime'], 
                             $row['connectDetails'], 
                             $row['winner'], 
                             $row['state'], 
                             $row['compoId'],
                             $row['bracketOffset']);
        }
    }

    public static function createMatch($scheduledTime, $connectData, $compo, $bracketOffset) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_matches . '` (`scheduledTime`, `connectDetails`, `state`, `winner`, `compoId`, `bracketOffset`) 
            VALUES (\'' . $mysql->real_escape_string($scheduledTime) . '\', 
                    \'' . $mysql->real_escape_string($connectData) . '\', 
                    \'' . Match::STATE_READYCHECK . '\',
                    \'0\', 
                    \'' . $mysql->real_escape_string($compo->getId()) . '\',
                    \'' . $mysql->real_escape_string($bracketOffset) . '\');');

        $fetchNewestResultId = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_matches . '` ORDER BY `id` DESC LIMIT 1;');

        $row = $fetchNewestResultId->fetch_array();

        $newId = $row['id'];

        return self::getMatch($newId);
    }
    public static function addMatchParticipant($type, $participantId, $match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_participantOfMatch . '` (`type`, `participantId`, `matchId`) 
                        VALUES (\'' . $mysql->real_escape_string($type) . '\', 
                                \'' . $mysql->real_escape_string($participantId) . '\', 
                                \'' . $mysql->real_escape_string($match->getId()) . '\');');

        $mysql->close();
    }

    public static function getPendingMatches($compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` 
                                      WHERE `compoId` = ' . $mysql->real_escape_string( $compo->getId() ) . ';');

        $matchList = array();

        while ($row = $result->fetch_array()) {
            $match = self::getMatch($row['id']);
            
            if ($match->getScheduledTime() > time()) {
                array_push($matchList, $match);
            } else if(!self::isReady($match)) {
                array_push($matchList, $match);
            }
        }

        $mysql->close();

        return $matchList;
    }

    public static function getCurrentMatches($compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        //Picks matches that "should" be running.
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` 
                                      WHERE `compoId` = ' . $mysql->real_escape_string( $compo->getId() ) . ' 
                                      AND `winner` = 0 AND `scheduledTime` < ' . time() . ';');

        $matchList = array();

        while ($row = $result->fetch_array()) {
            $match = self::getMatch($row['id']);
            
            if (self::isReady($match)) {
                array_push($matchList, $match);
            }
        }

        $mysql->close();

        return $matchList;
    }

    public static function getFinishedMatches($compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        //Picks matches that "should" be running.
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` 
                                      WHERE `compoId` = ' . $mysql->real_escape_string( $compo->getId() ) . ' 
                                      AND `winner` != 0;');

        $matchList = array();

        while ($row = $result->fetch_array()) {
            array_push($matchList, self::getMatch($row['id']));
        }

        $mysql->close();

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
                            SET `winner` = ' . $mysql->real_escape_string($clan->getId()) . ' 
                            WHERE `id` = ' . $mysql->real_escape_string($match->getId()) . ';');

        // Update match results
        $mysql->query('UPDATE `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                            SET `type` = 0, `participantId` = ' . $mysql->real_escape_string($clan->getId()) . ' 
                            WHERE `type` = 1 
                            AND `participantId` = ' . $mysql->real_escape_string($match->getId()) . ';');

        $looser = null;

        $participants = self::getParticipants($match);

        foreach ($participants as $participant) {
            if ($participant->getId() != $clan->getId()) {
                $looser = $participant;
            }
        }

        $mysql->query('UPDATE `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                            SET `type` = 0, `participantId` = ' . $mysql->real_escape_string($looser->getId()) . ' 
                            WHERE `type` = 2 AND `participantId` = ' . $mysql->real_escape_string($match->getId()) . ';');
    
        MySQL::open(Settings::db_name_infected_compo);
    }

    public static function getParticipants($match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                                      WHERE `matchId` = ' . $mysql->real_escape_string($match->getId()) . ' 
                                      AND `type` = ' . Settings::compo_match_participant_type_clan . ';');
    
        $clanArray = array();

        while ($row = $result->fetch_array()) {
            array_push($clanArray, ClanHandler::getClan($row['participantId']) );
        }

        $mysql->close();

        return $clanArray;
    }

    public static function getParticipantString($match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                                      WHERE `matchId` = ' . $mysql->real_escape_string($match->getId()) . ';');

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

        $mysql->close();

        return $stringArray;
    }

    //Checks if the match can run(If we have enough participants. Returns false if we have to wait for earlier matches to complete)
    public static function isReady($match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                                      WHERE `matchId` = ' . $mysql->real_escape_string($match->getId()) . ';');

        $hasParticipants = false;

        $mysql->close();

        while($row = $result->fetch_array()) {
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

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_readyusers . '` 
                                      WHERE `userId` = ' . $mysql->real_escape_string($user->getId()) . ' 
                                      AND `matchId` = ' . $mysql->real_escape_string($match->getId()) . ';');
    
        $row = $result->fetch_array();
        
        return $row ? true : false;
    }

    public static function acceptMatch($user, $match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_readyusers . '` (`userId`, `matchId`) 
                                      VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                                              \'' . $mysql->real_escape_string($match->getId()) . '\');');

        $mysql->close();
    }

    public static function allHasAccepted($match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` 
                                      WHERE `type` = 0 AND `matchId` = ' . $mysql->real_escape_string($match->getId()) . ';');

        //Iterate through clans
        while($row = $result->fetch_array()) {
            $users = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                         WHERE `clanId` = ' . $row['participantId'] . ';');
            
            while($userRow = mysqli_fetch_array($users)) {
                $userCheck = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_readyusers . '` 
                                                 WHERE `userId` = ' . $userRow['userId'] . ' AND `matchId` = ' . $mysql->real_escape_string($match->getId()) . ';');
                
                $row = mysqli_fetch_array($userCheck);
                
                if (!$row) {
                    return false;
                }

            }
        }

        $mysql->close();

        return true;
    }
}
?>