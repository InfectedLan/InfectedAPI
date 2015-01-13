<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/clan.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/invitehandler.php';

class ClanHandler {
    public static function getClan($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_clans . '` 
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $row = $result->fetch_array();
        
        $mysql->close();

        if ($row) {
            return new Clan($row['id'], 
                            $row['chief'], 
                            $row['name'], 
                            $row['event'], 
                            $row['tag']);
        }
    }

    public static function getClansForUser($user, $event) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');

        $clanArray = array();

        while ($row = $result->fetch_array()) {
            $clan = self::getClan($row['clanId']);
            if($event->getId() == $clan->getEvent()) {
                array_push($clanArray, $clan);
            }
        }
        
        /*
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_clans . '` WHERE `chief` = ' . $mysql->real_escape_string($user->getId()) . ';');

        while($row = $result->fetch_array()) {
            array_push($clanArray, self::getClan($row['id']));
        }*/

        return $clanArray;
    }

    public static function getCompo($clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantof . '` 
                                      WHERE `clanId` = \'' . $mysql->real_escape_string( $clan->getId() ) . '\';');

        $row = $result->fetch_array();

        $mysql->close();

        if ($row) {
            return CompoHandler::getCompo($row['compoId']);
        }
    }

    public static function getInvites($clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_invites . '` 
                                      WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\';');

        $peopleArray = array();

        while ($row = $result->fetch_array()) {
            array_push($peopleArray, InviteHandler::getInvite($row['id']));
        }

        $mysql->close();

        return $peopleArray;
    }

    public static function getMembers($clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                      WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\';');

        $memberList = array();

        while ($row = $result->fetch_array()) {
            array_push($memberList, UserHandler::getUser($row['userId']));
        }

        $mysql->close();

        return $memberList;
    }

    public static function getPlayingMembers($clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                      WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\'
                                      AND `stepin` = 0;');

        $memberList = array();

        while ($row = $result->fetch_array()) {
            array_push($memberList, UserHandler::getUser($row['userId']));
        }

        $mysql->close();

        return $memberList;
    }

    public static function getStepinMembers($clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                      WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\'
                                      AND `stepin` = 1;');

        $memberList = array();

        while ($row = $result->fetch_array()) {
            array_push($memberList, UserHandler::getUser($row['userId']));
        }

        $mysql->close();

        return $memberList;
    }

    public static function isMember($user, $clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                      WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\'
                                      AND `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');

        $row = $result->fetch_array();

        $mysql->close();

        return null ==! $row;
    }

    public static function isMemberStepin($user, $clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                      WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\'
                                      AND `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                      AND `stepin` = 1;');

        $row = $result->fetch_array();

        $mysql->close();

        return null ==! $row;
    }

    public static function inviteUser($clan, $user) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_invites . '` (`userId`, `clanId`) 
                            VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                                    \'' . $mysql->real_escape_string($clan->getId()) . '\');');

        $mysql->close();
    }

    /*public static function createClan($owner, $name)
    {
        $event = EventHandler::getCurrentEvent();

        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_clans . '` (`chief`, `name`, `event`) 
                    VALUES (\'' . $owner->getId() . '\', \'' . $mysql->real_escape_string($name) . '\', \'' . $event->getId() . '\');');
        
        $mysql->close();
    }*/
    
    public static function registerClan($name, $tag, $compo, $user) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        $event = EventHandler::getCurrentEvent();

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_clans . '` (`chief`, `name`, `tag`, `event`) 
                            VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                                    \'' . $mysql->real_escape_string( htmlentities($name, ENT_QUOTES, 'UTF-8') ) . '\', 
                                    \'' . $mysql->real_escape_string( htmlentities($tag, ENT_QUOTES, 'UTF-8') ) . '\', 
                                    \'' . $mysql->real_escape_string( $event->getId() ) . '\');');
        
        //Fetch the id of the clan we just added
        $fetchedId = mysqli_insert_id($mysql);

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_participantof . '` (`clanId`, `compoId`) 
                            VALUES (\'' . $mysql->real_escape_string($fetchedId) . '\', 
                                    \'' . $mysql->real_escape_string($compo) . '\');');
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`clanId`, `userId`) 
                            VALUES (\'' . $mysql->real_escape_string($fetchedId) . '\', 
                                    \'' . $mysql->real_escape_string($user->getId()) . '\');');

        $mysql->close();

        return $fetchedId;
    }

    public static function kickFromClan($user, $clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('DELETE FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\' 
                                      AND `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\';');
        
        $mysql->close();
    }
}
?>