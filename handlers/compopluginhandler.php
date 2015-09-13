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
class CompoPluginHandler {
    /**
     * Returns object for the plugin, or the default if none existing
     */
    public static function getPluginOrDefault($pluginName) {
        if(!file_exists("plugins/compo/" . $pluginName . ".php")) {
            return self::loadPlugin($pluginName);
        } else {
            return self::loadPlugin($pluginName);
        }
    }

    /**
     * Returns javascripts for the plugin, or the default if none existing
     */
    public static function getPluginJavascriptOrDefault($pluginName) {
        if(!file_exists("plugins/compo/" . $pluginName . ".php")) {
            return self::getPluginScripts($pluginName);
        } else {
            return self::getPluginScripts($pluginName);
        }
    }

    /**
     * Returns true if plugin exists
     */
    public static function pluginExists($pluginName) {
        return file_exists("plugins/compo/" . $pluginName . ".php");
    }

    /**
     * Returns an object with the plugin
     */
    public static function loadPluginObject($pluginName) {
        $string = file_get_contents("plugins/compo/" . $pluginName . ".php");
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
    public static function getPluginScripts($pluginName) {
       $string = file_get_contents("plugins/compo/" . $pluginName . ".php");
       $json = json_decode($string, true);

       return $json["javascript"];
    }
}