<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <https://infected.no/>.
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
require_once 'handlers/eventhandler.php';

/*
 * A bong type is an item in the store which can be purchased or, in this case, be provided in a limited supply to entitled individuals
 */
class BongType extends DatabaseObject {
	private $name;
	private $description;
	private $eventId;
	
	/*
	 * Returns the name of the bong type
	 */
	public function getName() {
		return $this->name;
	}

	/*
	 * Returns the description of the bong type
	 */
	public function getDescription() {
		return $this->description;
	}

	/*
	 * Returns the event that this bong type is connected to
	 */
	public function getEvent() {
		return EventHandler::getEvent($this->eventId);
	}
}
?>