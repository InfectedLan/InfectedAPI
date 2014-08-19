<?php
require_once 'settings.php';
require_once 'mysql.php';

class PaymentLogHandler
{
	//Couldnt be bothered to write object for this yet, as we only log for security purposes
	public static function logPayment($user, $item_name, $item_number, $payment_status, $payment_amount, $payment_currency, $txn_id, $receiver_email, $payer_email, $quantity)
	{
		$time = time();

		$con = MySQL::open(Settings::db_name_infected_tickets);

		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_tickets_paymentlog . '` (`timeLogged`, `itemName`, `itemNumber`, `paymentStatus`, `paymentAmount`, `paymentCurrency`, `txnId`, `recieverEmail`, `payerEmail`, `quantity`, `userId`) 
							VALUES (' . $con->real_escape_string($time) . ', 
									\'' . $con->real_escape_string($item_name) . '\', 
									\'' . $con->real_escape_string($item_number) . '\',
									\'' . $con->real_escape_string($payment_status) . '\', 
									\'' . $con->real_escape_string($payment_amount) . '\', 
									\'' . $con->real_escape_string($payment_currency) . '\', 
									\'' . $con->real_escape_string($txn_id) . '\', 
									\'' . $con->real_escape_string($receiver_email) . '\',
									\'' . $con->real_escape_string($payer_email) . '\', 
									\'' . $con->real_escape_string($quantity) . '\', 
									' . $con->real_escape_string($user->getId()) . ');');

		MySQL::close($con);
	}
}
?>