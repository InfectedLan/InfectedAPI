<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/payment.php';
require_once 'objects/user.php';
require_once 'objects/tickettype.php';

class PaymentHandler {
    public static function getPayment($id) {
        $database = Database::open(Settings::db_name_infected_tickets);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_payments . '` 
                                 WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();

		    return $result->fetch_object('Payment');
    }
    
    public static function createPayment(User $user, TicketType $ticketType, $price, $totalPrice, $transactionId) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $database->query('INSERT INTO `' . Settings::db_table_infected_tickets_payments . '` (`userId`, `ticketTypeId`, `price`, `totalPrice`, `transactionId`, `datetime`) 
                          VALUES (\'' . $user->getId() . '\', 
                                  \'' . $ticketType->getId() . '\', 
                                  \'' . $database->real_escape_string($price) . '\', 
                                  \'' . $database->real_escape_string($totalPrice) . '\', 
                                  \'' . $database->real_escape_string($transactionId) . '\', 
                                  \'' . date('Y-m-d H:i:s') . '\');');

        $database->close();
    }
}
?>