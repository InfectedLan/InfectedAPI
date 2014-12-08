<?php
require_once 'settings.php';
require_once 'mysql.php';

class PaymentLogHandler {
	// Could not be bothered to write object for this yet, as we only log for security purposes.
	public static function logPayment($user, $ticketType, $price, $totalPrice, $transactionId) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_tickets_paymentlog . '` (`userId`, `ticketType`, `price`, `totalPrice`, `transactionId`, `datetime`) 
								VALUES (\'' . $con->real_escape_string($user->getId()) . '\', 
										\'' . $con->real_escape_string($ticketType->getId()) . '\', 
										\'' . $con->real_escape_string($price) . '\', 
										\'' . $con->real_escape_string($totalPrice) . '\', 
										\'' . $con->real_escape_string($transactionId) . '\', 
										\'' . date('Y-m-d H:i:s') . '\');');

		MySQL::close($con);
	}
}
?>