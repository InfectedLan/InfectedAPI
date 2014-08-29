<?php
require_once 'handlers/userhandler.php';
require_once 'settings.php';

class Avatar {
	private $id;
	private $userId;
	private $file;
	private $state;
	private $defaultState;

	public function __construct($id, $userId, $file, $state) {
		$this->id = $id;
		$this->userId = $userId;
		
		if (!file_exists(Settings::api_path . Settings::avatar_path . "hd/" . $file) || $file == null) {
			$user = UserHandler::getUser($userId);
		
			if ($user->getAge() >= 18) {
				if ($user->getGender() == 0) {
					$file = 'default_gutt.png';
					$defaultState = 1;
				} else {
					$file = 'default_jente.png';
					$defaultState = 2;
				}
			} else {
				$file = 'default_child.png';
				$defaultState = 3;
			}
		} else {
			$defaultState = 0;
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
		return $self->getSd();
	}

	public function getHd() {
		if($defaultState == 0) {
			return Settings::avatar_path . "hd/" . $this->file;
		} else {
			return Settings::avatar_path . "default/" . $this->file;
		}
	}

	public function getSd() {
		if($defaultState == 0) {
			return Settings::avatar_path . "sd/" . $this->file;
		} else {
			return Settings::avatar_path . "default/" . $this->file;
		}
	}

	public function getThumbnail() {
		if($defaultState == 0) {
			return Settings::avatar_path . "thumb/" . $this->file;
		} else {
			return Settings::avatar_path . "default/" . $this->file;
		}
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