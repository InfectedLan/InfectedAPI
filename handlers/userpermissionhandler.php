<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/permissionhandler.php';

class UserPermissionHandler {
    /*
     * Returns true if user has the given permission, otherwise false.
     */
    public static function hasUserPermission($user, $permission) {
        $mysql = MySQL::open(Settings::db_name_infected);
		
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_userpermissions . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                 AND `permissionId` = \'' . $mysql->real_escape_string($permission->getId()) . '\';');
		
        $mysql->close();
        
        return $result->num_rows > 0;
    }
	
	/*
     * Returns true if user has the given permission value, otherwise false.
     */
	public static function hasUserPermissionByValue($user, $value) {
		$mysql = MySQL::open(Settings::db_name_infected);
		
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_userpermissions . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                 AND `permissionId` = (SELECT `id` FROM `' . Settings::db_table_infected_permissions . '` 
													   WHERE `value` = \'' . $mysql->real_escape_string($value) . '\');');
		
        $mysql->close();
        
        return $result->num_rows > 0;
	}
    
	public static function hasUserPermissions($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_userpermissions . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();
        
        return $result->num_rows > 0;
    }
	
    public static function getUserPermissions($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
                                 WHERE `id` = (SELECT `permissionId` FROM `' . Settings::db_table_infected_userpermissions . '`
                                               WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\');');
        
        $mysql->close();
        
        $permissionList = array();
        
        while ($object = $result->fetch_object('Permission')) {
            array_push($permissionList, $object);
        }
        
        return $permissionList;
    }
    
    public static function createUserPermission($user, $permission) {
        if (!self::hasUserPermission($user, $permission)) {
            $mysql = MySQL::open(Settings::db_name_infected);
        
            $mysql->query('INSERT INTO `' . Settings::db_table_infected_userpermissions . '` (`userId`, `permissionId`) 
                           VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                                   \'' . $mysql->real_escape_string($permission->getId()) . '\')');
            
            $mysql->close();
        }
    }
    
    public static function removeUserPermission($user, $permission) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_userpermissions . '` 
                       WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                       AND `permissionId` = \'' . $mysql->real_escape_string($permission->getId()) . '\';');
        
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