<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/clan.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/invitehandler.php';
require_once 'handlers/chathandler.php';

class ClanHandler {
    const STATE_MAIN_PLAYER = 0;
    const STATE_STEPIN_PLAYER = 1;

    /*
     * Get a clan by the internal id.
     */
    public static function getClan($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_clans . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();

        return $result->fetch_object('Clan');
    }

    /*
     * Get clan for a specified user.
     */
    public static function getClansForUser($user, $event) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_clans . '` 
                                 WHERE `id` = (SELECT `clanId` FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                               WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\');');

        $mysql->close();

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
    public static function getCompo($clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_compo . '`
                                 WHERE `id` = (SELECT `compoId` FROM `' . Settings::db_table_infected_compo_participantof . '` 
                                               WHERE `clanId` = \'' . $mysql->real_escape_string( $clan->getId() ) . '\');');

        $mysql->close();

        return $result->fetch_object('Compo');
    }

    /*
     * Get invites for specified clan.
     */
    public static function getInvites($clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_invites . '` 
                                 WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\';');

        $mysql->close();

        $inviteList = array();

        while ($object = $result->fetch_object('Invite')) {
            array_push($inviteList, $object);
        }

        return $inviteList;
    }

    /*
     * Get members for specified clan.
     */
    public static function getMembers($clan) {
        $mysql = MySQL::open(Settings::db_name_infected);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_users . '` 
                                 WHERE `id` = (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . Settings::db_table_infected_compo_memberof . '` 
                                               WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\');');

        $mysql->close();

        $memberList = array();

        while ($object = $result->fetch_object('User')) {
            array_push($memberList, $object);
        }

        return $memberList;
    }

    /*
     * Get playing members for specified clan.
     */
    public static function getPlayingMembers($clan) {
        $mysql = MySQL::open(Settings::db_name_infected);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_users . '` 
                                 WHERE `id` = (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . Settings::db_table_infected_compo_memberof . '` 
                                               WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\'
                                               AND `stepin` = \'0\');');

        $mysql->close();

        $memberList = array();

        while ($object = $result->fetch_object('User')) {
            array_push($memberList, $object);
        }

        return $memberList;
    }

    /*
     * Get step in members for specified clan.
     */
    public static function getStepinMembers($clan) {
        $mysql = MySQL::open(Settings::db_name_infected);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_users . '` 
                                 WHERE `id` = (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . Settings::db_table_infected_compo_memberof . '` 
                                               WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\'
                                               AND `stepin` = \'1\');');
        $mysql->close();

        $memberList = array();

        while ($object = $result->fetch_object('User')) {
            array_push($memberList, $object);
        }

        return $memberList;
    }

    /*
     * Returns true of the specified user is member of the specified clan.
     */
    public static function isMember($user, $clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                 WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\'
                                 AND `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');

        $mysql->close();

        $row = $result->fetch_array();

        return $row ? true : false;
    }

    /*
     * Return true if the specified user is a stepin member.
     */
    public static function isMemberStepin($user, $clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                 WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\'
                                 AND `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                 AND `stepin` = \'1\';');

        $mysql->close();

        $row = $result->fetch_array();

        return $row ? true : false;
    }

    /*
     * Set the step in state of a member.
     */
    public static function setMemberStepinState($clan, $user, $state) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('UPDATE `' . Settings::db_table_infected_compo_memberof . '` 
                                 SET `stepin` = \'' . $mysql->real_escape_string($state) . '\'
                                 WHERE `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\'
                                 AND `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');

        $mysql->close();
    }

    /*
     * Invite the specified user to the specifed clan.
     */
    public static function inviteUser($clan, $user) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_invites . '` (`userId`, `clanId`) 
                       VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                               \'' . $mysql->real_escape_string($clan->getId()) . '\');');

        $mysql->close();
    }

    /*
     * Kick a specified member from specified clan.
     */
    public static function kickFromClan($user, $clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('DELETE FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\' 
                                 AND `clanId` = \'' . $mysql->real_escape_string($clan->getId()) . '\';');
        
        $mysql->close();
    }
    
    /*
     * Register a new clan.
     */
    public static function registerClan($name, $tag, $compo, $user) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_clans . '` (`chief`, `name`, `tag`, `event`) 
                       VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                               \'' . $mysql->real_escape_string(htmlentities($name, ENT_QUOTES, 'UTF-8')) . '\', 
                               \'' . $mysql->real_escape_string(htmlentities($tag, ENT_QUOTES, 'UTF-8')) . '\', 
                               \'' . $mysql->real_escape_string(EventHandler::getCurrentEvent()->getId()) . '\');');
        
        // Fetch the id of the clan we just added
        $fetchedId = $mysql->insert_id;

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_participantof . '` (`clanId`, `compoId`) 
                       VALUES (\'' . $mysql->real_escape_string($fetchedId) . '\', 
                               \'' . $mysql->real_escape_string($compo) . '\');');
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`clanId`, `userId`) 
                       VALUES (\'' . $mysql->real_escape_string($fetchedId) . '\', 
                               \'' . $mysql->real_escape_string($user->getId()) . '\');');

        // Allow user to talk in global chat.
        $mainChat = ChatHandler::getChat(1); // TODO: Change this to the first chat in the array?
        ChatHandler::addChatMember($user, $mainChat);

        $mysql->close();

        return $fetchedId;
    }
}
?>