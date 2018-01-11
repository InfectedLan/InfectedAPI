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
use PHPUnit\Framework\TestCase;

require_once 'handlers/bonghandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/eventhandler.php';
require_once 'database.php';

/* 
 * BongTest
 *
 * Tests the integrity of the bong system
 *
 */
class BongTest extends TestCase {
	public function test() {
		$this->creationTest();
		$this->cleanup();
	}

	private function creationTest() {
		$bongs = BongHandler::getBongTypes(); //Current event
		$this->assertEquals(count($bongs), 3);
	}

	private function cleanup() {

	}
}
?>