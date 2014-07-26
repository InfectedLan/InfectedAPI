<?php
require_once 'handlers/userhandler.php';

class Avatar {
	private $id;
	private $userId;
	private $relativeUrl;
	private $state;

	public function __construct($id, $userId, $relativeUrl, $state) {
		$this->id = $id;
		$this->userId = $userId;
		
		$relativeUrlPath = 'images/avatars/';
		
		if (!file_exists($relativeUrlPath . $relativeUrl) ||
			$relativeUrl == null) {
			$user = UserHandler::getUser($userId);
		
			if ($user->getAge() >= 18) {
				if ($user->getGender() == 0) {
					$relativeUrl = 'default_gutt.png';
				} else {
					$relativeUrl = 'default_jente.png';
				}
			} else {
				$relativeUrl = 'default_child.png';
			}
		}
		
		$this->relativeUrl = $relativeUrlPath . $relativeUrl;
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
			
			MYSQL::close($con);
		}
	}
}
?>