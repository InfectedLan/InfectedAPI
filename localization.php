<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY, without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Localization {
	static $list;

	/*
	 * Get locale by key.
	 */
	public static function getLocale($key) {
		// If key exists in array, return the value.
		if (array_key_exists($key, self::$list)) {
			return self::$list[$key];
		}

		// Otherwise, return an error string.
		return 'Locale not found, this is a bug so please submit a report at https://github.com/InfectedLan/InfectedAPI/issues';
	}

	/*
	 * Get locale by key, with the given replace argument.
	 */
	public static function getLocaleWithArgument($key, $argument) {
		return str_replace('%s', $argument, self::getLocale($key));
	}
}

// Initialize the list.
Localization::$list = json_decode(file_get_contents('http://crew.test.infected.no/api/resources/lang/nb_NO.json'), true);
?>