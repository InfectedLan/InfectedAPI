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
                                
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        return $row ? true : false;
    }
    
    public static function getUserPermissions($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `value` FROM `' . Settings::db_table_infected_userpermissions . '`
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $permissionList = array();
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($permissionList, $row['value']);
        }
        
        $mysql->close();

        return $permissionList;
    }
    
    public static function hasUserPermissions($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_userpermissions . '` 
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
                                
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        return $row ? true : false;
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