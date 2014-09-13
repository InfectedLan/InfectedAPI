<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/userhandler.php';
class Clan {
	private $id;
	private $chief;
	private $name;
	private $event;

	public function __construct($id, $chief, $name, $event) {
		$this->id = $id;
		$this->chief = $chief;
		$this->name = $name;
		$this->event = $event;
	}

	public function getId() {
		return $this->id;
	}

	public function getChief() {
		return $this->chief;
	}

	public function getName() {
		return $this->name;
	}

	public function getEvent() {
		return $this->event;
	}

	public function getMembers() {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` WHERE `clanId` = ' . $this->id . ';');
	
		$userList = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($userList, UserHandler::getUser($row['userId']) );
		}

		MySQL::close($con);
		return $userList;
	}
}
?>