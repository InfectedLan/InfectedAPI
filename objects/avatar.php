<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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
require_once 'handlers/avatarhandler.php';
require_once 'handlers/userhandler.php';
require_once 'objects/databaseobject.php';

class Avatar extends DatabaseObject {
	private $userId;
	private $fileName;
	private $state;

	/*
	 * Returns the user this avatar belongs to.
	 */
	public function getUser(): User {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the filename of this avatar.
	 */
	public function getFileName(): string {
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
	 * Returns the avatar image file type.
	 */
	public function getFile($quality): string {
		return Settings::avatar_path . $quality . '/' . $this->fileName;
	}

	/*
	 * Returns the avatar image in HD.
	 */
	public function getHd(): string {
		return $this->getFile('hd');
	}

	/*
	 * Returns the avatar image in SD.
	 */
	public function getSd(): string {
		return $this->getFile('sd');
	}

	/*
	 * Returns the avatar image as thumbnail.
	 */
	public function getThumbnail(): string {
		return $this->getFile('thumbnail');
	}

	/*
	 * Returns the avatar temporarily image.
	 */
	public function getTemp(): string {
		return $this->getFile('temp');
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

	/*
	* Returns the state of this avatar
	*/
	public function getState(): int {
		return $this->state;
	}

	/*
	* Sets the state of this avatar
	*/
	public function setState($state) {
		$this->state = $state;

		AvatarHandler::updateAvatar($this, $state, $this->getFileName());
	}
}