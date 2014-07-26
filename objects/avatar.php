<?php
require_once 'handlers/userhandler.php';

class Avatar {
	private $id;
	private $userId;
	private $file;
	private $state;

	public function __construct($id, $userId, $file, $state) {
		$this->id = $id;
		$this->userId = $userId;
		$this->path = 'images/avatars/';
		
		if (!file_exists($path . $file) || $file == null) {
			$user = UserHandler::getUser($userId);
		
			if ($user->getAge() >= 18) {
				if ($user->getGender() == 0) {
					$file = 'default_gutt.png';
				} else {
					$file = 'default_jente.png';
				}
			} else {
				$file = 'default_child.png';
			}
		}
		
		$this->file = $file;
		$this->state = $state;
	}

	public function getId() {
		return $this->id;
	}
	
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	public function getFile() {
		return path . $this->file;
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