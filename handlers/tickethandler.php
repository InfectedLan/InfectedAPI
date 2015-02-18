<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/ticket.php';
require_once 'objects/event.php';
require_once 'objects/user.php';
require_once 'objects/tickettype.php';
require_once 'objects/seat.php';
require_once 'objects/payment.php';

class TicketHandler {
    /*
     * Return the ticket by the internal id.
     */
    public static function getTicket($id) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();
		
		return $result->fetch_object('Ticket');
    }

    /*
     * Returns a list of all the tickets.
     */
    public static function getTickets() {
        $event = EventHandler::getCurrentEvent();
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '`
                                    WHERE `eventId` = \'' . $event->getId() . '\';');

        $database->close();
        
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }

    /*
     * Returns a list of all the tickets for a specified event.
     */
    public static function getTicketsByEvent(Event $event) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '`
                                    WHERE `eventId` = \'' . $event->getId() . '\';');

        $database->close();
        
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }

    /*
     * Return the specified user latest ticket.
     */
    public static function getTicketByUser(User $user) {
        $event = EventHandler::getCurrentEvent();
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\'
                                    AND `eventId` = \'' . $event->getId() . '\' 
                                    LIMIT 1;');

        $database->close();

        return $result->fetch_object('Ticket');
    }

    /*
     * Returns a list of the specified user tickets.
     */
    public static function getTicketsByUser(User $user) {
        $event = EventHandler::getCurrentEvent();
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\' 
                                    AND `eventId`= \'' . $event->getId() . '\';');

        $database->close();
                                 
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }

    /*
     * Returns true if the specified user has a ticket.
     */
    public static function hasTicket(User $user) {
        $event = EventHandler::getCurrentEvent();
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\'
                                    AND `eventId` = \'' . $event->getId() . '\';');

        $database->close();

        return $result->num_rows > 0;
    }

    /*
     * Create a new ticket.
     */
    public static function createTicket(User $user, TicketType $ticketType, Payment $payment) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('INSERT INTO `' . Settings::db_table_infected_tickets_tickets . '` (`eventId`, `paymentId`, `typeId`, `buyerId`, `userId`) 
                                    VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\', 
                                            \'' . $payment->getId() . '\', 
                                            \'' . $ticketType->getId() . '\', 
                                            \'' . $user->getId() . '\', 
                                            \'' . $user->getId() . '\');');

        $database->close();
    }

    /*
     * Update the ticket with the specified user.
     */
    public static function updateTicketUser(Ticket $ticket, User $user) {
        if (!$user->equals($ticket->getUser())) {
            $database = Database::open(Settings::db_name_infected_tickets);
            
            // Change the user of the ticket.
            $database->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
                              SET `userId` = ' . $user->getId() . ' 
                              WHERE `id` = ' . $ticket->getId() . ';');
            
            $database->close();
        }
    }

    /*
     * Update the ticket with the specified seater.
     */
    public static function updateTicketSeater(Ticket $ticket, User $seater) {
        $database = Database::open(Settings::db_name_infected_tickets);
        
        $database->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
                          SET `seaterId` = \'' . ($seater != null ? $seater->getId() : 0) . '\' 
                          WHERE `id` = \'' . $ticket->getId() . '\';');
        
        $database->close();
    }

    /*
     * Update the ticket with the specified seat.
     */
    public static function updateTicketSeat(Ticket $ticket, Seat $seat) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
                                    SET `seatId` = \'' . ($seat != null ? $seat->getId() : 0) . '\' 
                                    WHERE `id` = \'' . $ticket->getId() . '\';');
        
        $database->close();
    }

    /*
     * Removes the specified ticket.
     */
    public static function removeTicket(Ticket $ticket) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('DELETE FROM `' . Settings::db_table_infected_tickets_storesessions . '` 
                                    WHERE `id` = ' . $storeSession->getId() . ';');

        $database->close();
    }

    /*
     * Check in a ticket.
     */
    public static function checkInTicket(Ticket $ticket) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $reuslt = $database->query('INSERT INTO `' . Settings::db_table_infected_tickets_checkedintickets . '` (`ticketId`, `userId`) 
                                    VALUES (\'' . $ticket->getId() . '\',
                                            \'' . $ticket->getUser()->getId() . '\');');

        $database->close();
    }

    /*
     * Returns true if the specified ticket is checked in.
     */
    public static function isTicketCheckedIn(Ticket $ticket) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_checkedintickets . '` 
                                    WHERE `ticketId` = \'' . $ticket->getId() . '\';');

        $database->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Returns true if the specified user is able to seat tickets.
     */
    public static function isTicketsSeatableByUser(User $user) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
								    WHERE (`seaterId` = \'' . $user->getId() . '\' OR (`userId` = \'' . $user->getId() . '\' AND `seaterId` = \'0\'))
                                    AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\';');
    
		$database->close();
	
        $ticketList = array();

        while ($object = $result->fetch_object('Ticket')) {
            array_push($ticketList, $object);
        }

        return $ticketList;
    }
}
?>