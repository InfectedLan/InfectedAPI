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

require_once 'settings.php';
require_once 'database.php';
require_once 'objects/bongentitlement.php';
require_once 'handlers/eventhandler.php';

class BongEntitlementHandler {
	/*
	 * Returns the gate with the given id.
	 */
	public static function getBongEntitlement($id) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_bongEntitlements . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('BongEntitlement');
	}

	/*
	 * Creates a bong entitlement
	 */
	public static function createBongEntitlement(BongType $type, $amount, $appendType, $entitlementType, $entitlementArg) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('INSERT INTO `' . Settings::db_table_infected_bongEntitlements . '` (`bongId`, `entitlementType`, `entitlementArg`, `entitlementAmt`, `appendType`) VALUES (' . $type->getId() . ', ' . $database->real_escape_string($entitlementType) . ', ' . $database->real_escape_string($entitlementArg) . ', ' . $database->real_escape_string($entitlementAmt) . ', ' . $database->real_escape_string($appendType) . ');');

		return self::getBongEntitlement($database->insert_id);
	}

	/*
	 * Returns a list of all bong entitlements
	 */

	/*
	 * Calculates how much of a bong type a certain user has left
	 */
	public static function calculateBongEntitlementByUser(BongType $type, User $user, Event $event = null) {
		if($event==null) {
			$event = EventHandler::getCurrentEvent();
		}

		$database = Database::getConnection(Settings::db_name_infected);

		$exclusiveNum = 0;
		$additiveNum = 0;

		//First, we find out how much of the bong that this user is entitled to
		$personalEntitlements = $database->query('SELECT * FROM `' . Settings::db_table_infected_bongEntitlements . '` WHERE `bongId` = ' . $type->getId() . ' AND `entitlementType` = ' . self::ENTITLEMENT_TYPE_USER . ' AND `entitlementArgs` = ' . $user->getId() . ';');

		while($obj = $personalEntitlements->fetch_object('BongEntitlement')) {
			if($obj->getAppendType()==BongEntitlement::APPEND_TYPE_ADDITIVE) {
				$additiveNum += $obj->getEntitlementAmt();
			} else if($obj->appendType()==BongEntitlement::APPEND_TYPE_EXCLUSIVE) {
				if($obj->getEntitlementAmt() > $exclusiveNum) {
					$exclusiveNum = $obj->getEntitlementAmt();
				}
			}
		}

		if($user->isGroupMember()) {
			$group = $user->getGroup($event);
			$groupEntitlements = $database->query('SELECT * FROM `' . Settings::db_table_infected_bongEntitlements . '` WHERE `bongId` = ' . $type->getId() . ' AND `entitlementType` = ' . self::ENTITLEMENT_TYPE_CREW . ' AND (`entitlementArgs` = ' . $group->getId() . ' OR `entitlementArgs` = 0);');
			while($obj = $groupEntitlements->fetch_object('BongEntitlement')) {
				if($obj->getAppendType()==BongEntitlement::APPEND_TYPE_ADDITIVE) {
					$additiveNum += $obj->getEntitlementAmt();
				} else if($obj->appendType()==BongEntitlement::APPEND_TYPE_EXCLUSIVE) {
					if($obj->getEntitlementAmt() > $exclusiveNum) {
						$exclusiveNum = $obj->getEntitlementAmt();
					}
				}
			}
		}

		return $exclusiveNum+$additiveNum;

	}

}
?>
