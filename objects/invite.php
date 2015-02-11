<?php
require_once 'mysql.php';
require_once 'settings.php';
require_once 'objects/object.php';
require_once 'handlers/clanhandler.php';

class Invite extends Object{
	private $userId;
	private $clanId;

	public function getUserId() {
		return $this->userId;
	}

	public function getClanId() {
		return $this->clanId;
	}

	public function decline() {
		$con = MySQL::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_compo_invites . '` WHERE `id` = ' . $this->getId() . ';');
	
		MySQL::close($con);
	}

	public function accept() {
		$con = MySQL::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_compo_invites . '` WHERE `id` = ' . $this->getId() . ';');

		$clan = ClanHandler::getClan($this->getClanId());

		$memberList = ClanHandler::getPlayingMembers($clan);
		$compo = ClanHandler::getCompo($clan);

		if (count($memberList) < $compo->getTeamSize()) {
			mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`userId`, `clanId`, `stepin`) VALUES (' . $this->userId . ', ' . $this->clanId . ', 0);');
		} else {
			mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`userId`, `clanId`, `stepin`) VALUES (' . $this->userId . ', ' . $this->clanId . ', 1);');
		}

		MySQL::close($con);
	}
}
?>