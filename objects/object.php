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

class Object {
	protected $id;

	/*
	 * Retuns the internal id for this object.
	 */
	public function getId() {
		return $this->id;
	}

	/*
	 * Compare is a function that allows you to easily compare this object with the specified one.
	 * It returns true if the specified object is equal to this one.
	 */
	public function equals(Object $object) {
		// Check that the specified object is an instance of this one.
		if ($object instanceof $this) {
			// Logically compare these two objects against each other.
			return $object == $this;
		}

		return false;
	}

	/*
	 * Compare is a function that allows you to easily compare this object with the specified one.
	 * It returns true if the specified object is less than this one.
	 */
	public function isLessThan(Object $object) {
		// Check that the specified object is an instance of this one.
		if ($object instanceof $this) {
			// Logically compare these two objects against each other.
			return $this->getId() < $object->getId();
		}

		return false;
	}

	/*
	 * Compare is a function that allows you to easily compare this object with the specified one.
	 * It returns true if the specified object is greater than this one.
	 */
	public function isGreaterThan(Object $object) {
		return !$this->isLessThan($object);
	}
}
?>
