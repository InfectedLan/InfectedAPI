<?php
require_once 'handlers/userhandler.php';

class Avatar {
	private $id;
	private $userId;
	private $relativeUrl;
	private $state;

	public function Avatar($id, $userId, $relativeUrl, $state) {
		$this->id = $id;
		$this->userId = $userId;
		$this->relativeUrl = $relativeUrl;
		$this->state = $state;
	}

	public function getId() {
		return $this->id;
	}
	
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	public function getRelativeUrl() {
		return $this->relativeUrl;
	}

	public function getState() {
		return $this->state;
	}
	
	public function setState($newstatus) {
		if (is_bool($newstatus)) {
			$con = MySQL::open(Settings::db_name_infected_crew);
			
			mysqli_query($con, 'UPDATE' . Settings::db_table_infected_crew_teams . ' SET `state` = ' . $newstatus . ' WHERE id = \'' . $this->getId() . '\'');
			$state = $newstatus;
			
			$this->mysql->close($con);
		}
	}
}
?>