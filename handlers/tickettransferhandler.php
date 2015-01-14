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
                                         
        $row = $result->fetch_array();

        if ($row) {
            return new TicketTransfer($row['id'],
                                      $row['ticketId'], 
                                      $row['fromId'],
                                      $row['toId'],
                                      $row['datetime'],                              
                                      $row['revertable']);
        }
    }

    //Returns list of transfers that are eligible for reverting
    public static function getRevertableTransfers($user) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $wantedTimeLimit = time() - Settings::ticketTransferTime;

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickettransfers . '` 
          WHERE `revertable` = true 
          AND `fromId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
          AND `datetime` > \'' . date('Y-m-d H:i:s', $wantedTimeLimit) . '\';');

        $transferrableList = array();

        while($row = $result->fetch_array()) {
        	$transferrableTicket = new TicketTransfer($row['id'],
                                      $row['ticketId'], 
                                      $row['fromId'],
                                      $row['toId'],
                                      $row['datetime'],                              
                                      $row['revertable']);
            array_push($transferrableList, $transferrableTicket);
        }

        return $transferrableList;
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
								 SET `revertable`=\'0\'
								 WHERE `id` = \'' . $mysql->real_escape_string($transfer->getId()) . '\';');
	}
    
    /*
     * Transfers a given ticket from it's current user to the user specified.
     */
    public static function transfer($ticket, $user) {
        // Check that the ticket is for current event, we don't allow transfers of old tickets.
        if ($ticket->getEvent()->getId() == EventHandler::getCurrentEvent()->getId()) {
        
            // If the ticket is checked in, we don't allow transfers.
            if (!$ticket->isCheckedIn()) {
            
                // Check that the new user isn't the same as the old one.
                if ($ticket->getUser()->getId() != $user->getId()) {
                    // Add row to ticket transfers table.
                    self::createTransfer($ticket, $user, true);
                                                
                    // Actually change the user of the ticket.
                    TicketHandler::updateTicketUser($ticket, $user);
                }
            }
        }
    }
    
    /*
     * Reverts most recent transfer for given ticket, if a 
     */
    public static function revertTransfer($ticket, $user) {
        $transfer = self::getTransferFromTicket($ticket);
        $timeLimit = Settings::ticketTransferTime;
        
        // Check that the ticket is for current event, we don't allow reverting transfers for old tickets.
        if ($ticket->getEvent()->getId() == EventHandler::getCurrentEvent()->getId()) {
        
            // If the ticket is checked in, we don't allow transfers.
            if (!$ticket->isCheckedIn()) {
            
                // Check if the user specified matches the former user of the ticket.
                if ($transfer->getFrom()->getId() == $user->getId()) {
                
                    // Check if the ticket is revertable and that the time limit isn't run out.
                    if ($transfer->getDateTime() + $timeLimit >= time() && $transfer->isRevertable()) {
                        // Add row to ticket transfers table.
						self::createTransfer($ticket, $transfer->getFrom(), false);
						
						// Prevent the original transfer from being reverted.
						self::freezeTransfer($transfer->getId());
                        
                        // Actually change the user of the ticket.
                        TicketHandler::updateTicketUser($ticket, $user);
                    } else {
                    	return "Det er for sent å overføre denne billetten!";
                    }
                } else {
                	return "Du er ikke den gamle eieren av billetten!";
                }
            } else {
            	return "Billetten er sjekket inn!";
            }
        } else {
        	return "Billetten er for et gammelt arrangement!";
        }
    }
}
?>