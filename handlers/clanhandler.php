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
require_once 'handlers/chathandler.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/clan.php';
require_once 'objects/user.php';
require_once 'objects/event.php';
require_once 'objects/compo.php';

class ClanHandler {
    const STATE_MAIN_PLAYER = 0;
    const STATE_STEPIN_PLAYER = 1;

    /*
     * Get a clan by the internal id.
     */
    public static function getClan($id) {
        $database = Database::open(Settings::db_name_infected_compo);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_clans . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();

        return $result->fetch_object('Clan');
    }

    /*
     * Get clan for a specified user.
     */
    public static function getClansByUser(User $user) {
        $event = EventHandler::getCurrentEvent();
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_clans . '` 
                                    WHERE `id` = (SELECT `clanId` FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                                  WHERE `userId` = \'' . $user->getId() . '\');');

        $database->close();

        $clanList = array();

        while ($object = $result->fetch_object('Clan')) {
            if ($event->equals($object->getEvent())) {
                array_push($clanList, $object);
            }
        }

        return $clanList;
    }

    /*
     * Get compo by specified clan.
     */
    public static function getCompo(Clan $clan) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_compo . '`
                                    WHERE `id` = (SELECT `compoId` FROM `' . Settings::db_table_infected_compo_participantof . '` 
                                                  WHERE `clanId` = \'' . $clan->getId() . '\');');

        $database->close();

        return $result->fetch_object('Compo');
    }

    /*
     * Get members for specified clan.
     */
    public static function getClanMembers(Clan $clan) {
        $database = Database::open(Settings::db_name_infected);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '` 
                                    WHERE `id` = (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . Settings::db_table_infected_compo_memberof . '` 
                                                  WHERE `clanId` = \'' . $clan->getId() . '\');');

        $database->close();

        $memberList = array();

        while ($object = $result->fetch_object('User')) {
            array_push($memberList, $object);
        }

        return $memberList;
    }

    /*
     * Get playing members for specified clan.
     */
    public static function getPlayingClanMembers(Clan $clan) {
        $database = Database::open(Settings::db_name_infected);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '` 
                                    WHERE `id` = (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . Settings::db_table_infected_compo_memberof . '` 
                                                  WHERE `clanId` = \'' . $clan->getId() . '\'
                                                  AND `stepInId` = \'0\');');

        $database->close();

        $memberList = array();

        while ($object = $result->fetch_object('User')) {
            array_push($memberList, $object);
        }

        return $memberList;
    }

    /*
     * Get step in members for specified clan.
     */
    public static function getStepInClanMembers(Clan $clan) {
        $database = Database::open(Settings::db_name_infected);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '` 
                                    WHERE `id` = (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . Settings::db_table_infected_compo_memberof . '` 
                                                  WHERE `clanId` = \'' . $clan->getId() . '\'
                                                  AND `stepInId` = \'1\');');
        $database->close();

        $memberList = array();

        while ($object = $result->fetch_object('User')) {
            array_push($memberList, $object);
        }

        return $memberList;
    }

    /*
     * Returns true of the specified user is member of the specified clan.
     */
    public static function isClanMember(Clan $clan, User $user) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                    WHERE `clanId` = \'' . $clan->getId() . '\'
                                    AND `userId` = \'' . $user->getId() . '\';');

        $database->close();

        return $result->num_rows > 0;
    }

    /*
     * Return true if the specified user is a stepin member.
     */
    public static function isStepInClanMember(Clan $clan, User $user) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                    WHERE `clanId` = \'' . $clan->getId() . '\'
                                    AND `userId` = \'' . $user->getId() . '\'
                                    AND `stepInId` = \'1\';');

        $database->close();

        return $result->num_rows > 0;
    }

    /*
     * Set the step in state of a member.
     */
    public static function setStepInClanMemberState(Clan $clan, User $user, $state) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('UPDATE `' . Settings::db_table_infected_compo_memberof . '` 
                                    SET `stepInId` = \'' . $database->real_escape_string($state) . '\'
                                    WHERE `clanId` = \'' . $clan->getId() . '\'
                                    AND `userId` = \'' . $user->getId() . '\';');

        $database->close();
    }

    /*
     * Create a new clan.
     */
    public static function createClan(Event $event, $name, $tag, Compo $compo, User $user) {
        $database = Database::open(Settings::db_name_infected_compo);

        $database->query('INSERT INTO `' . Settings::db_table_infected_compo_clans . '` (`eventId`, `chiefId`, `name`, `tag`) 
                          VALUES (\'' . $event->getId() . '\', 
                                  \'' . $user->getId() . '\', 
                                  \'' . $database->real_escape_string(htmlentities($name, ENT_QUOTES, 'UTF-8')) . '\', 
                                  \'' . $database->real_escape_string(htmlentities($tag, ENT_QUOTES, 'UTF-8')) . '\');');
        
        // Fetch the id of the clan we just added.
        $id = $database->insert_id;

        $database->query('INSERT INTO `' . Settings::db_table_infected_compo_participantof . '` (`clanId`, `compoId`) 
                          VALUES (\'' . $database->real_escape_string($fetchedId) . '\', 
                                  \'' . $compo->getId() . '\');');
        
        $database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`clanId`, `userId`) 
                          VALUES (\'' . $database->real_escape_string($fetchedId) . '\', 
                                  \'' . $user->getId() . '\');');

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_clans . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();

        // Allow user to talk in global chat.
        $mainChat = ChatHandler::getChat(1); // TODO: Change this to the first chat in the array?
        $mainChat->addMember($user);

        return $result->fetch_object('Clan');
    }

    /*
     * Kick a specified member from specified clan.
     */
    public static function kickFromClan(Clan $clan, User $user) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('DELETE FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\' 
                                    AND `clanId` = \'' . $clan->getId() . '\';');
    
        $database->close();
    }
}
?>