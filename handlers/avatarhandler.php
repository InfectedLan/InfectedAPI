<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/avatar.php';

class AvatarHandler {
	/* Get a avatar by id */
	public static function getAvatar($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '` 
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new Avatar($row['id'], 
							  $row['userId'], 
							  $row['file'], 
							  $row['state']);
		}
	}
	
	public static function getAvatarForUser($userId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
									  WHERE `userId` = \'' . $con->real_escape_string($userId) . '\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return self::getAvatar($row['id']);
		}
	}
	
	public static function getAvatars() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '`;');
		
		$avatarList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($avatarList, self::getAvatar($row['id']));
		}
		
		MySQL::close($con);
		
		return $avatarList;
	}
	
	public static function getPendingAvatars() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
									  WHERE `state` = \'1\';');
		
		$pendingAvatarList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pendingAvatarList, self::getAvatar($row['id']));
		}
		
		MySQL::close($con);
		
		return $pendingAvatarList;
	}
	
	/*
	public function getPendingAvatar($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\' 
									  AND `state` = \'1\';');
		
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return self::getAvatar($row['id']);
		}
	}
	
	public static function getPendingAvatarForUser($userId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_avatars . '` 
									  WHERE `userId` = \'' . $con->real_escape_string($userId) . '\'
									  AND `state` = \'1\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return self::getAvatar($row['id']);
		}
	}
	*/
	
	public static function deleteAvatar($avatar) {
		$con = MySQL::open(Settings::db_name_infected_crew);

		$result = mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_crew_avatars . '` WHERE `id` = ' . $con->real_escape_string($avatar->getId()) . ';');
		
		$avatar->deleteFiles();

		MySQL::close($con);
	}

	public static function createAvatar($fileName, $user) {
		$con = MySQL::open(Settings::db_name_infected_crew);

		$result = mysqli_query($con, 'INSERT INTO `` (`userId`, `file`, `state`) VALUES (' . $user->getId() . ', \'' . $fileName . '\', 0);');
	
		return Settings::api_path . Settings::avatar_path . 'temp/' . $fileName;
	}
}
?>