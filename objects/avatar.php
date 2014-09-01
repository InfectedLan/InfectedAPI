<?php
require_once 'handlers/userhandler.php';
require_once 'settings.php';

class Avatar {
	private $id;
	private $userId;
	private $file;
	private $state;

	public function __construct($id, $userId, $file, $state) {
		$this->id = $id;
		$this->userId = $userId;
		$this->file = $file;
		$this->state = $state;
	}

	public function getId() {
		return $this->id;
	}
	
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	public function getHd() {
		return Settings::avatar_path . 'hd/' . $this->file;
	}
	
	public function getSd() {
		return Settings::avatar_path . 'sd/' . $this->file;
	}
	
	public function getThumbnail() {
		return Settings::avatar_path . 'thumbnail/' . $this->file;
	}

	// Only use if state = 0
	public function getTemp() {
		return Settings::avatar_path . 'temp/' . $this->file;
	}

	public function getState() {
		return $this->state;
	}
	
	// TODO: We don't make SQL queries in object files.
	public function setState($newstatus) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_avatars . '` SET `state` = ' . $con->real_escape_string($newstatus) . ' WHERE id = \'' . $this->getId() . '\'');
		$this->state = $newstatus;
		
		MySQL::close($con);
	}

	public function getFileName() {
		return $this->file;
	}
	
	// TODO: We don't make SQL queries in object files.
 	public function setFileName($newName) {
 		$con = MySQL::open(Settings::db_name_infected_crew);

 		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_avatars . '` SET `file` = \'' . $con->real_escape_string($newName) . '\' WHERE `id`=' . $this->id . ';');
		$this->file  = $newName;

		MySQL::close($con);
	}

	//Do not use
	public function deleteFiles() {
		if ($this->state == 0) {
			//This picture is not cropped
			unlink(Settings::api_path . $this->getTemp());
		} else {
			unlink(Settings::api_path . $this->getSd());
			unlink(Settings::api_path . $this->getHd());
			unlink(Settings::api_path . $this->getThumbnail());
		}
	}
}
?>