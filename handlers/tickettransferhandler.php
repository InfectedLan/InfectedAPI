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
require_once 'handlers/tickethandler.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/tickettransfer.php';
require_once 'objects/ticket.php';
require_once 'objects/user.php';

class TicketTransferHandler {
	/*
	 * Get a ticket transer by the internal id.
	 */
    public static function getTransferFromTicket(Ticket $ticket) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickettransfers . '` 
                                    WHERE `ticketId` = \'' . $ticket->getId() . '\'
                                    ORDER BY `datetime` DESC
                                    LIMIT 1;');

        $database->close();
		
		    return $result->fetch_object('TicketTransfer');
    }

    /*
     * Returns list of transfers that are eligible for reverting.
     */
    public static function getRevertableTransfers(User $user) {
 		$wantedTimeLimit = time() - Settings::ticketTransferTime;

        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickettransfers . '` 
                                    WHERE `fromId` = \'' . $user->getId() . '\'
                                    AND `revertable` = \'1\'
                                    AND `datetime` > \'' . date('Y-m-d H:i:s', $wantedTimeLimit) . '\';');

        $database->close();

        $transferList = array();

        while ($object = $result->fetch_object('TicketTransfer')) {
            array_push($transferList, $object);
        }

        return $transferList;
    }
    
    /*
     * Create a new ticket transfer.
     */
    public static function createTransfer(Ticket $ticket, User $user, $revertable) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $database->query('INSERT INTO `' . Settings::db_table_infected_tickets_tickettransfers . '` (`ticketId`, `fromId`, `toId`, `datetime`, `revertable`)
                          VALUES (\'' . $ticket->getId() . '\', 
                                  \'' . $ticket->getUser()->getId() . '\', 
                                  \'' . $user->getId() . '\',
                                  \'' . date('Y-m-d H:i:s') . '\',
                                  \'' . $revertable . '\');');

        $database->close();
    }
    
    /*
     * Freeze a specific ticketransfer.
     */
    public static function freezeTransfer(TicketTransfer $ticketTransfer) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('UPDATE `' . Settings::db_table_infected_tickets_tickettransfers .  '` 
                                    SET `revertable` = \'0\'
                                    WHERE `id` = \'' . $ticketTransfer->getId() . '\';');
								 
		    $database->close();
    }
    
    /*
     * Transfers a given ticket from it's current user to the user specified.
     */
    public static function transfer(Ticket $ticket, User $user) {
		    $ticketTransfer = self::getTransferFromTicket($ticket);
	
        // Check that the ticket is for current event, we don't allow transfers of old tickets.
    		if ($ticket->getEvent()->equals(EventHandler::getCurrentEvent())) {
    			  // Check that the recipient user is activated.
      			if ($user->isActivated()) {
      		
        				// If the ticket is checked in, we don't allow transfers.
        				if (!$ticket->isCheckedIn()) {
          				
          					// Check that the new user isn't the same as the old one.
          					if (!$user->equals($ticket->getUser())) {
            						// Prevent the original transfer from being reverted.
            						if ($ticketTransfer != null) {
            							self::freezeTransfer($ticketTransfer);
            						}
            					
            						// Add row to ticket transfers table.
            						self::createTransfer($ticket, $user, true);
            													
            						// Actually change the user of the ticket.
            						TicketHandler::updateTicketUser($ticket, $user);
          					}
        				}
      			}
        }
    }
    
    /*
     * Reverts most recent transfer for given ticket.
     */
    public static function revertTransfer(Ticket $ticket, User $user) {
        $ticketTransfer = self::getTransferFromTicket($ticket);
        $timeLimit = Settings::ticketTransferTime;
		
        // Check that the ticket is for current event, we don't allow reverting transfers for old tickets.
        if ($ticket->getEvent()->equals(EventHandler::getCurrentEvent())) {
        
            // Check that the recipient user is activated.
      			if ($user->isActivated()) {
      				
        				// If the ticket is checked in, we don't allow transfers.
        				if (!$ticket->isCheckedIn()) {
        				
          					// Check if the user specified matches the former user of the ticket.     
          					if ($user->equals($ticketTransfer->getFrom())) {
          		
            						// Check if the ticket is revertable.
            						if ($ticketTransfer->isRevertable()) {
            						
              							// Check that the time limit isn't run out.
              							if ($ticketTransfer->getDateTime() + $timeLimit >= time()) {
              								// Prevent the original transfer from being reverted.
              								self::freezeTransfer($ticketTransfer);
              								
              								// Add row to ticket transfers table.
              								self::createTransfer($ticket, $ticketTransfer->getFrom(), false);
              								
              								// Actually change the user of the ticket.
              								TicketHandler::updateTicketUser($ticket, $user);
              							}
            						}
          					}
        				}
      			}
        }
    }
}
?>