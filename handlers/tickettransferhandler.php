<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/tickettransfer.php';

class TicketTransferHandler {
    public static function getTransferFromTicket($ticket) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickettransfers . '` 
                                 WHERE `ticketId` = \'' . $mysql->real_escape_string($ticket->getId()) . '\'
                                 ORDER BY `datetime` DESC
                                 LIMIT 1;');

        $mysql->close();
		
		return $result->fetch_object('TicketTransfer');
    }

    // Returns list of transfers that are eligible for reverting
    public static function getRevertableTransfers($user) {
 		$wantedTimeLimit = time() - Settings::ticketTransferTime;

        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickettransfers . '` 
                                 WHERE `fromId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                 AND `revertable` = \'1\'
                                 AND `datetime` > \'' . date('Y-m-d H:i:s', $wantedTimeLimit) . '\';');

        $mysql->close();

        $transferList = array();

        while ($object = $result->fetch_object('TicketTransfer')) {
            array_push($transferList, $object);
        }

        return $transferList;
    }
    
    public static function createTransfer($ticket, $user, $revertable) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $mysql->query('INSERT INTO `' . Settings::db_table_infected_tickets_tickettransfers . '` (`ticketId`, `fromId`, `toId`, `datetime`, `revertable`)
                       VALUES (\'' . $mysql->real_escape_string($ticket->getId()) . '\', 
                               \'' . $mysql->real_escape_string($ticket->getUser()->getId()) . '\', 
                               \'' . $mysql->real_escape_string($user->getId()) . '\',
                               \'' . date('Y-m-d H:i:s') . '\',
                               \'' . $revertable . '\');');

        $mysql->close();
    }
    
    public static function freezeTransfer($transfer) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('UPDATE `' . Settings::db_table_infected_tickets_tickettransfers .  '` 
                                 SET `revertable` = \'0\'
                                 WHERE `id` = \'' . $mysql->real_escape_string($transfer->getId()) . '\';');
								 
		$mysql->close();
    }
    
    /*
     * Transfers a given ticket from it's current user to the user specified.
     */
    public static function transfer($ticket, $user) {
		$transfer = self::getTransferFromTicket($ticket);
	
        // Check that the ticket is for current event, we don't allow transfers of old tickets.
		if ($ticket->getEvent()->equals(EventHandler::getCurrentEvent())) {
			// Check that the recipient user is activated.
			if ($user->isActivated()) {
		
				// If the ticket is checked in, we don't allow transfers.
				if (!$ticket->isCheckedIn()) {
				
					// Check that the new user isn't the same as the old one.
					if (!$user->equals($ticket->getUser())) {
						// Prevent the original transfer from being reverted.
						if ($transfer != null) {
							self::freezeTransfer($transfer);
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
    public static function revertTransfer($ticket, $user) {
        $transfer = self::getTransferFromTicket($ticket);
        $timeLimit = Settings::ticketTransferTime;
		
        // Check that the ticket is for current event, we don't allow reverting transfers for old tickets.
        if ($ticket->getEvent()->equals(EventHandler::getCurrentEvent())) {
        
			// Check that the recipient user is activated.
			if ($user->isActivated()) {
				
				// If the ticket is checked in, we don't allow transfers.
				if (!$ticket->isCheckedIn()) {
				
					// Check if the user specified matches the former user of the ticket.     
					if ($user->equals($transfer->getFrom())) {
		
						// Check if the ticket is revertable.
						if ($transfer->isRevertable()) {
						
							// Check that the time limit isn't run out.
							if ($transfer->getDateTime() + $timeLimit >= time()) {
								// Prevent the original transfer from being reverted.
								self::freezeTransfer($transfer);
								
								// Add row to ticket transfers table.
								self::createTransfer($ticket, $transfer->getFrom(), false);
								
								// Actually change the user of the ticket.
								TicketHandler::updateTicketUser($ticket, $user);
							} else {
								return 'Det er for sent å overføre denne billetten!';
							}
						} else {
							return 'Billeten kan ikke angres.';
						}
					} else {
						return 'Du er ikke den gamle eieren av billetten!';
					}
				} else {
					return 'Billetten er sjekket inn!';
				}
			} else {
				return 'Brukeren er ikke aktivert, be personen om å aktivere sin bruker.';
			}
        } else {
            return 'Billetten er for et gammelt arrangement!';
        }
    }
}
?>