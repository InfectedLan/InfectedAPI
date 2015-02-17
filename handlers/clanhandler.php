<?php
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
    public static function getClansForUser(User $user, Event $event) {
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
     * Get invites for specified clan.
     */
    public static function getInvites(Clan $clan) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_invites . '` 
                                    WHERE `clanId` = \'' . $clan->getId() . '\';');

        $database->close();

        $inviteList = array();

        while ($object = $result->fetch_object('Invite')) {
            array_push($inviteList, $object);
        }

        return $inviteList;
    }

    /*
     * Get members for specified clan.
     */
    public static function getMembers(Clan $clan) {
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
    public static function getPlayingMembers(Clan $clan) {
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
    public static function getStepinMembers(Clan $clan) {
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
    public static function isMember(User $user, Clan $clan) {
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
    public static function isMemberStepin(User $user, Clan $clan) {
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
    public static function setMemberStepinState(Clan $clan, User $user, $state) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('UPDATE `' . Settings::db_table_infected_compo_memberof . '` 
                                    SET `stepInId` = \'' . $database->real_escape_string($state) . '\'
                                    WHERE `clanId` = \'' . $clan->getId() . '\'
                                    AND `userId` = \'' . $user->getId() . '\';');

        $database->close();
    }

    /*
     * Invite the specified user to the specifed clan.
     */
    public static function inviteUser(Clan $clan, User $user) {
        $database = Database::open(Settings::db_name_infected_compo);

        $database->query('INSERT INTO `' . Settings::db_table_infected_compo_invites . '` (`userId`, `clanId`) 
                          VALUES (\'' . $user->getId() . '\', 
                                  \'' . $clan->getId() . '\');');

        $database->close();
    }

    /*
     * Kick a specified member from specified clan.
     */
    public static function kickFromClan(User $user, Clan $clan) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('DELETE FROM `' . Settings::db_table_infected_compo_memberof . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\' 
                                    AND `clanId` = \'' . $clan->getId() . '\';');
    
        $database->close();
    }
    
    /*
     * Register a new clan.
     */
    public static function registerClan(Event $event, $name, $tag, Compo $compo, User $user) {
        $database = Database::open(Settings::db_name_infected_compo);

        $database->query('INSERT INTO `' . Settings::db_table_infected_compo_clans . '` (`eventId`, `chiefId`, `name`, `tag`) 
                          VALUES (\'' . $event->getId() . '\', 
                                  \'' . $user->getId() . '\', 
                                  \'' . $database->real_escape_string(htmlentities($name, ENT_QUOTES, 'UTF-8')) . '\', 
                                  \'' . $database->real_escape_string(htmlentities($tag, ENT_QUOTES, 'UTF-8')) . '\');');
        
        // Fetch the id of the clan we just added
        $fetchedId = $database->insert_id;

        $database->query('INSERT INTO `' . Settings::db_table_infected_compo_participantof . '` (`clanId`, `compoId`) 
                          VALUES (\'' . $database->real_escape_string($fetchedId) . '\', 
                                  \'' . $compo->getId() . '\');');
        
        $database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`clanId`, `userId`) 
                          VALUES (\'' . $database->real_escape_string($fetchedId) . '\', 
                                  \'' . $user->getId() . '\');');

        // Allow user to talk in global chat.
        $mainChat = ChatHandler::getChat(1); // TODO: Change this to the first chat in the array?
        ChatHandler::addChatMember($user, $mainChat);

        $database->close();

        return $fetchedId;
    }
}
?>