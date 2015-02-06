<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/compo.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/matchhandler.php';

class CompoHandler {
    public static function getCompo($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_compos . '` 
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $row = $result->fetch_array();
        
        $mysql->close();
        
        if ($row) {
            return new Compo($row['id'], 
                             $row['startTime'], 
                             $row['registrationDeadline'], 
                             $row['name'],
                             $row['desc'], 
                             $row['event'], 
                             $row['teamSize'], 
                             $row['tag']);
        }
    }
    public static function hasGeneratedMatches($compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` 
            WHERE `compoId` = \'' . $mysql->real_escape_string($compo->getId()) . '\';');
        
        $row = $result->fetch_array();

        $mysql->close();

        return null ==! $row;
    }
    public static function getComposForEvent($event) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_compos . '` 
                                      WHERE `event` = \'' . $event->getId() . '\';');

        $compoList = array();

        while ($row = $result->fetch_array()) {
            array_push($compoList, self::getCompo($row['id']));
        }

        $mysql->close();

        return $compoList;
    }

    public static function getClans($compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantof . '` 
                                      WHERE `compoId` = \'' . $mysql->real_escape_string($compo->getId()) . '\';');

        $clanList = array();

        while ($row = $result->fetch_array()) {
            $clan =  ClanHandler::getClan($row['clanId']);
            array_push($clanList, $clan);
        }

        $mysql->close();
        
        return $clanList;
    }

    public static function generateDoubleElimination($compo, $startTime, $compoSpacing) {
        /*
         * Psudocode:
         * Get list of participants, order randomly
         * Pass to some round iteration function that:
         * Generates matches based on passed clans/previous matches
         * Returns matches that should carry on in the winners and loosers bracket.
        */
        $clans = self::getClans($compo);
        $newClanList = array();
        //Randomize participant list to avoid any cheating
        while(count($clans) > 0) {
            $toRemove = rand(0, count($clans)-1);
            array_push($newClanList, $clans[$toRemove]);
            array_splice($clans, $toRemove, 1);
        }

        $carryData = array("matches" => array(), "clans" => $newClanList, "looserMatches" => array());
        $iteration = 0;
        while(true) {
            $carryData = self::generateMatches($carryData["matches"], $carryData["clans"], $carryData["looserMatches"], $iteration, $compo, $startTime + ($iteration * $compoSpacing));
            echo ";Iteration finished. Matches:";
            foreach($carryData["matches"] as $match) {
                echo "Match: " . $match->getId() . ",";
            }
            echo " clans: ";
            foreach($carryData["clans"] as $clan) {
                echo "Clan: " . $clan->getId() . ",";
            }
            echo " carry matches: ";
            foreach($carryData["carryMatches"] as $match) {
                echo "Carrymatch: " . $match->getId() . ",";
                array_push($carryData["matches"], $match);
            }
            echo ", looser matches:";
            foreach($carryData["looserMatches"] as $looserMatches) {
                echo "Old looser: " . $looserMatches->getId() . ",";
            }

            if(count($carryData['matches'])<2) {
                break;
            }

            $iteration++;
        }
    }

    private static function generateMatches($carryMatches, $carryClans, $carryLoosers, $iteration, $compo, $time) {
        $numberOfObjects = count($carryMatches) + count($carryClans); //The amount of objects we are going to handle
        $match_start_index = $numberOfObjects % 2; // 0 if even number of objects, 1 if uneven
        echo ";Num of objects: " . $numberOfObjects . ", " . count($carryMatches) . " matches and " . count($carryClans) . " clans;";

        $carryObjects = array("matches" => array(), "clans" => array(), "looserMatches" => array(), "carryMatches" => array()); //Prepare all the info to return back

        if($match_start_index == 1) { //If there is an uneven amount of objects
            if(count($carryMatches) > 0 ) { //Prioritize carrying matches
                array_push($carryObjects['carryMatches'], array_shift($carryMatches));
            } else { //No matches to carry, carry a clan
                array_push($carryObjects['clans'], array_shift($carryClans));
            }
        }
        
        while($numberOfObjects > 0) {
            //Create match
            $match = MatchHandler::createMatch($time, "", $compo, $iteration); //TODO connectData
            array_push($carryObjects["matches"], $match);

            //Assign participants
            for($a = 0; $a < 2; $a++) {
                if(count($carryClans) > 0) {
                    MatchHandler::addMatchParticipant(0, array_shift($carryClans)->getId(), $match);
                } else {
                    MatchHandler::addMatchParticipant(1, array_shift($carryMatches)->getId(), $match);
                }
            }

            $numberOfObjects = count($carryMatches) + count($carryClans); //The amount of objects we are going to handle
        }

        //Generate loosers

        $oldLooserCarry = $carryLoosers;
        $currentMatches = $carryObjects['matches'];

        $looserCount = count($oldLooserCarry) + count($currentMatches);

        echo ";Number of loosers: " . $looserCount . ". Old loosers: " . count($oldLooserCarry) . ". Newer matches: " . count($currentMatches) . ". Data: ";
        foreach($oldLooserCarry as $old) {
            echo "Old looser: " . $old->getId() . ",";
        }
        foreach($currentMatches as $new) {
            echo "New match: " . $new->getId() . ",";
        }

        if($looserCount % 2 != 0) {
            //Prioritize carrying new
            if(count($currentMatches) > 0) {
                $matchToPush = array_shift($currentMatches);
                echo ";we have to carry a looser match. Carrying a current match: " . $matchToPush->getId() . ".";
                array_push($carryObjects['looserMatches'], $matchToPush);
            } else if(count($oldLooserCarry) > 0) {
                $matchToPush = array_shift($oldLooserCarry);
                echo ";we have to carry a looser match. Carrying a old match: " . $matchToPush->getId() . ".";
                array_push($carryObjects['looserMatches'], $matchToPush);
            }
        }

        while($looserCount > 0)
        {
            $match = MatchHandler::createMatch($time, "", $compo, $iteration); //TODO connectData

            if(count($oldLooserCarry) > 0) {
                MatchHandler::addMatchParticipant(MatchHandler::participantof_state_winner, array_shift($oldLooserCarry)->getId(), $match);
            } else {
                MatchHandler::addMatchParticipant(MatchHandler::participantof_state_looser, array_shift($currentMatches)->getId(), $match);
            }

            if(count($currentMatches) > 0) {
                MatchHandler::addMatchParticipant(MatchHandler::participantof_state_looser, array_shift($currentMatches)->getId(), $match);
            } else {
                MatchHandler::addMatchParticipant(MatchHandler::participantof_state_winner, array_shift($oldLooserCarry)->getId(), $match);
            }
            array_push($carryObjects['looserMatches'], $match);

            $looserCount = count($oldLooserCarry) + count($currentMatches);
        }



        return $carryObjects;
    }
}
?>