<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/avatar.php';

class AvatarHandler {
    /* 
     * Get an avatar by id
     */
    public static function getAvatar($id) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('Avatar');
    }
    
    /*
     * Get an avatar for a specified user.
     */
    public static function getAvatarForUser($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();

        $row = $result->fetch_array();
        
        if ($row) {
            return self::getAvatar($row['id']);
        }
    }
    
    /*
     * Returns a list of all avatars.
     */
    public static function getAvatars() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '`;');
        
        $mysql->close();

        $avatarList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($avatarList, self::getAvatar($row['id']));
        }
        
        return $avatarList;
    }
    
    /*
     * Returns a list of all pending avatars.
     */
    public static function getPendingAvatars() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                 WHERE `state` = \'1\';');
        
        $mysql->close();

        $pendingAvatarList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($pendingAvatarList, self::getAvatar($row['id']));
        }
        
        return $pendingAvatarList;
    }
    
    /*
     * Returns true if the specificed user have an avatar.
     */
    public static function hasAvatar($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();

        $row = $result->fetch_array();
        
        return $row ? true : false;
    }
    
    /*
     * Returns true if the specificed user have a cropped avatar.
     */
    public static function hasCroppedAvatar($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                 AND (`state` = 1 OR `state` = 2);');
        
        $mysql->close();

        $row = $result->fetch_array();
        
        return $row ? true : false;
    }
    
    /*
     * Returns true if the specificed user have a valid vatar.
     */
    public static function hasValidAvatar($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                      AND `state` = 2;');
        
        $mysql->close();

        $row = $result->fetch_array();
        
        return $row ? true : false;
    }
    
    /*
     * Creates an new avatar.
     */
    public static function createAvatar($fileName, $user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);

        $result = $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_avatars . '` (`userId`, `file`) 
                                 VALUES (\'' . $user->getId() . '\',
                                         \'' . $fileName . '\');');
        
        $mysql->close();

        return Settings::api_path . Settings::avatar_path . 'temp/' . $fileName;
    }
    
    /*
     * Deletes an avatar.
     */
    public static function deleteAvatar($avatar) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);

        $result = $mysql->query('DELETE FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                      WHERE `id` = \'' . $mysql->real_escape_string($avatar->getId()) . '\';');
        
        $mysql->close();

        // Delete all avatars.
        $avatar->deleteFiles();
    }
    
    /*
     * Accept the specificed avatar.
     */
    public static function acceptAvatar($avatar) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_avatars . '` 
                       SET `state` = \'2\'
                       WHERE `id` = \'' . $mysql->real_escape_string($avatar->getId()) . '\';');
        
        $mysql->close();
    }
    
    /*
     * Reject the specified avatar.
     */
    public static function rejectAvatar($avatar) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_avatars . '` 
                       SET `state` =  \'3\'
                       WHERE `id` = \'' . $mysql->real_escape_string($avatar->getId()) . '\';');
        
        $mysql->close();
    }
    
    /*
     * Get the default avatar for the specified user.
     */
    public static function getDefaultAvatar($user) {
        $file = null;
        
        if ($user->getAge() >= 18) {
            if ($user->getGender() == 0) {
                $file = 'default_gutt.png';
            } else {
                $file = 'default_jente.png';
            }
        } else {
            $file = 'default_child.png';
        }
        
        return Settings::avatar_path . 'default/' . $file;
    }
}
?>