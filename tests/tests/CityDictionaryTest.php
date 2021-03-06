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

require_once 'handlers/citydictionary.php';

/*
* Responsible for testing CityDictionary.
*/
class CityDictionaryTest extends TestCase {
    public function test() {
    	// Check if we can fetch a city name by postal code.
		$city = CityDictionary::getCity(1); // Oslo
		$this->assertEquals('Oslo', $city);

		// Check if we a city exists.
		$isCity = CityDictionary::isValidPostalCode(1); // Oslo
		$this->assertEquals(true, $isCity);

		// Get postal code when we know the city.
		$code = CityDictionary::getPostalCode('Oslo');
		$this->assertEquals(1, $code);
    }
}