<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/permission.php';

class PermissionHandler {
    public static function getPermission($id) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
        
		return $result->fetch_object('Permission');
    }
    
    public static function getPermissionByValue($value) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_permissions . '`
                                 WHERE `value` = \'' . $mysql->real_escape_string($value) . '\';');
        
        $mysql->close();
        
        return $result->fetch_object('Permission');
    }
    
    public static function getPermissions() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
								 ORDER BY `value` ASC;');
        
        $mysql->close();

        $permissionList = array();
        
        while ($object = $result->fetch_object('Permission')) {
            array_push($permissionList, $object);
        }

        return $permissionList;
    }
}
?>