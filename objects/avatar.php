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
	private $fileName;
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
		return Settings::avatar_path . 'hd/' . $this->getFileName();
	}

	/*
	 * Returns the avatar image in SD.
	 */
	public function getSd() {
		return Settings::avatar_path . 'sd/' . $this->getFileName();
	}

	/*
	 * Returns the avatar image as thumbnail.
	 */
	public function getThumbnail() {
		return Settings::avatar_path . 'thumbnail/' . $this->getFileName();
	}

	/*
	 * Returns the avatar temporarily image.
	 */
	public function getTemp() {
		return Settings::avatar_path . 'temp/' . $this->getFileName();
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
	public function setState($state) {
		$this->state = $state;

		AvatarHandler::updateAvatar($this, $state, $this->getFileName());
	}

	/*
	 * Returns the filename of this avatar.
	 */
	public function getFileName() {
		return $this->fileName;
	}

	/*
	 * Sets the filename of this avatar.
	 */
 	public function setFileName($fileName) {
		$this->fileName = $fileName;

 		AvatarHandler::updateAvatar($this, $this->getState(), $fileName);
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
