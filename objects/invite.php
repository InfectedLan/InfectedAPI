<?php
require_once 'mysql.php';
require_once 'settings.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'objects/object.php';

class Invite extends Object {
	private $userId;
	private $clanId;

	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	public function getClan() {
		return ClanHandler::getClan($this->clanId);
	}

	public function decline() {
		$con = MySQL::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_compo_invites . '`
							WHERE `id` = \'' . $this->getId() . '\';');
	
		MySQL::close($con);
	}

	public function accept() {
		$con = MySQL::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_compo_invites . '`
							WHERE `id` = \'' . $this->getId() . '\';');

		$clan = $this->getClan();

		$memberList = ClanHandler::getPlayingMembers($clan);
		$compo = ClanHandler::getCompo($clan);

		if (count($memberList) < $compo->getTeamSize()) {
			$mysql->query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`userId`, `clanId`, `stepin`) 
								 VALUES (' . $this->getUser()->getId() . ', 
									     ' . $clan->getId() . ', 0);');
		} else {
			$mysql->query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`userId`, `clanId`, `stepin`) 
								 VALUES (' . $this->getUser()->getId() . ', 
									     ' . $clan->getId() . ', 1);');
		}

		$mysql->close();
	}
}
?>