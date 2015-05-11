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

require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/chathandler.php';
require_once 'handlers/clanhandler.php';
require_once 'objects/compo.php';
require_once 'objects/event.php';

class CompoHandler {
    /*
     * Get a compo by the internal id.
     */
    public static function getCompo($id) {
        $database = Database::open(Settings::db_name_infected_compo);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_compos . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();

        return $result->fetch_object('Compo');
    }

    /*
     * Get a list of compos.
     */
    public static function getCompos() {
        $event = EventHandler::getCurrentEvent();
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_compos . '` 
                                    WHERE `eventId` = \'' . $event->getId() . '\';');

        $database->close();

        $compoList = array();

        while ($object = $result->fetch_object('Compo')) {
            array_push($compoList, $object);
        }

        return $compoList;
    }

    /*
     * Get compos for the specified event.
     */
    public static function getComposByEvent(Event $event) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_compos . '` 
                                    WHERE `eventId` = \'' . $event->getId() . '\';');

        $database->close();

        $compoList = array();

        while ($object = $result->fetch_object('Compo')) {
            array_push($compoList, $object);
        }

        return $compoList;
    }

    /*
     * Get compo by specified clan.
     */
    public static function getCompoByClan(Clan $clan) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_compo . '`
                                    WHERE `id` = (SELECT `compoId` FROM `' . Settings::db_table_infected_compo_participantof . '` 
                                                  WHERE `clanId` = \'' . $clan->getId() . '\'
                                                  LIMIT 1);');

        $database->close();

        return $result->fetch_object('Compo');
    }

    /*
     * Returns true if the given compo has generated matches.
     */
    public static function hasGeneratedMatches(Compo $compo) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_matches . '` 
                                    WHERE `compoId` = \'' . $compo->getId() . '\';');
        
        $database->close();

        return $result->num_rows > 0;
    }

    private static function generateMatches(array $carryMatches, array $carryClans, array $carryLoosers, $iteration, Compo $compo, $time, $looserOffsetTime) {
        $numberOfObjects = count($carryMatches) + count($carryClans); //The amount of objects we are going to handle
        $match_start_index = $numberOfObjects % 2; // 0 if even number of objects, 1 if uneven

        $carryObjects = array('matches' => array(), 
                              'clans' => array(), 
                              'looserMatches' => array(), 
                              'carryMatches' => array()); // Prepare all the info to return back

        if ($match_start_index == 1) { //If there is an uneven amount of objects
            if (count($carryMatches) > 0 ) { //Prioritize carrying matches
                array_push($carryObjects['carryMatches'], array_shift($carryMatches));
            } else { //No matches to carry, carry a clan
                array_push($carryObjects['clans'], array_shift($carryClans));
            }
        }
        
        while ($numberOfObjects > 0) {
            //Create match
            $chat = ChatHandler::createChat('match chat');
            $match = MatchHandler::createMatch($time, '', $compo, $iteration, $chat->getId(), Match::BRACKET_WINNER); // TODO: connectData
            array_push($carryObjects['matches'], $match);

            //Assign participants
            for ($a = 0; $a < 2; $a++) {
                if (count($carryClans) > 0) {
                    $clan = array_shift($carryClans);
                    MatchHandler::addMatchParticipant(0, $clan->getId(), $match);
                    ChatHandler::addClanMembersToChat($chat, $clan);
                } else {
                    MatchHandler::addMatchParticipant(1, array_shift($carryMatches)->getId(), $match);
                }
            }

            $numberOfObjects = count($carryMatches) + count($carryClans); // The amount of objects we are going to handle
        }

        // Generate loosers
        $oldLooserCarry = $carryLoosers;
        $currentMatches = $carryObjects['matches'];

        $looserCount = count($oldLooserCarry) + count($currentMatches);

        //echo ';Number of loosers: ' . $looserCount . '. Old loosers: ' . count($oldLooserCarry) . '. Newer matches: ' . count($currentMatches) . '. Data: ';
        foreach ($oldLooserCarry as $old) {
            //echo 'Old looser: ' . $old->getId() . ',';
        }

        foreach ($currentMatches as $new) {
            //echo 'New match: ' . $new->getId() . ',';
        }

        if ($looserCount % 2 != 0) {
            //Prioritize carrying new
            if (count($currentMatches) > 0) {
                $matchToPush = array_shift($currentMatches);
                //echo ';we have to carry a looser match. Carrying a current match: ' . $matchToPush->getId() . '.';
                array_push($carryObjects['looserMatches'], $matchToPush);
            } else if (count($oldLooserCarry) > 0) {
                $matchToPush = array_shift($oldLooserCarry);
                //echo ';we have to carry a looser match. Carrying a old match: ' . $matchToPush->getId() . '.';
                array_push($carryObjects['looserMatches'], $matchToPush);
            }
        }

        while ($looserCount > 0) {
            $chat = ChatHandler::createChat('match chat');
            $match = MatchHandler::createMatch($time + $looserOffsetTime, '', $compo, $iteration, $chat->getId(), Match::BRACKET_LOOSER); //TODO connectData

            if (count($oldLooserCarry) > 0) {
                MatchHandler::addMatchParticipant(MatchHandler::participantof_state_winner, array_shift($oldLooserCarry)->getId(), $match);
            } else {
                MatchHandler::addMatchParticipant(MatchHandler::participantof_state_looser, array_shift($currentMatches)->getId(), $match);
            }

            if (count($currentMatches) > 0) {
                MatchHandler::addMatchParticipant(MatchHandler::participantof_state_looser, array_shift($currentMatches)->getId(), $match);
            } else {
                MatchHandler::addMatchParticipant(MatchHandler::participantof_state_winner, array_shift($oldLooserCarry)->getId(), $match);
            }

            array_push($carryObjects['looserMatches'], $match);
            $looserCount = count($oldLooserCarry) + count($currentMatches);
        }

        return $carryObjects;
    }

    public static function generateDoubleElimination(Compo $compo, $startTime, $compoSpacing) {
        /*
         * Psudocode:
         * Get list of participants, order randomly
         * Pass to some round iteration function that:
         * Generates matches based on passed clans/previous matches
         * Returns matches that should carry on in the winners and loosers bracket.
        */
        $clans = self::getCompleteClans($compo);
        $newClanList = array();
        
        //Randomize participant list to avoid any cheating
        while (count($clans) > 0) {
            $toRemove = rand(0, count($clans) - 1);
            array_push($newClanList, $clans[$toRemove]);
            array_splice($clans, $toRemove, 1);
        }

        $carryData = array('matches' => array(), 
                           'clans' => $newClanList, 
                           'looserMatches' => array());
        $iteration = 0;
        
        while (true) { // Um. Memory leak here? We should have some objects that we could iterate instead?...
            $carryData = self::generateMatches($carryData['matches'], 
                                               $carryData['clans'], 
                                               $carryData['looserMatches'], 
                                               $iteration, $compo, $startTime + ($iteration * $compoSpacing), $compoSpacing);
            
            foreach ($carryData['carryMatches'] as $match) {
                array_push($carryData['matches'], $match);
            }
            //echo ', looser matches:';
            /*foreach($carryData['looserMatches'] as $looserMatches) {
                //echo 'Old looser: ' . $looserMatches->getId() . ',';
            }*/

            $iteration++;

            if (count($carryData['matches']) < 2) {
                $chat = ChatHandler::createChat('match chat');
                $match = MatchHandler::createMatch($startTime + ($iteration * $compoSpacing), '', $compo, $iteration, $chat->getId(), Match::BRACKET_WINNER); //TODO connectData
                MatchHandler::addMatchParticipant(1, $carryData['matches'][0]->getId(), $match);
                MatchHandler::addMatchParticipant(1, $carryData['looserMatches'][0]->getId(), $match);
                break;
            }
        }
    }

    /*
     * Get clans for specified compo.
     *
     * DEPRECATED: This is only kept for comptibility, should be removed as soon as possible.
     */
    public static function getClans(Compo $compo) {
        return ClanHandler::getClansByCompo($compo);
    }

    /*
     * Get complete clans for specified compo.
     *
     * DEPRECATED: This is only kept for comptibility, should be removed as soon as possible.
     */
    public static function getCompleteClans(Compo $compo) {
        return ClanHandler::getCompleteClansByCompo($compo);
    }
}
?>