<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/payment.php';

class PaymentLogHandler {
	public static function getPayment($id) {
		$con = MySQL::open(Settings::db_name_infected_tickets);
		
		$result = $con->query('SELECT `userId` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
									  WHERE `code` = \'' . $con->real_escape_string($code) . '\';');
							
		$row = mysqli_fetch_array($result);
		
		$con->close();

		if ($row) {
			return new Payment($row['id']
							   $row['userId'],
							   $row['ticketType'],
							   $row['price'],
							   $row['totalPrice'],
							   $row['transactionId'],
							   $row['datetime']);
		}
	}
	
	public static function createPayment($user, $ticketType, $price, $totalPrice, $transactionId) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$con->query('INSERT INTO `' . Settings::db_table_infected_tickets_payments . '` (`userId`, `ticketType`, `price`, `totalPrice`, `transactionId`, `datetime`) 
								VALUES (\'' . $con->real_escape_string($user->getId()) . '\', 
										\'' . $con->real_escape_string($ticketType->getId()) . '\', 
										\'' . $con->real_escape_string($price) . '\', 
										\'' . $con->real_escape_string($totalPrice) . '\', 
										\'' . $con->real_escape_string($transactionId) . '\', 
										\'' . date('Y-m-d H:i:s') . '\');');

		$con->close();
	}
}
?>