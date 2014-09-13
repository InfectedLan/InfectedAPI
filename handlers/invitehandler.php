<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/invite.php';
class InviteHandler {
	public static function getInvite($id) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_invites . '` WHERE `id` = \'$id\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if($row) {
			return new Invite($row['id'], $row['userId'], $row['clanId']);
		}
	}
	public static function getInvitesForUser($user) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `'  . Settings::db_table_infected_compo_invites . '` WHERE `userId` = ' . $user->getId() . ';');
	
		$inviteList = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($inviteList, self::getInvite($row['id']) );
		}

		MySQL::close($con);

		return $inviteList;
	}
}
?>