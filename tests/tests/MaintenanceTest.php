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
use PHPUnit\Framework\TestCase;

require_once 'database.php';
require_once 'maintenance.php';

/* 
 * MaintenanceTest
 *
 * Tests the maintenance mode system
 *
 */
class MaintenanceTest extends TestCase {
	public function test() {
		$this->mainTest();
		$this->cleanup();
	}

	private function mainTest() {
        Maintenance::loadMaintenanceState();
        $this->assertEquals(false, Maintenance::isMaintenance());
        Maintenance::setMaintenance(10);
        $this->assertEquals(true, Maintenance::isMaintenance());
        Maintenance::disableMaintenance();
        $this->assertEquals(false, Maintenance::isMaintenance());
	}

	private function cleanup() {

	}
}
?>
