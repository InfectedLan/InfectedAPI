<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'objects/emergencycontact.php';
require_once 'objects/object.php';

class EmergencyContact extends Object {
	private $userId;
	private $phone;
	
	/* 
	 * Returns associated user.
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}
	
	/* 
	 * Returns the phone number.
	 */
	public function getPhone() {
		return $this->phone;
	}
	
	/* 
	 * Returns the phone number formatted as a string.
	 */
	public function getPhoneAsString() {
		return !empty($this->phone) ? chunk_split($this->phone, 2, ' ') : 'Ikke oppgitt';
	}
}
?>