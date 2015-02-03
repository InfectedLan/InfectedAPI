<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/storesession.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/tickettypehandler.php';

class StoreSessionHandler {
    public static function getStoreSession($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                      WHERE `id` = ' . $mysql->real_escape_string($id) . ';');
        
        $row = $result->fetch_array();

        $mysql->close();

        if ($row) {
            return new StoreSession($row['id'], 
                                    $row['userId'], 
                                    $row['ticketType'],
                                    $row['amount'],
                                    $row['code'],
                                    $row['price'],
                                    $row['datetime']);
        }
    }
    
    public static function registerStoreSession($user, $type, $amount, $price) {
        $code = bin2hex(openssl_random_pseudo_bytes(16));
    
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('INSERT INTO `' . Settings::db_table_infected_tickets_storesessions . '` (`userId`, `ticketType`, `amount`, `code`, `price`, `datetime`) 
                                      VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                                              \'' . $mysql->real_escape_string($type->getId()) . '\', 
                                              \'' . $mysql->real_escape_string($amount) . '\', 
                                              \'' . $code . '\',
                                              \'' . $mysql->real_escape_string($price) . '\',
                                              \'' . $mysql->real_escape_string(date('Y-m-d H:i:s')) . '\');');

        $mysql->close();

        return $code;
    }
    
    public static function getStoreSessionForUser($user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\' 
                                      AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

        $row = $result->fetch_array();

        $mysql->close();

        if ($row) {
            return self::getStoreSession($row['id']);
        }
    }

    //Used to validate a payment
    public static function isPaymentValid($totalPrice, $session) {
        return $totalPrice == $session->getPrice();
    }
    
    public static function hasStoreSession($user) {
        return self::getStoreSessionForUser($user) != null;
    }

    public static function getReservedTicketCount($ticketType) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `amount` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                      WHERE `ticketType` = \'' . $mysql->real_escape_string($ticketType->getId()) . '\' 
                                      AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

        $reservedCount = 0;

        while ($row = $result->fetch_array()) {
            $reservedCount += $row['amount'];
        }

        $mysql->close();

        return $reservedCount;
    }

    private static function oldestValidTimestamp() {
        return date('Y-m-d H:i:s', time() - Settings::storeSessionTime);
    }

    public static function deleteStoreSession($storeSession) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('DELETE FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                      WHERE `id` = ' . $mysql->real_escape_string($storeSession->getId()) . ';');

        $mysql->close();
    }

    private static function getStoreSessionFromKey($key) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                      WHERE `code` = ' . $mysql->real_escape_string($code) . ' 
                                      AND `datetime` > ' . self::oldestValidTimestamp() . ';');

        $row = $result->fetch_array();

        $mysql->close();

        if ($row) {
            return self::getStoreSession($row['id']);
        }
    }

    public static function getUserIdFromKey($key) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `userId` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                      WHERE `code`=' . $mysql->real_escape_string($code) . ' 
                                      AND `datetime` > ' . self::oldestValidTimestamp() . ';');

        $row = $result->fetch_array();

        $mysql->close();

        if ($row) {
            return $row['userId'];
        }
    }

    public static function purchaseComplete($storeSession, $payment) {
        if (!isset($storeSession)) {
            return false;
        }

        $user = UserHandler::getUser( $storeSession->getUserId() );

        $ticketType = TicketTypeHandler::getTicketType($storeSession->getTicketType());

        // Checks are ok, lets buy!
        for ($i = 0; $i < $storeSession->getAmount(); $i++) {
            TicketHandler::createTicket($user, $payment, $ticketType);
        }

        self::deleteStoreSession($storeSession);

        return true;
    }
}
?>