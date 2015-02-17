<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'objects/eventobject.php';

class Clan extends EventObject {
	private $name;
	private $tag;
	private $chiefId;

	/*
	 * Return the name of this clan.
	 */
	public function getName() {
		return $this->name;
	}

	/*
	 * Return the tag of this clan.
	 */
	public function getTag() {
		return $this->tag;
	}

	/*
	 * Return the chief of this clan.
	 */
	public function getChief() {
		return UserHandler::getUser($this->chiefId);
	}

	/*
	 * Returns a list of all the clan members.
	 */
	public function getMembers() {
		return ClanHandler::getMembers($this);
	}

	/*
	 * Returns true if this clan is qualified for specified compo.
	 */
	public function isQualified($compo) {
		$primaryPlayers = ClanHandler::getPlayingMembers($this);

		if (count($primaryPlayers) != $compo->getTeamSize()) {
			return false;
		}

		$mysql = MySQL::open(Settings::db_name_infected_compo);

		$result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_participantof . '` 
								 WHERE `clanId` = \'' . $this->getId() . '\' 
								 AND `compoId` = \'' . $mysql->real_escape_string($compo->getId()) . '\';');

		$mysql->close();

		return $result->num_rows > 0;
	}
}
?>