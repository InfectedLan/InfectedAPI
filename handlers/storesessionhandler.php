<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/storesession.php';

class StoreSessionHandler
{
	public static function getStoreSession($id)
	{
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` WHERE `id`=' . $id . ';')
		
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return new StoreSession($row('id'), 
				$row('userId'), 
				$row('timeCreated'));
		}
	}
	public static function getStoreSessionForUser($userId)
	{
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_tickets_storesessions . '` WHERE `userId`=' . $userId . ';');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return self::getStoreSession($row['id']);
		}
	}
	public static function hasStoreSession($userId)
	{
		return getStoreSessionForUser($userId) != null;
	}
}
?>