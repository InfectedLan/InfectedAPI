<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/permission.php';

class PermissionHandler {
    /*
     * Get the permission by the internal id.
     */
    public static function getPermission($id) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();
        
		return $result->fetch_object('Permission');
    }

    /*
     * Returns the permission with the given value.
     */
    public static function getPermissionByValue($value) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
                                    WHERE `value` = \'' . $database->real_escape_string($value) . '\';');
        
        $database->close();
        
        return $result->fetch_object('Permission');
    }
    
    /*
     * Returns a list of all permissions.
     */
    public static function getPermissions() {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
								    ORDER BY `value` ASC;');
        
        $database->close();

        $permissionList = array();
        
        while ($object = $result->fetch_object('Permission')) {
            array_push($permissionList, $object);
        }

        return $permissionList;
    }
}
?>