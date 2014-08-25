<?php
require_once 'settings.php';
require_once 'mysql.php';

class PaymentLogHandler
{
	//Couldnt be bothered to write object for this yet, as we only log for security purposes
	public static function logPayment($user, $ticketType, $amount, $total, $transactionId)
	{
		$time = time();

		$con = MySQL::open(Settings::db_name_infected_tickets);

		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_tickets_paymentlog . '` (`timeLogged`, `userId`, `ticketType`, `amount`, `totalPrice`, `transactionId`) 
							VALUES (' . $con->real_escape_string($time) . ', 
									\'' . $con->real_escape_string($user->getId()) . '\', 
									\'' . $con->real_escape_string($ticketType->getId()) . '\',
									\'' . $con->real_escape_string($amount) . '\', 
									\'' . $con->real_escape_string($total) . '\', 
									\'' . $con->real_escape_string($transactionId) . ');');

		MySQL::close($con);
	}
}
?>