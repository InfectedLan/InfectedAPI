<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/ticket.php';
require_once 'objects/event.php';
require_once 'objects/user.php';
require_once 'objects/tickettype.php';
require_once 'objects/seat.php';

class TicketHandler {
    public static function getTicket($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('Ticket');
    }
    
    public static function getTicketForUser(Event $event, User $user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `userId` = \'' . $user->getId() . '\'
                                 AND `eventId` = \'' . $event->getId() . '\' 
                                 LIMIT 1;');

		$mysql->close();

        return $result->fetch_object('Ticket');
    }
    
    public static function getTicketsForUser(Event $event, User $user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `userId` = \'' . $user->getId() . '\' 
                                 AND `eventId`= ' . $event->getId() . ';');

		$mysql->close();
								 
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }

    //Renamed from hasUserTicket to make function behaviour more obvious
    public static function hasUserAnyTicket(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `userId` = \'' . $user->getId() . '\';');

		$mysql->close();
									  
        return $result->num_rows > 0;
    }

    // Renamed from hasTicket to make function behaviour more obvious
    public static function hasTicketForEvent(Event $event, User $user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `userId` = \'' . $user->getId() . '\'
                                 AND `eventId` = \'' . $event->getId() . '\' 
                                 LIMIT 1;');

		$mysql->close();

        return $result->num_rows > 0;
    }
    
    public static function getTicketsForOwner(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `userId` = \'' . $user->getId() . '\';');

		$mysql->close();
									  
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }
    
    public static function getTicketsForOwnerAndEvent(User $user, Event $event) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `userId` = \'' . $user->getId() . '\' 
                                 AND `eventId`= ' . $event->getId() . '
                                 LIMIT 1;');

		$mysql->close();
									  
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }
    
    public static function getTicketList(Event $event) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `eventId` = \'' . $event->getId() . '\';');

		$mysql->close();
									  
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }

    public static function getTicketCount(Event $event) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `eventId` = \'' . $event->getId() . '\';');
        $mysql->close();

        return $result->num_rows;
    }

    public static function setSeater(Ticket $ticket, User $newSeater) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        if (!isset($newSeater) || 
            empty($newSeater)) {
            $result = $mysql->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
                                     SET `seaterId` = \'0\' 
                                     WHERE `id` = \'' . $ticket->getId() . '\';');
        } else {
            $result = $mysql->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
                                     SET `seaterId` = \'' . $newSeater->getId() . '\' 
                                     WHERE `id` = \'' . $ticket->getId() . '\';');
        }
        
        $mysql->close();
    }

    public static function createTicket(User $user, TicketType $ticketType, $payment) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('INSERT INTO `' . Settings::db_table_infected_tickets_tickets . '` (`eventId`, `paymentId`, `typeId`, `buyerId`, `userId`) 
                                 VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\', 
                                         \'' . $payment->getId() . '\', 
                                         \'' . $ticketType->getId() . '\', 
                                         \'' . $user->getId() . '\', 
                                         \'' . $user->getId() . '\');');

        $mysql->close();
    }    
    
    public static function getTicketsSeatableByUser(User $user, Event $event) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
								 WHERE (`seaterId`=\'' . $user->getId() . '\'
                                        OR (`userId`=\'' . $user->getId() . '\' 
                                        AND `seaterId` = \'0\'))
                                 AND `eventId`=\'' . $event->getId() . '\';');
    
		$mysql->close();
	
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }
    
    public static function changeSeat(Ticket $ticket, Seat $seat) {
        // Check that the current event matches tickets, we don't allow seating of old tickets.
        if ($ticket->getEvent()->equals(EventHandler::getCurrentEvent())) {
            $mysql = MySQL::open(Settings::db_name_infected_tickets);

            $result = $mysql->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
                                     SET `seatId` = \'' . $seat->getId() . '\' 
                                     WHERE `id` = \'' . $ticket->getId() . '\';');
            
            $mysql->close();
        }
    }
    
    public static function updateTicketUser(Ticket $ticket, User $user) {
        if (!$user->equals($ticket->getUser())) {
            $mysql = MySQL::open(Settings::db_name_infected_tickets);
            
            // Change the user of the ticket.
            $mysql->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
                           SET `userId` = ' . $user->getId() . ' 
                           WHERE `id` = ' . $ticket->getId() . ';');
            
            $mysql->close();
        }
    }
}
?>