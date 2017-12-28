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

require_once 'settings.php';

class CityDictionary {
	/*
	 * Returns the city from given postalcode.
	 */
	public static function getCity(int $code): ?string {
		$json = json_decode(file_get_contents(Settings::file_json_postalcodes));

		foreach ($json as $key => $data) {
			if ($data->code == $code) {
				return ucfirst(strtolower($data->city));
			}
		}

		return null;
	}

	/*
	 * Return true if the specified postal code exists.
	 */
	public static function isValidPostalCode(int $code): bool {
		$json = json_decode(file_get_contents(Settings::file_json_postalcodes));

		foreach ($json as $key => $data) {
			if ($data->code == $code) {
				return true;
			}
		}

		return false;
	}

	/*
	 * Returns the postalcode for given city.
	 */
	public static function getPostalCode(string $city): int {
		$json = json_decode(file_get_contents(Settings::file_json_postalcodes));

		foreach ($json as $key => $data) {
			if ($data->city == $city) {
				return $data->code;
			}
		}

		return 0;
	}
}