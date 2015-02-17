<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/tickethandler.php';
require_once 'objects/storesession.php';
require_once 'objects/user.php';
require_once 'objects/tickettype.php';
require_once 'objects/payment.php';

class StoreSessionHandler {
    public static function getStoreSession($id) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `id` = ' . $database->real_escape_string($id) . ';');
        
        $database->close();
		
		return $result->fetch_object('StoreSession');
    }
    
    public static function getStoreSessionForUser(User $user) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\' 
                                    AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

        $database->close();

        return $result->fetch_object('StoreSession');
    }

    public static function registerStoreSession(User $user, TicketType $ticketType, $amount, $price) {
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

    public static function deleteStoreSession(StoreSession $storeSession) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('DELETE FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `id` = ' . $storeSession->getId() . ';');

        $database->close();
    }

    // Used to validate a payment.
    public static function isPaymentValid($totalPrice, StoreSession $storeSession) {
        return $storeSession->getPrice() == $totalPrice;
    }
    
    public static function hasStoreSession(User $user) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\' 
                                    AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

        $database->close();

        return $result->num_rows > 0;
    }

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

    private static function oldestValidTimestamp() {
        return date('Y-m-d H:i:s', time() - Settings::storeSessionTime);
    }

    private static function getStoreSessionFromKey($key) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `code` = ' . $database->real_escape_string($code) . ' 
                                    AND `datetime` > ' . self::oldestValidTimestamp() . ';');

        $database->close();

        return $result->fetch_object('StoreSession');
    }

    public static function getUserIdFromKey($key) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT `userId` FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `code`=' . $database->real_escape_string($code) . ' 
                                    AND `datetime` > ' . self::oldestValidTimestamp() . ';');

        $database->close();

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