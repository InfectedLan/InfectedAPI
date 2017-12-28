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
class BongType extends DatabaseObject {
	private $userId;
	private $name;
	
	/*
	 * Returns the name of the bong type
	 */
	public function getName() {
		return $this->name;
	}
}
?>