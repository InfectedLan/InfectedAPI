<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/payment.php';
require_once 'objects/user.php';
require_once 'objects/tickettype.php';

class PaymentHandler {
    public static function getPayment($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_paymentlog . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();

		    return $result->fetch_object('Payment');
    }
    
    public static function createPayment(User $user, TicketType $ticketType, $price, $totalPrice, $transactionId) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_tickets_paymentlog . '` (`userId`, `ticketType`, `price`, `totalPrice`, `transactionId`, `datetime`) 
                       VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                               \'' . $mysql->real_escape_string($ticketType->getId()) . '\', 
                               \'' . $mysql->real_escape_string($price) . '\', 
                               \'' . $mysql->real_escape_string($totalPrice) . '\', 
                               \'' . $mysql->real_escape_string($transactionId) . '\', 
                               \'' . date('Y-m-d H:i:s') . '\');');

        $mysql->close();
    }
}
?>