<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/payment.php';

class PaymentHandler {
    public static function getPayment($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_paymentlog . '` 
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                            
        $row = $result->fetch_array();
        
        $mysql->close();

        if ($row) {
            return new Payment($row['id'],
                               $row['userId'],
                               $row['ticketType'],
                               $row['price'],
                               $row['totalPrice'],
                               $row['transactionId'],
                               $row['datetime']);
        }
    }
    
    public static function createPayment($user, $ticketType, $price, $totalPrice, $transactionId) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_tickets_paymentlog . '` (`userId`, `ticketType`, `price`, `totalPrice`, `transactionId`, `datetime`) 
                                VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                                        \'' . $mysql->real_escape_string($ticketType->getId()) . '\', 
                                        \'' . $mysql->real_escape_string($price) . '\', 
                                        \'' . $mysql->real_escape_string($totalPrice) . '\', 
                                        \'' . $mysql->real_escape_string($transactionId) . '\', 
                                        \'' . date('Y-m-d H:i:s') . '\');');

        //Get the id we inserted
        $paymentId = $mysql->insert_id;

        $mysql->close();

        $payment = self::getPayment($paymentId);
        return $payment;
    }
}
?>