<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/avatar.php';
require_once 'objects/user.php';

class AvatarHandler {
    /* 
     * Get an avatar by the internal id.
     */
    public static function getAvatar($id) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();
		
		return $result->fetch_object('Avatar');
    }
    
    /*
     * Get an avatar for a specified user.
     */
    public static function getAvatarForUser(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\';');
        
        $database->close();

        return $result->fetch_object('Avatar');
    }
    
    /*
     * Returns a list of all avatars.
     */
    public static function getAvatars() {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '`;');
        
        $database->close();

        $avatarList = array();
        
        while ($object = $result->fetch_object('Avatar')) {
            array_push($avatarList, $object);
        }
        
        return $avatarList;
    }
    
    /*
     * Returns a list of all pending avatars.
     */
    public static function getPendingAvatars() {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                    WHERE `state` = \'1\';');
        
        $database->close();

        $avatarList = array();
        
        while ($object = $result->fetch_object('Avatar')) {
            array_push($avatarList, $object);
        }
        
        return $avatarList;
    }
    
    /*
     * Returns true if the specificed user have an avatar.
     */
    public static function hasAvatar(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\';');
        
        $database->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Returns true if the specificed user have a cropped avatar.
     */
    public static function hasCroppedAvatar(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\'
                                    AND (`state` = 1 OR `state` = 2);');
        
        $database->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Returns true if the specificed user have a valid vatar.
     */
    public static function hasValidAvatar(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\'
                                    AND `state` = \'2\';');
        
        $database->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Creates an new avatar.
     */
    public static function createAvatar($fileName, User $user) {
        $database = Database::open(Settings::db_name_infected_crew);

        $result = $database->query('INSERT INTO `' . Settings::db_table_infected_crew_avatars . '` (`userId`, `file`) 
                                    VALUES (\'' . $user->getId() . '\',
                                            \'' . $fileName . '\');');
        
        $database->close();

        return Settings::api_path . Settings::avatar_path . 'temp/' . $fileName;
    }
    
    /*
     * Deletes an avatar.
     */
    public static function deleteAvatar(Avatar $avatar) {
        $database = Database::open(Settings::db_name_infected_crew);

        $result = $database->query('DELETE FROM `' . Settings::db_table_infected_crew_avatars . '` 
                                    WHERE `id` = \'' . $avatar->getId() . '\';');
        
        $database->close();

        // Delete all avatars.
        $avatar->deleteFiles();
    }
    
    /*
     * Accept the specificed avatar.
     */
    public static function acceptAvatar(Avatar $avatar) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $database->query('UPDATE `' . Settings::db_table_infected_crew_avatars . '` 
                          SET `state` = \'2\'
                          WHERE `id` = \'' . $avatar->getId() . '\';');
        
        $database->close();
    }
    
    /*
     * Reject the specified avatar.
     */
    public static function rejectAvatar(Avatar $avatar) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $database->query('UPDATE `' . Settings::db_table_infected_crew_avatars . '` 
                          SET `state` =  \'3\'
                          WHERE `id` = \'' . $avatar->getId() . '\';');
        
        $database->close();
    }
    
    /*
     * Get the default avatar for the specified user.
     */
    public static function getDefaultAvatar(User $user) {
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