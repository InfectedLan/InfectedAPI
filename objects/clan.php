<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'objects/object.php';

class Clan extends Object {
	private $chief;
	private $name;
	private $event;
	private $tag;

	public function __construct($id, $chief, $name, $event, $tag) {
		parent::__construct($id);
		
		$this->chief = $chief;
		$this->name = $name;
		$this->event = $event;
		$this->tag = $tag;
	}

	public function getChief() {
		return $this->chief;
	}

	public function getName() {
		return $this->name;
	}

	public function getTag() {
		return $this->tag;
	}

	public function getEvent() {
		return $this->event;
	}

	public function getMembers() {
		return ClanHandler::getMembers($this);
	}

	public function isQualified($compo) {
		$primaryPlayers = ClanHandler::getPlayingMembers($this);

		if (count($primaryPlayers) != $compo->getTeamSize()) {
			return false;
		}

		$mysql = MySQL::open(Settings::db_name_infected_compo);

		$result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantof . '` WHERE `clanId` = ' . $this->id . ' AND `compoId` = ' . $con->real_escape_string($compo->getId()) . ';');

		$mysql->close();

		return $row = mysqli_fetch_array($result);
	}
}
?>