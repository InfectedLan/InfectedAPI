<?php
require_once 'settings.php';
require_once 'mysql.php';

class UserPermissionsHandler {
    /*
     * Returns true if user has the given permission, otherwise false.
     */
    public static function hasUserPermission($user, $value) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `value` FROM `' . Settings::db_table_infected_userpermissions . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\' 
                                 AND `value` = \'' . $mysql->real_escape_string($value) . '\';');
        
        $mysql->close();
        
        $row = $result->fetch_array();
        
        return $row ? true : false;
    }
    
	public static function hasUserPermissions($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_userpermissions . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();
        
        $row = $result->fetch_array();
        
        return $row ? true : false;
    }
	
    public static function getUserPermissions($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `value` FROM `' . Settings::db_table_infected_userpermissions . '`
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();
        
        $permissionList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($permissionList, $row['value']);
        }

        return $permissionList;
    }
    
    public static function createUserPermission($user, $value) {
        if (!self::hasUserPermission($user, $value)) {
            $mysql = MySQL::open(Settings::db_name_infected);
        
            $mysql->query('INSERT INTO `' . Settings::db_table_infected_userpermissions . '` (`userId`, `value`) 
                           VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                                   \'' . $mysql->real_escape_string($value) . '\')');
            
            $mysql->close();
        }
    }
    
    public static function removeUserPermission($user, $value) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_userpermissions . '` 
                       WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                       AND `value` = \'' . $mysql->real_escape_string($value) . '\';');
        
        $mysql->close();
    }
    
    public static function removeUserPermissions($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_userpermissions . '` 
                       WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();
    }
}
?>