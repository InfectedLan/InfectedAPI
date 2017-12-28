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

use PHPUnit\Framework\TestCase;

require_once 'handlers/permissionhandler.php';

/*
* Responsible for testing PermissionHandler, and the Permission object.
*/
class PermissionTest extends TestCase {
    public function test() {
    	// Check if we can fetch a instance of a permission object by id.
		$permissionById = PermissionHandler::getPermission(1);
		$this->assertNotEquals(null, $permissionById);

		// Check if we can fetch a instance of a permission object by value.
		$permissionByValue = PermissionHandler::getPermissionByValue('*');
		$this->assertNotEquals(null, $permissionByValue);

		// Check that permission count is greater than zero.
		$permissions = PermissionHandler::getPermissions();
        $this->assertGreaterThan(0, count($permissions));

		// Check that permission by values count is greater than zero.
		$permissionsByValues = PermissionHandler::getPermissionsByValues([1, 2, 3, 4]);
		$this->assertGreaterThan(0, count($permissionsByValues));
    }
}