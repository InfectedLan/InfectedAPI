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

require_once 'objects/databaseobject.php';
require_once 'handlers/userhandler.php';

/*
 * Motivations behind this:
 * - A card should be able to be re-used on multiple events, even if the user isn't the same
 * - Thats about it
 */
class NfcCard extends DatabaseObject {
	private $userId;
	private $eventId;
	private $nfcId;
	
	/*
	 * Returns the user assigned to this card
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the event this card entry is assigned to
	 */
	public function getEvent() {
		return EventHandler::getEvent($this->eventId);
	}

	/*
	 * Returns the card id connected to this entry
	 */
	public function getNfcId() {
		return $this->nfcId;
	}
}
?>