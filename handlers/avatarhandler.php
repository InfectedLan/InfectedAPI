<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/avatar.php';

class AvatarHandler {
    /* Get a avatar by id */
    public static function getAvatar($id) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                      
        $row = mysqli_fetch_array($result);
        
        $mysql->close();

        if ($row) {
            return new Avatar($row['id'], 
                              $row['userId'], 
                              $row['file'], 
                              $row['state']);
        }
    }
    
    public static function getAvatarForUser($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        if ($row) {
            return self::getAvatar($row['id']);
        }
    }
    
    public static function getAvatars() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '`;');
        
        $avatarList = array();
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($avatarList, self::getAvatar($row['id']));
        }
        
        $mysql->close();
        
        return $avatarList;
    }
    
    public static function getPendingAvatars() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                      WHERE `state` = \'1\';');
        
        $pendingAvatarList = array();
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($pendingAvatarList, self::getAvatar($row['id']));
        }
        
        $mysql->close();
        
        return $pendingAvatarList;
    }
    
    public static function hasAvatar($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        return $row ? true : false;
    }
    
    public static function hasCroppedAvatar($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                      AND (`state` = 1 OR `state` = 2);');
        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        return $row ? true : false;
    }
    
    public static function hasValidAvatar($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                      AND `state` = 2;');
        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        return $row ? true : false;
    }
    
    public static function createAvatar($fileName, $user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);

        $result = $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_avatars . '` (`userId`, `file`, `state`) 
                                      VALUES (\'' . $user->getId() . '\',
                                              \'' . $fileName . '\',
                                              \'0\');');
    
        return Settings::api_path . Settings::avatar_path . 'temp/' . $fileName;
    }
    
    public static function deleteAvatar($avatar) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);

        $result = $mysql->query('DELETE FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                      WHERE `id` = \'' . $mysql->real_escape_string($avatar->getId()) . '\';');
        
        // Delete all avatars.
        $avatar->deleteFiles();

        $mysql->close();
    }
    
    public static function acceptAvatar($id) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_avatars . '` 
                            SET `state` = \'2\'
                            WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
    
    public static function rejectAvatar($id) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_avatars . '` 
                            SET `state` =  \'3\'
                            WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
    
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