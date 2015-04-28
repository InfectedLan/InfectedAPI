<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/tickethandler.php';
require_once 'objects/storesession.php';
require_once 'objects/user.php';
require_once 'objects/tickettype.php';
require_once 'objects/payment.php';

class StoreSessionHandler {
    /*
     * Get a store session by the internal id.
     */
    public static function getStoreSession($id) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `id` = ' . $database->real_escape_string($id) . ';');
        
        $database->close();
		
		return $result->fetch_object('StoreSession');
    }
    
    /*
     * Get a list of all store sessions.
     */
    public static function getStoreSessions() {
        $database = Database::open(Settings::db_name_infected_tickets);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '`;');
        
        $database->close();
        
        $storeSessionList = array();
        
        while ($object = $result->fetch_object('StoreSession')) {
            array_push($storeSessionList, $object);
        }
        
        return $storeSessionList;
    }

    /*
     * Returns the store session for the specified user.
     */
    public static function getStoreSessionByUser(User $user) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\' 
                                    AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

        $database->close();

        return $result->fetch_object('StoreSession');
    }

    /*
     * Returns the store session by the specified key.
     */
    private static function getStoreSessionByCode($code) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `code` = ' . $database->real_escape_string($code) . ' 
                                    AND `datetime` > ' . self::oldestValidTimestamp() . ';');

        $database->close();

        return $result->fetch_object('StoreSession');
    }

    /* 
     * Returns true if the specified user have a store session.
     */
    public static function hasStoreSession(User $user) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\' 
                                    AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

        $database->close();

        return $result->num_rows > 0;
    }

    /*
     * Create a new store session.
     */
    public static function createStoreSession(User $user, TicketType $ticketType, $amount, $price) {
        $code = bin2hex(openssl_random_pseudo_bytes(16));
    
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('INSERT INTO `' . Settings::db_table_infected_tickets_storesessions . '` (`userId`, `ticketTypeId`, `amount`, `code`, `price`, `datetime`) 
                                    VALUES (\'' . $user->getId() . '\', 
                                            \'' . $ticketType->getId() . '\', 
                                            \'' . $database->real_escape_string($amount) . '\', 
                                            \'' . $code . '\',
                                            \'' . $database->real_escape_string($price) . '\',
                                            \'' . date('Y-m-d H:i:s') . '\');');

        $database->close();

        return $code;
    }

    /*
     * Removes the specified store session.
     */
    public static function removeStoreSession(StoreSession $storeSession) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('DELETE FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `id` = ' . $storeSession->getId() . ';');

        $database->close();
    }

    /*
     * This is used to validate a payment.
     */
    public static function isPaymentValid($totalPrice, StoreSession $storeSession) {
        return $storeSession->getPrice() == $totalPrice;
    }
    
    /*
     * Returns the amount of reserved tickets for the specified ticket type.
     */
    public static function getReservedTicketCount(TicketType $ticketType) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT `amount` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `ticketTypeId` = \'' . $ticketType->getId() . '\' 
                                    AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

        $database->close();

        $reservedCount = 0;

        while ($row = $result->fetch_array()) {
            $reservedCount += $row['amount'];
        }

        return $reservedCount;
    }

    /*
     * Returns the oldest valid time a store session can be from.
     */
    private static function oldestValidTimestamp() {
        return date('Y-m-d H:i:s', time() - Settings::storeSessionTime);
    }

    /*
     * Returns the user with a store session with the specified code.
     */
    public static function getUserByStoreSessionCode($code) {
        $database = Database::open(Settings::db_name_infected);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
                                    WHERE `id` = (SELECT `userId` FROM `' . Settings::db_name_infected_tickets . '`.`' . Settings::db_table_infected_tickets_storesessions . '`
                                                  WHERE `code`=' . $database->real_escape_string($code) . ' 
                                                  AND `datetime` > ' . self::oldestValidTimestamp() . ');');

        $database->close();

        return $result->fetch_object('User');
    }

    public static function purchaseComplete(StoreSession $storeSession, Payment $payment) {
        if ($storeSession != null) {
            // Checks are ok, lets buy!
            for ($i = 0; $i < $storeSession->getAmount(); $i++) {
                TicketHandler::createTicket($storeSession->getUser(), $storeSession->getTicketType(), $payment);
            }

            self::deleteStoreSession($storeSession);

            return true;
        }

        return false;
    }
}
?>