<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <http://infected.no/>.
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

//New settings system which loads settings from json files, and allows inheritance, making modifications in production easier. All "sane defaults" are located in json/settings.json, while the values that need attention can be stored in /srv/config/settings.json, or even further files if needed.

class Settings {
	private $config = [];
	private $files = [];
	public static function refreshSettings() {
		$config = [];
		$files = [];

		$nextFile = "json/settings.json";
		while(!empty($nextFile)) {
			if(strpos($nextFile, "/") !== 0) {
				//Does not start with /, is not absolute, so we need to fix it
				$nextFile = __DIR__ . "/" . $nextFile;
				//echo "Patched nextFile to " . $nextFile;
			}
			$string = trim(file_get_contents($nextFile), "\xEF\xBB\xBF");
			//Remove comments
			$string = preg_replace('!/\*.*?\*/!s', '', $string);
			$string = preg_replace('/\n\s*\n/', "\n", $string);

			//echo $string;
            //$currentFile = json_decode($string, true);
            //print_r($currentFile);
            $files[] = $nextFile;
            $nextFile = "";

            foreach ($currentFile as $key => $value) {
            	if($key == "chainload") { 
            		echo "Chainloading " . $value;
            		$nextFile = $value;
            	}
            	else {
            		$config[$key] = $value;
            	}
            }
		}
	}
	public static function getConfigFileList() : array{
		return $files;
	}
	public static function getValue(string $name) { //Any type
		return $config[$name];
	}
	public static function isDocker() : bool {
		return !empty(getenv('ENVIRONMENT'));
	}
}

Settings::refreshSettings();