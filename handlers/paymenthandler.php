<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'settings.php';
require_once 'database.php';
require_once 'objects/payment.php';
require_once 'objects/user.php';
require_once 'objects/tickettype.php';

class PaymentHandler {
    /*
     * Returns the payment by the internal id.
     */
    public static function getPayment($id) {
        $database = Database::open(Settings::db_name_infected_tickets);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_payments . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();

		    return $result->fetch_object('Payment');
    }
    
    /*
     * Returns a list of all payments.
     */
    public static function getPayments() {
        $database = Database::open(Settings::db_name_infected_tickets);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_payments . '`;');
        
        $database->close();

        $paymentList = array();
        
        while ($object = $result->fetch_object('Payment')) {
            array_push($paymentList, $object);
        }

        return $paymentList;
    }

    /*
     * Create a new payment.
     */
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