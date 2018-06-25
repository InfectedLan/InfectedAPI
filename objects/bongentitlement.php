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
require_once 'objects/bongtype.php';
require_once 'handlers/bongtypehandler.php';
require_once 'handlers/bongentitlementhandler.php';

class BongEntitlement extends DatabaseObject {
	private $bongTypeId;
	private $entitlementType;
	private $entitlementArg;
	private $entitlementAmt;
	private $appendType;

	//Entitlement types
	const ENTITLEMENT_TYPE_USER = 0;
	const ENTITLEMENT_TYPE_CREW = 1;
	/*
	 * Append types. These control how the amount is calculated torwards the grand total
	*/
	const APPEND_TYPE_ADDITIVE = 0; // The entitlement entry will always add this amount, no matter how many of this type the user is already entitled to
	const APPEND_TYPE_EXCLUSIVE = 1; // Only the highest entitlement entry of the exclusive type will count torwards the grand total. This means you can say "all crews get two", and "tech gets three", and the higher entry will override the lower, not add to it.


	/*
	 * Returns the bong type that this entitlement is for
	 */
	public function getBongType(): BongType {
		return BongHandler::getBongType($this->bongId);
	}

	/*
	 * Returns the type of entitlement this object represents. See the ENTITLEMENT_* constants
	 */
	public function getEntitlementType(): int {
		return $this->entitlementType;
	}

	/*
	 * Returns the argument field for the given entitlement type
	 */
	public function getEntitlementArg(): int {
		return $this->entitlementArg;
	}

	/*
	 * Returns the amount of the specified bong type that this entitlement allows
	 */
	public function getEntitlementAmt(): int {
		return $this->entitlementAmt;
	}

	/*
	 * Returns the type of appending this entitlement represents. See the APPEND_TYPE_* constants.
	 */
	public function getAppendType(): int {
		return $this->appendType;
	}
}