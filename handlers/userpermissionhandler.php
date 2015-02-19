<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/permissionhandler.php';
require_once 'objects/user.php';
require_once 'objects/permission.php';

class UserPermissionHandler {
    /*
     * Returns true if user has the given permission, otherwise false.
     */
    public static function hasUserPermission(User $user, Permission $permission) {
        $database = Database::open(Settings::db_name_infected);
		
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_userpermissions . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\'
                                    AND `permissionId` = \'' . $permission->getId() . '\';');
		
        $database->close();
        
        return $result->num_rows > 0;
    }
	
	/*
     * Returns true if user has the given permission value, otherwise false.
     */
	public static function hasUserPermissionByValue(User $user, $value) {
		$database = Database::open(Settings::db_name_infected);
		
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_userpermissions . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\'
                                    AND `permissionId` = (SELECT `id` FROM `' . Settings::db_table_infected_permissions . '` 
													      WHERE `value` = \'' . $database->real_escape_string($value) . '\');');
		
        $database->close();
        
        return $result->num_rows > 0;
	}
    
    /* 
     * Returns true if the specified user has any permissions.
     */
	public static function hasUserPermissions(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_userpermissions . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\';');
        
        $database->close();
        
        return $result->num_rows > 0;
    }
	
    /* 
     * Returns a list of permissions for the specified user.
     */
    public static function getUserPermissions(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
                                    WHERE `id` IN (SELECT `permissionId` FROM `' . Settings::db_table_infected_userpermissions . '`
                                                   WHERE `userId` = \'' . $user->getId() . '\');');
        
        $database->close();
        
        $permissionList = array();
        
        while ($object = $result->fetch_object('Permission')) {
            array_push($permissionList, $object);
        }
        
        return $permissionList;
    }
    
    /* 
     * Create a new user permission.
     */
    public static function createUserPermission(User $user, Permission $permission) {
        if (!self::hasUserPermission($user, $permission)) {
            $database = Database::open(Settings::db_name_infected);
        
            $database->query('INSERT INTO `' . Settings::db_table_infected_userpermissions . '` (`eventId`, `userId`, `permissionId`) 
                              VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\', 
                                      \'' . $user->getId() . '\', 
                                      \'' . $permission->getId() . '\')');
            
            $database->close();
        }
    }
    
    /* 
     * Remove a user permission.
     */
    public static function removeUserPermission(User $user, Permission $permission) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_userpermissions . '` 
                          WHERE `userId` = \'' . $user->getId() . '\'
                          AND `permissionId` = \'' . $permission->getId() . '\';');
        
        $database->close();
    }
    
    /* 
     * Removes all permissions for the specified user.
     */
    public static function removeUserPermissions(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_userpermissions . '` 
                          WHERE `userId` = \'' . $user->getId() . '\';');
        
        $database->close();
    }
}
?>