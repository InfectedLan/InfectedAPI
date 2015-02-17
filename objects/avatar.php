<?php
require_once 'settings.php';
require_once 'handlers/userhandler.php';
require_once 'objects/object.php';

class Avatar extends Object {
	private $userId;
	private $file;
	private $state;
	
	/*
	 * Returns the user this avatar belongs to.
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the avatar image in HD.
	 */
	public function getHd() {
		return Settings::avatar_path . 'hd/' . $this->file;
	}
	
	/*
	 * Returns the avatar image in SD.
	 */
	public function getSd() {
		return Settings::avatar_path . 'sd/' . $this->file;
	}
	
	/*
	 * Returns the avatar image as thumbnail.
	 */
	public function getThumbnail() {
		return Settings::avatar_path . 'thumbnail/' . $this->file;
	}

	/*
	 * Returns the avatar temporarily image.
	 */
	public function getTemp() {
		return Settings::avatar_path . 'temp/' . $this->file;
	}

	/*
	 * Returns the state of this avatar.
	 */
	public function getState() {
		return $this->state;
	}

	/*
	 * Returns the avatar temporarily image.
	 */
	public function setState($newstatus) { // TODO: We don't make SQL queries in object files.
		$database = Database::open(Settings::db_name_infected_crew);
		
		$database->query('UPDATE `' . Settings::db_table_infected_crew_avatars . '` 
					      SET `state` = ' . $con->real_escape_string($newstatus) . ' 
					      WHERE id = \'' . $this->getId() . '\'');

		$this->state = $newstatus;
		
		$database->close();
	}

	/*
	 * Returns the filename of this avatar.
	 */
	public function getFileName() {
		return $this->file;
	}
	
	/*
	 * Sets the filename of this avatar.
	 */
 	public function setFileName($newName) { // TODO: We don't make SQL queries in object files.
 		$database = Database::open(Settings::db_name_infected_crew);

 		$database->query('UPDATE `' . Settings::db_table_infected_crew_avatars . '` 
 						  SET `file` = \'' . $con->real_escape_string($newName) . '\' 
 						  WHERE `id` = \'' . $this->getId() . '\';');
		
		$this->file  = $newName;

		$database->close();
	}

	/*
	 * Delete all files for this avatar.
	 */
	public function deleteFiles() { // Do not use.
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