<?php
require_once 'mysql.php';
require_once 'settings.php';
class Invite {
	private $id;
	private $userId;
	private $clanId;

	public function __construct($id, $userId, $clanId) {
		$this->id = $id;
		$this->userId = $userId;
		$this->clanId = $clanId;
	}

	public function getId() {
		return $this->id;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function getClanId() {
		return $this->clanId;
	}

	public function decline() {
		$con = MySQL::open(Setttings::db_name_infected_compo);

		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_compo_invites . '` WHERE `id` = ' . $this->id . ';');
	
		MySQL::close($con);
	}

	public function accept() {
		$con = MySQL::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_compo_invites . '` WHERE `id` = ' . $this->id . ';');
	
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`userId`, `clanId`) VALUES (' . $this->userId . ', ' . $this->clanId . ');');

		MySQL::close($con);
	}
}
?>