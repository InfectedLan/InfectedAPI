<?php
require_once 'database.php';
require_once 'settings.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'objects/object.php';

class Invite extends Object {
	private $userId;
	private $clanId;

	/*
	 * Returns the user that this invite is for.
	 */ 
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the clan this invite is to.
	 */ 
	public function getClan() {
		return ClanHandler::getClan($this->clanId);
	}

	/*
	 * Decline this invite.
	 */ 
	public function decline() {
		$con = Database::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_compo_invites . '`
							WHERE `id` = \'' . $this->getId() . '\';');
	
		Database::close($con);
	}

	/*
	 * Accept this invite.
	 */ 
	public function accept() {
		$con = Database::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_compo_invites . '`
							WHERE `id` = \'' . $this->getId() . '\';');

		$clan = $this->getClan();

		$memberList = ClanHandler::getPlayingMembers($clan);
		$compo = ClanHandler::getCompo($clan);

		if (count($memberList) < $compo->getTeamSize()) {
			$database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`userId`, `clanId`, `stepInId`) 
							  VALUES (' . $this->getUser()->getId() . ', 
									  ' . $clan->getId() . ', 0);');
		} else {
			$database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`userId`, `clanId`, `stepInId`) 
							  VALUES (\'' . $this->getUser()->getId() . '\', 
									  \'' . $clan->getId() . '\', 
									  \'1\');');
		}

		$database->close();
	}
}
?>