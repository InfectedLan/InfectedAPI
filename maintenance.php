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

require_once 'settings.php';
class Maintenance {

    private static $maintenanceState = [];
    private const maintenanceFilename = "maintenance.json";

    private static function saveMaintenanceState() {
        $file = fopen(Settings::api_path . Maintenance::maintenanceFilename,"w");
        fwrite($file,json_encode(Maintenance::$maintenanceState));
        fclose($file); 
    }

    public static function loadMaintenanceState() {
        if(!file_exists(Settings::api_path . Maintenance::maintenanceFilename)) {
            self::$maintenanceState = [ "maintenance_state" => false, "maintenance_end" => time() ];
            self::saveMaintenanceState();
        } else {
            $string = trim(file_get_contents(Settings::api_path . Maintenance::maintenanceFilename), "\xEF\xBB\xBF");
            self::$maintenanceState = json_decode($string, true);
        }
    }

    public static function setMaintenance(int $duration) {
        self::$maintenanceState["maintenance_state"] = true;
        self::$maintenanceState["maintenance_end"] = time() + $duration;
        self::saveMaintenanceState();
    }

    public static function isMaintenance() {
        if(!self::$maintenanceState["maintenance_state"])
            return false;
        return time() < self::$maintenanceState["maintenance_end"];
    }
}

?>
