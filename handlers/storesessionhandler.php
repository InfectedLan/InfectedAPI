<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/storesession.php';

class StoreSessionHandler
{
	public static function getStoreSession($id) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` WHERE `id`=' . $id . ';');
		
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return new StoreSession($row['id'], 
				$row['userId'], 
				$row['timeCreated'],
				$row['ticketType'],
				$row['amount']);
		}
	}
	
	public static function registerStoreSession($user, $type, $amount) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_tickets_storesessions . '` (`userId`, `timeCreated`, `ticketType`, `amount`) VALUES (' . $user->getId() . ', ' . time() . ', ' . $type->getId() . ', ' . $amount . ')');

		MySQL::close($con);
	}
	
	public static function getStoreSessionForUser($user) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_tickets_storesessions . '` WHERE `userId`=' . $user->getId() . ' AND `timeCreated` > ' . self::oldestValidTimestamp() . ';');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return self::getStoreSession($row['id']);
		}
	}
	
	public static function hasStoreSession($user) {
		return self::getStoreSessionForUser($user) != null;
	}

	public static function getReservedTicketCount($ticketType) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `amount` FROM `' . Settings::db_table_infected_tickets_storesessions . '` WHERE `ticketType` = ' . $ticketType->getId() . ' AND `timeCreated` > ' . self::oldestValidTimestamp() . ';');

		$reservedCount = 0;

		while ($row = mysqli_fetch_array($result)) {
			$reservedCount += $row['amount'];
		}

		MySQL::close($con);

		return $reservedCount;
	}

	private static function oldestValidTimestamp()
	{
		return time() - Settings::storeSessionTime;
	}
}
?>