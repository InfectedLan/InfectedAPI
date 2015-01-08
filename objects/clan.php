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
		$nonStepinCount = 0;
		$members = $this->getMembers();
		foreach ($members as $member) {
			if(!$member->isStepin()) {
				$nonStepinCount++;
			}
		}
		if($nonStepinCount != $compo->getTeamSize())
			return false;

		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_participantof . '` WHERE `clanId` = ' . $this->id . ' AND `compoId` = ' . $con->real_escape_string($compo->getId()) . ';');

		MySQL::close($con);

		return $row = mysqli_fetch_array($result);
	}
}
?>