<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'settings.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/avatarhandler.php';
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
		return (int) $this->state;
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
	 * Accepts this avatar.
	 */
	public function accept() {
		AvatarHandler::acceptAvatar($this);
	}

	/*
	 * Rejects this avatar.
	 */
	public function reject() {
		AvatarHandler::rejectAvatar($this);
	}

	/*
	 * Removes this avatar.
	 */
	public function remove() {
		AvatarHandler::removeAvatar($this);
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