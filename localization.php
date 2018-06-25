<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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

require_once 'settings.php';

class Localization {
	const defaultLanguage = 'en_US';

	/*
	 * Get locale by key.
	 */
	public static function getLocale(string $key, ?string ...$arguments): string {
		$path = Settings::getValue("api_path") . 'json/languages/';
		$language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']) : self::defaultLanguage;

		// Fix for some browsers that is sending "nb" instead of "nb_NO".
		if ($language == 'nb' || $language == 'nb-NO') {
			$language = 'nb_NO';
		}

		$filename = $path . $language . '.json';

		// If the requested language don't exist, use the default language.
		if (!file_exists($filename)) {
			$filename = $path . self::defaultLanguage . '.json';
		}

		// Fetch the language json file as an array.
		$list = json_decode(file_get_contents($filename), true);

		// If key exists in array, return the value.
		if (is_array($list) && array_key_exists($key, $list)) {
			return vsprintf($list[$key], $arguments);
		}

		// Otherwise, return an error string.
		return 'Locale "' . $key . '" file for language "\'' . $language . '\'" not found, this is a bug.';
	}
}
?>
