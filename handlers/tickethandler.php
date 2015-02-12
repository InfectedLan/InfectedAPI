<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'handlers/seathandler.php';
require_once 'handlers/storesessionhandler.php';
require_once 'handlers/tickettransferhandler.php';
require_once 'objects/ticket.php';
require_once 'objects/tickettransfer.php';

class TicketHandler {
    public static function getTicket($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('Ticket');
    }
    
    public static function getTicketForUser($event, $user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                 AND `eventId` = \'' . $mysql->real_escape_string($event->getId()) . '\' 
                                 LIMIT 1;');

		$mysql->close();

        return $result->fetch_object('Ticket');
    }
    
    public static function getTicketsForUser($event, $user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\' 
                                 AND `eventId`= ' . $mysql->real_escape_string($event->getId()) . ';');

		$mysql->close();
								 
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }

    //Renamed from hasUserTicket to make function behaviour more obvious
    public static function hasUserAnyTicket($user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');

		$mysql->close();
									  
        $row = $result->fetch_array();
        
        return $row ? true : false;    
    }

    // Renamed from hasTicket to make function behaviour more obvious
    public static function hasTicketForEvent($event, $user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                 AND `eventId` = \'' . $mysql->real_escape_string($event->getId()) . '\' 
                                 LIMIT 1;');

		$mysql->close();

        $row = $result->fetch_array();
        
        return $row ? true : false;
    }
    
    public static function getTicketsForOwner($user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');

		$mysql->close();
									  
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }
    
    public static function getTicketsForOwnerAndEvent($user, $event) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\' 
                                 AND `eventId`= ' . $mysql->real_escape_string($event->getId()) . '
                                 LIMIT 1;');

		$mysql->close();
									  
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }
    
    public static function getTicketList($event) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `eventId` = \'' . $mysql->real_escape_string($event->getId()) . '\';');

		$mysql->close();
									  
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }

    public static function getTicketCount($event) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `eventId` = \'' . $mysql->real_escape_string($event->getId()) . '\';');
        $mysql->close();

        return $result->num_rows;
    }

    public static function setSeater($ticket, $newSeater) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        if (!isset($newSeater) || empty($newSeater)) {
            $result = $mysql->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
                                     SET `seaterId` = \'0\' 
                                     WHERE `id` = \'' . $mysql->real_escape_string($ticket->getId()) . '\';');
        } else {
            $result = $mysql->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
                                     SET `seaterId` = \'' . $mysql->real_escape_string($newSeater->getId()) . '\' 
                                     WHERE `id` = \'' . $mysql->real_escape_string($ticket->getId()) . '\';');
        }
        
        $mysql->close();
    }

    public static function createTicket($user, $ticketType, $payment) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('INSERT INTO `' . Settings::db_table_infected_tickets_tickets . '` (`eventId`, `paymentId`, `typeId`, `buyerId`, `userId`) 
                                 VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\', 
                                         \'' . $mysql->real_escape_string($payment->getId()) . '\', 
                                         \'' . $mysql->real_escape_string($ticketType->getId()) . '\', 
                                         \'' . $mysql->real_escape_string($user->getId()) . '\', 
                                         \'' . $mysql->real_escape_string($user->getId()) . '\');');

        $mysql->close();
    }    
    
    public static function getTicketsSeatableByUser($user, $event) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
								 WHERE (`seaterId`=\'' . $mysql->real_escape_string($user->getId()) . '\'
                                        OR (`userId`=\'' . $mysql->real_escape_string($user->getId()) . '\' 
                                        AND `seaterId` = \'0\'))
                                 AND `eventId`=\'' . $mysql->real_escape_string($event->getId()) . '\';');
    
		$mysql->close();
	
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }
    
    public static function changeSeat($ticket, $seat) {
        // Check that the current event matches tickets, we don't allow seating of old tickets.
        if ($ticket->getEvent()->equals(EventHandler::getCurrentEvent())) {
            $mysql = MySQL::open(Settings::db_name_infected_tickets);

            $result = $mysql->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
                                     SET `seatId` = \'' . $mysql->real_escape_string($seat->getId()) . '\' 
                                     WHERE `id` = \'' . $mysql->real_escape_string($ticket->getId()) . '\';');
            
            $mysql->close();
        }
    }
    
    public static function updateTicketUser($ticket, $user) {
        if (!$user->equals($ticket->getUser())) {
            $mysql = MySQL::open(Settings::db_name_infected_tickets);
            
            // Change the user of the ticket.
            $mysql->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
                           SET `userId` = ' . $mysql->real_escape_string($user->getId()) . ' 
                           WHERE `id` = ' . $mysql->real_escape_string($ticket->getId()) . ';');
            
            $mysql->close();
        }
    }
}
?>