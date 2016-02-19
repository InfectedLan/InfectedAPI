<?php
/*
 * This file is part of InfectedCrew.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'objects/compoplugin.php'; //Not really required, but is used in a hack to make the default plugin use the baseclass for compo plugins

class CompoPluginHandler {

    private static $cachedPlugins = array();
    private static $cachedJavascripts = array();
    /**
     * Returns object for the plugin, or the default if none existing. Will cache.
     */
    public static function getPluginObjectOrDefault($pluginName) {
	if(!isset(self::$cachedPlugins[$pluginName])) { //please note isset will return false if the key exists, but the value is null. This is not a case we need to care for in this example.
	    $obj = null;
	    if(!file_exists(Settings::api_path . "plugins/compo/" . $pluginName . ".json")) {
		$obj = self::loadPluginObject("default");
	    } else {
		$obj = self::loadPluginObject($pluginName);
	    }
	    self::$cachedPlugins[$pluginName] = $obj;
	    return $obj;
	} else {
	    return self::$cachedPlugins[$pluginName];
	}
    }

    /**
     * Returns javascripts for the plugin, or the default if none existing. Will cache.
     */
    public static function getPluginJavascriptOrDefault($pluginName) {
	if(!isset(self::$cachedJavascripts[$pluginName])) { //please note isset will return false if the key exists, but the value is null. This is not a case we need to care for in this example.
	    $arr = null;
	    if(!file_exists(Settings::api_path . "plugins/compo/" . $pluginName . ".json")) {
		$arr = self::loadPluginScripts("default");
	    } else {
		$arr = self::loadPluginScripts($pluginName);
	    }
	    self::$cachedJavascripts[$pluginName] = $arr;
	    return $arr;
	} else {
	    return self::$cachedJavascripts[$pluginName];
	}
    }

    /**
     * Returns true if plugin exists
     */
    public static function pluginExists($pluginName) {
        return file_exists(Settings::api_path . "plugins/compo/" . $pluginName . ".json");
    }

    /**
     * Returns an object with the plugin
     */
    public static function loadPluginObject($pluginName) {
        $string = file_get_contents(Settings::api_path . "plugins/compo/" . $pluginName . ".json");
        $json = json_decode($string, true);

        foreach($json["plugin"] as $pluginFile) {
            require_once 'plugins/compo/' . $pluginFile;
        }

        //This is a cool feature about php
	return new $json["classname"]; //yes, this works!
    }

    /**
     * Returns a list of scripts used by this plugin
     */
    public static function loadPluginScripts($pluginName) {
        $string = file_get_contents(Settings::api_path . "plugins/compo/" . $pluginName . ".json");
        $json = json_decode($string, true);

        return $json["javascript"];
    }
}

?>