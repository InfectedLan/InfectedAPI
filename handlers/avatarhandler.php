<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/avatar.php';

class AvatarHandler {
	/* Get a avatar by id */
	public static function getAvatar($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_infected_crew_avatars . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new Avatar($row['id'], 
							$row['userId'], 
							$row['relativeUrl'], 
							$row['state']);
		}
	}
	
	public static function getAvatars() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_infected_crew_avatars);
		
		$avatarList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($groupList, self::getAvatar($row['id']));
		}
		
		MySQL::close($con);
		
		return $avatarList;
	}
	
	public static function getPendingAvatars() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_infected_crew_avatars . ' WHERE state=\'1\'');
		
		$avatarList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($groupList, self::getAvatar($row['id']));
		}
		
		MySQL::close($con);
		
		return $avatarList;
	}
	
	public static function getAvatarForUser($userId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_infected_crew_avatars . ' WHERE userId=\'' . $userId . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return self::getAvatar($row['id']);
		}
	}
}
?>