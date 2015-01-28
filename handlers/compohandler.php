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

    public static function generateDoubleElimination($compo) {
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
        $i = 0;
        while($i < count($clans)) {
            $toRemove = rand(0, count($clans-1));
            array_push($newClanList, $clans[$toRemove]);
            array_splice($array, $toRemove, 1);
            $i++;
        }
    }

    private static function generateMatches($carryMatches, $carryClans, $carryLoosers, $iteration, $compo) {
        $numberOfObjects = count($carryMatches) + count($carryClans); //The amount of objects we are going to handle
        $match_start_index = $numberOfObjects % 2; // 0 if even number of objects, 1 if uneven

        $carryObjects = array("matches" => array(), "clans" => array(), "looserClans" => array()); //Prepare all the info to return back

        if($match_start_index == 1) { //If there is an uneven amount of objects
            if(count($carryMatches) > 0 ) { //Prioritize carrying matches
                array_push($carryObjects['matches'], $carryMatches[0]);
            } else { //No matches to carry, carry a clan
                array_push($carryObjects['clans'], $carryClans[0]);
            }
        }
        

        for($i = 0; $i < ($numberOfObjects-$match_start_index)/2; $i++) { //Loop through amount of matches we are going to make
            $index = ($i*2)+$match_start_index; //Start acces

            $match = MatchHandler::createMatch(0, "", $compo, $iteration); //TODO connectData and scheduledTime
            array_push($carryObjects["matches"], $match);

            for($x = 0; $x < 2; $x++) {
                $toCheck = $index + $x;
                if($toCheck >= count($carryMatches)) {
                    //We are going to generate a reference to a clan
                    $toCheck = $toCheck - count($carryMatches);
                    MatchHandler::addMatchParticipant(0, $carryClans[$toCheck], $match->getId() );
                } else {
                    //Generate reference to winner of a match
                    MatchHandler::addMatchParticipant(1, $carryMatches[$toCheck], $match->getId() );
                }
            }

        }

        //Generate loosers

        

        return $carryObjects;
    }
}
?>