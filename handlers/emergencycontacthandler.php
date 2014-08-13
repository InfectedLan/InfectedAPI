<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/emergencycontact.php';

class EmergencyContactHandler {
	// Returns the emergency contact with the given id.
	public static function getEmergencyContact($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM `'. Settings::db_table_infected_emergencycontacts . '`
									  WHERE `id` = \'' . $id . '\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new EmergencyContact($row['id'],
										$row['userId'],
										$row['phone']);
		}
	}
	
	public static function getEmergencyContactForUser($user) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `'. Settings::db_table_infected_emergencycontacts . '`
									  WHERE `userId` = \'' . $user->getId() . '\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return self::getEmergencyContact($row['id']);
		}
	}
	
	// Returns a list of all emergency contacts.
	public static function getEmergencyContacts() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_emergencycontacts . '`;');
		
		$emergencyContactsList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($emergencyContactsList, self::getEmergencyContact($row['id']));
		}
		
		MySQL::close($con);

		return $emergencyContactsList;
	}
	
	/* Create new emergency contact */
	public static function createEmergencyContact($user, $phone) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_emergencycontacts . '` (`userId`, `phone`) 
							VALUES (\'' . $user->getId() . '\', 
									\'' . $phone . '\');');
									
		MySQL::close($con);
	}
	
	/* Remove a emergency contact */
	public static function removeEmergencyContact($user) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_emergencycontacts . '` 
							WHERE `userId` = \'' . $user->getId . '\';');
		
		MySQL::close($con);
	}
}
?>