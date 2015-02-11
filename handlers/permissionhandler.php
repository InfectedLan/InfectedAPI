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
                                
        $row = $result->fetch_array();
        
        $mysql->close();
        
        if ($row) {
            return self::getPermission($row['id']);
        }
    }
    
    public static function getPermissions() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_permissions . '`
								 ORDER BY `value` ASC;');
        
        $permissionList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($permissionList, self::getPermission($row['id']));
        }
        
        $mysql->close();

        return $permissionList;
    }
}
?>