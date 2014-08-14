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
			VALUES (' . $time . ', \'' . $item_name . '\', \'' . $item_number . '\', \'' . $payment_status . '\', \'' . $payment_amount . '\', \'' . $payment_currency . '\', \'' . $txn_id . '\', \'' . $receiver_email . '\', \'' . $payer_email . '\', \'' . $quantity . '\', ' . $user->getId() . ');');

		MySQL::close($con);
	}
}
?>