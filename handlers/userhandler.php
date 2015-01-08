<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/emergencycontacthandler.php';
require_once 'handlers/passwordresetcodehandler.php';
require_once 'handlers/registrationcodehandler.php';
require_once 'handlers/userpermissionshandler.php';
require_once 'handlers/applicationhandler.php';
require_once 'handlers/avatarhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'objects/user.php';

class UserHandler {
    /* 
     * Get an user by the internal id.
     */
    public static function getUser($id) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_users . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
         
        $mysql->close();
         
        $row = $result->fetch_array();

        if ($row) {
            return new User($row['id'], 
                            $row['firstname'], 
                            $row['lastname'], 
                            $row['username'], 
                            $row['password'], 
                            $row['email'], 
                            $row['birthdate'], 
                            $row['gender'], 
                            $row['phone'], 
                            $row['address'], 
                            $row['postalcode'], 
                            $row['nickname']);
        }
    }
    
    /* 
     * Get user by it's username.
     */
    public static function getUserByName($username) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_users . '` 
                                 WHERE `username` = \'' . $mysql->real_escape_string($username) . '\' 
                                 OR `email` = \'' . $mysql->real_escape_string($username) . '\';');
        
        $mysql->close();
        
        $row = $result->fetch_array();

        if ($row) {
            return self::getUser($row['id']);
        }
    }
    
    /* 
     * Get a list of all users.
     */
    public static function getUsers() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_users . '`
                                 ORDER BY `firstname` ASC;');
        
        $mysql->close();
        
        $userList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($userList, self::getUser($row['id']));
        }

        return $userList;
    }

    /*
     * Returns all users that have one or more permission values in the permissions table.
     */
    public static function getPermissionUsers() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT DISTINCT `userId` FROM `' . Settings::db_table_infected_userpermissions . '`;');
        
        $mysql->close();
        
        $userList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($userList, self::getUser($row['userId']));
        }

        return $userList;
    }
    
    /* 
     * Get a list of all users which is member in a group
     */
    public static function getMemberUsers() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `' . Settings::db_table_infected_users . '`.`id` FROM `' . Settings::db_table_infected_users . '`
                                 LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '` ON `' . Settings::db_table_infected_users . '`.`id` = `userId`
                                 WHERE `groupId` IS NOT NULL 
                                 ORDER BY `firstname` ASC;');

        $mysql->close();
                                      
        $userList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($userList, self::getUser($row['id']));
        }

        return $userList;
    }
    
    /* 
     * Get a list of all users which is not member in a group
     */
    public static function getNonMemberUsers() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `' . Settings::db_table_infected_users . '`.`id` FROM ' . Settings::db_table_infected_users . ' 
                                      LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '` ON `' . Settings::db_table_infected_users . '`.`id` = `userId`
                                      WHERE `groupId` IS NULL 
                                      ORDER BY `firstname` ASC;');
        
        $userList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($userList, self::getUser($row['id']));
        }
        
        $mysql->close();
        
        return $userList;
    }
    
    /*
     * Create a new user
     */
    public static function createUser($firstname, $lastname, $username, $password, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_users . '` (`firstname`, `lastname`, `username`, `password`, `email`, `birthdate`, `gender`, `phone`, `address`, `postalcode`, `nickname`) 
                            VALUES (\'' . $mysql->real_escape_string($firstname) . '\', 
                                    \'' . $mysql->real_escape_string($lastname) . '\', 
                                    \'' . $mysql->real_escape_string($username) . '\', 
                                    \'' . $mysql->real_escape_string($password) . '\', 
                                    \'' . $mysql->real_escape_string($email) . '\', 
                                    \'' . $mysql->real_escape_string($birthDate) . '\', 
                                    \'' . $mysql->real_escape_string($gender) . '\', 
                                    \'' . $mysql->real_escape_string($phone) . '\', 
                                    \'' . $mysql->real_escape_string($address) . '\', 
                                    \'' . $mysql->real_escape_string($postalCode) . '\', 
                                    \'' . $mysql->real_escape_string($nickname) . '\');');
                                    
        $mysql->close();
    }
    
    /* 
     * Update a user
     */
    public static function updateUser($id, $firstname, $lastname, $username, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_users . '` 
                            SET `firstname` = \'' . $mysql->real_escape_string($firstname) . '\', 
                                `lastname` = \'' . $mysql->real_escape_string($lastname) . '\', 
                                `username` = \'' . $mysql->real_escape_string($username) . '\', 
                                `email` = \'' . $mysql->real_escape_string($email) . '\', 
                                `birthdate` = \'' . $mysql->real_escape_string($birthDate) . '\', 
                                `gender` = \'' . $mysql->real_escape_string($gender) . '\', 
                                `phone` = \'' . $mysql->real_escape_string($phone) . '\', 
                                `address` = \'' . $mysql->real_escape_string($address) . '\', 
                                `postalcode` = \'' . $mysql->real_escape_string($postalCode) . '\', 
                                `nickname` = \'' . $mysql->real_escape_string($nickname) . '\' 
                            WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
    
    /* 
     * Remove a user
     */
    public static function removeUser($user) {
        // Only remove users without a ticket, for now...
        if (!TicketHandler::hasUserAnyTicket($user)) {
            $mysql = MySQL::open(Settings::db_name_infected);
            
            $mysql->query('DELETE FROM `' . Settings::db_table_infected_users . '` 
                           WHERE `id` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
            
            $mysql->close();
            
            // Remove users emergencycontact.
            if (EmergencyContactHandler::hasEmergencyContact($user)) {
                EmergencyContactHandler::removeEmergenctContact($user);
            }
            
            // Remove users passwordresetcode.
            if (PasswordResetCodeHandler::hasPasswordResetCode($user)) {
                PasswordResetCodeHandler::removeUserPasswordResetCode($user);
            }
            
            // Remove users registrationscode.
            if (RegistrationCodeHandler::hasUserRegistrationCode($user)) {
                RegistrationCodeHandler::removeUserRegistrationCode($user);
            }
            
            // Remove users permissions.
            if (UserPermissionsHandler::hasUserPermissions($user)) {
                UserPermissionsHandler::removeUserPermissions($user);
            }
            
            // Remove users application.
            if (ApplicationHandler::hasApplication($user)) {
                ApplicationHandler::removeUserApplication($user);
            }
            
            // Remove users avatar.
            if (AvatarHandler::hasAvatar($user)) {
                AvatarHandler::deleteAvatar($user->getAvatar());
            }
            
            // Remove users memberof entry.
            if (GroupHandler::isGroupMember($user->getId())) {
                GroupHandler::removeUserFromGroup($user);
            }
        }
    }
    
    /* 
     * Update a users password
     */
    public static function updateUserPassword($userId, $password) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_users . '` 
                       SET `password` = \'' . $mysql->real_escape_string($password) . '\'
                       WHERE `id` = \'' . $mysql->real_escape_string($userId) . '\';');
        
        $mysql->close();
    }
    
    /* 
     * Check if a user with given username or email already exists.
     */
    public static function userExists($username) {
        $mysql = MySQL::open(Settings::db_name_infected);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_users . '` 
                                 WHERE `username` = \'' . $mysql->real_escape_string($username) . '\' 
                                  OR `email` = \'' . $mysql->real_escape_string($username) . '\';');
        
        $mysql->close();
                
        $row = $result->fetch_array();
        
        return $row ? true : false;
    }
    
    /*
     * Lookup users by set values and return a list of users as result.
     */
    public static function search($query) {
        $mysql = MySQL::open(Settings::db_name_infected);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_users . '` 
                                 WHERE `firstname` LIKE \'%' . $mysql->real_escape_string($query) . '%\'
                                 OR `lastname` LIKE \'%' . $mysql->real_escape_string($query) . '%\' 
                                 OR `username` LIKE \'%' . $mysql->real_escape_string($query) . '%\' 
                                 OR `email` LIKE \'%' . $mysql->real_escape_string($query) . '%\' 
                                 OR `phone` LIKE \'%' . $mysql->real_escape_string($query) . '%\' 
                                 OR `nickname` LIKE \'%' . $mysql->real_escape_string($query) . '%\' 
                                 LIMIT 10;');
        
        $mysql->close();
        
        $userList = array();

        while($row = $result->fetch_array()) {
            array_push($userList, self::getUser($row['id']));
        }
        
        return $userList;
    }
}
?>