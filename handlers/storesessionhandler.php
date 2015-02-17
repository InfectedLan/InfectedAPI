<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/tickethandler.php';
require_once 'objects/storesession.php';
require_once 'objects/user.php';
require_once 'objects/tickettype.php';
require_once 'objects/payment.php';

class StoreSessionHandler {
    public static function getStoreSession($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                 WHERE `id` = ' . $mysql->real_escape_string($id) . ';');
        
        $mysql->close();
		
		return $result->fetch_object('StoreSession');
    }
    
    public static function getStoreSessionForUser(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                 WHERE `userId` = \'' . $user->getId() . '\' 
                                 AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

        $mysql->close();

        return $result->fetch_object('StoreSession');
    }

    public static function registerStoreSession(User $user, TicketType $ticketType, $amount, $price) {
        $code = bin2hex(openssl_random_pseudo_bytes(16));
    
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('INSERT INTO `' . Settings::db_table_infected_tickets_storesessions . '` (`userId`, `ticketType`, `amount`, `code`, `price`, `datetime`) 
                                 VALUES (\'' . $user->getId() . '\', 
                                         \'' . $ticketType->getId() . '\', 
                                         \'' . $mysql->real_escape_string($amount) . '\', 
                                         \'' . $code . '\',
                                         \'' . $mysql->real_escape_string($price) . '\',
                                         \'' . $mysql->real_escape_string(date('Y-m-d H:i:s')) . '\');');

        $mysql->close();

        return $code;
    }

    public static function deleteStoreSession(StoreSession $storeSession) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('DELETE FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                 WHERE `id` = ' . $storeSession->getId() . ';');

        $mysql->close();
    }

    // Used to validate a payment.
    public static function isPaymentValid($totalPrice, StoreSession $storeSession) {
        return $storeSession->getPrice() == $totalPrice;
    }
    
    public static function hasStoreSession(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                 WHERE `userId` = \'' . $user->getId() . '\' 
                                 AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

        $mysql->close();

        return $result->num_rows > 0;
    }

    public static function getReservedTicketCount(TicketType $ticketType) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `amount` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                 WHERE `ticketType` = \'' . $ticketType->getId() . '\' 
                                 AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

        $mysql->close();

        $reservedCount = 0;

        while ($row = $result->fetch_array()) {
            $reservedCount += $row['amount'];
        }

        return $reservedCount;
    }

    private static function oldestValidTimestamp() {
        return date('Y-m-d H:i:s', time() - Settings::storeSessionTime);
    }

    private static function getStoreSessionFromKey($key) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                 WHERE `code` = ' . $mysql->real_escape_string($code) . ' 
                                 AND `datetime` > ' . self::oldestValidTimestamp() . ';');

        $mysql->close();

        return $result->fetch_object('StoreSession');
    }

    public static function getUserIdFromKey($key) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `userId` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                 WHERE `code`=' . $mysql->real_escape_string($code) . ' 
                                 AND `datetime` > ' . self::oldestValidTimestamp() . ';');

        $mysql->close();

        $row = $result->fetch_array();

        if ($row) {
            return $row['userId'];
        }
    }

    public static function purchaseComplete(StoreSession $storeSession, Payment $payment) {
        if ($storesession != null) {
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