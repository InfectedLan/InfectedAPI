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

require_once 'objects/databaseobject.php';

class NetworkType extends DatabaseObject {
	private $name;
	private $title;
	private $portType;

	/*
	 * Returns the name of this network type.
	 */
	public function getName(): string {
		return $this->name;
	}

	/*
	 * Returns the title of this network type.
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/*
	 * Returns the description of this network.
	 */
	public function getPortType(): string {
		return $this->portType;
	}
}