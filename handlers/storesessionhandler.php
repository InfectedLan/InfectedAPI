<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/storesession.php';
require_once 'handlers/tickethandler.php';

class StoreSessionHandler
{
	public static function getStoreSession($id) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` WHERE `id`=' . $con->real_escape_string($id) . ';');
		
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return new StoreSession($row['id'], 
									$row['userId'], 
									$row['timeCreated'],
									$row['ticketType'],
									$row['amount'],
									$row['code'],
									$row['price']);
		}
	}
	
	public static function registerStoreSession($user, $type, $amount, $price) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$code = md5(time() . $user->getId() . "123"); // TODO: Replace with fancy openssl function.

		$result = mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_tickets_storesessions . '` (`userId`, `timeCreated`, `ticketType`, `amount`, `code`, `price`) 
									  VALUES (' . $con->real_escape_string($user->getId()) . ', 
											  ' . $con->real_escape_string(time()) . ', 
											  ' . $con->real_escape_string($type->getId()) . ', 
											  ' . $con->real_escape_string($amount) . ', 
											  \'' . $con->real_escape_string($code) . '\',
											  ' . $con->real_escape_string($price) .  ')');

		MySQL::close($con);

		return $code;
	}
	
	public static function getStoreSessionForUser($user) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
									  WHERE `userId` = ' . $con->real_escape_string($user->getId()) . ' 
									  AND `timeCreated` > ' . self::oldestValidTimestamp() . ';');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return self::getStoreSession($row['id']);
		}
	}

	//Used to validate a payment
	public static function isPaymentValid($totalPrice, $amt, $session)
	{
		return $totalPrice == $session->getPrice() && $amt == $session->getAmount();
	}
	
	public static function hasStoreSession($user) {
		return self::getStoreSessionForUser($user) != null;
	}

	public static function getReservedTicketCount($ticketType) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `amount` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
									  WHERE `ticketType` = ' . $con->real_escape_string($ticketType->getId()) . ' 
									  AND `timeCreated` > ' . self::oldestValidTimestamp() . ';');

		$reservedCount = 0;

		while ($row = mysqli_fetch_array($result)) {
			$reservedCount += $row['amount'];
		}

		MySQL::close($con);

		return $reservedCount;
	}

	private static function oldestValidTimestamp() {
		return time() - Settings::storeSessionTime;
	}

	public static function deleteStoreSession($storeSession) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
									  WHERE `id` = ' . $con->real_escape_string($storeSession->getId()) . ';');

		MySQL::close($con);
	}

	private static function getStoreSessionFromKey($key) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
									  WHERE `code` = ' . $con->real_escape_string($code) . ' 
									  AND `timeCreated` > ' . self::oldestValidTimestamp() . ';');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if ($row) {
			return self::getStoreSession($row['id']);
		}
	}

	public static function getUserIdFromKey($key) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `userId` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
									  WHERE `code`=' . $con->real_escape_string($code) . ' 
									  AND `timeCreated` > ' . self::oldestValidTimestamp() . ';');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return $row['userId'];
		}
	}

	public static function purchaseComplete($storeSession) {
		if (!isset($storeSession)) {
			return false;
		}

		$user = UserHandler::getUser( $storeSession->getUserId() );

		$ticketType = $storeSession->getTicketType();

		// Checks are ok, lets buy!
		for ($i = 0; $i < $storeSession->getAmount(); $i++) {
			TicketHandler::createTicket($user, $ticketType);
		}

		self::deleteStoreSession($storeSession);

		return true;
	}
}
?>