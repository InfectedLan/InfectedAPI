<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/invite.php';

class InviteHandler {
    /*
     * Get a invite by the internal id.
     */
    public static function getInvite($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_invites . '` 
                                 WHERE `id` = \'' . $id . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('Invite');
    }
	
    /*
     * Get all invites for the specified user.
     */
    public static function getInvitesForUser($user) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `'  . Settings::db_table_infected_compo_invites . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();

        $inviteList = array();

        while ($object = $result->fetch_object('Invite')) {
            array_push($inviteList, $object);
        }

        return $inviteList;
    }

    /*
     * Get all invites that is to a clan.
     */
    public function getInvitedInClan($clan) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_invites . '` 
                                 WHERE `clanId` = \'' . $mysql->real_escape_string( $clan->getId() ) . '\';');
        
        $mysql->close();

        $inviteList = array();

        while ($object = $result->fetch_object('Invite')) {
            array_push($inviteList, $object);
        }

        return $userList;
    }
}
?>