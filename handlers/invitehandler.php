<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/invite.php';
require_once 'objects/user.php';
require_once 'objects/clan.php';

class InviteHandler {
    /*
     * Get a invite by the internal id.
     */
    public static function getInvite($id) {
        $database = Database::open(Settings::db_name_infected_compo);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_invites . '` 
                                    WHERE `id` = \'' . $id . '\';');
        
        $database->close();
		
		return $result->fetch_object('Invite');
    }
	
    /*
     * Get all invites for the specified user.
     */
    public static function getInvitesForUser(User $user) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT * FROM `'  . Settings::db_table_infected_compo_invites . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\';');
        
        $database->close();

        $inviteList = array();

        while ($object = $result->fetch_object('Invite')) {
            array_push($inviteList, $object);
        }

        return $inviteList;
    }

    /*
     * Get all invites that is to a clan.
     */
    public function getInvitedInClan(Clan $clan) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_invites . '` 
                                    WHERE `clanId` = \'' . $clan->getId() . '\';');
        
        $database->close();

        $inviteList = array();

        while ($object = $result->fetch_object('Invite')) {
            array_push($inviteList, $object);
        }

        return $userList;
    }
}
?>