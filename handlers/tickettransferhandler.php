<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/tickettype.php';
require_once 'objects/tickettransfer.php';

class TicketTransferHandler {
	public static function getTransfer($ticket, $user) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = $con->query($con, 'SELECT * FROM `' . Settings::db_table_infected_tickets_tickettransfers . '` 
									 WHERE `ticketId` = \'' . $con->real_escape_string($ticket->getId()) . '\'
									 AND `fromId` = \'' . $con->real_escape_string($user->getId()) . '\'
									 ORDER BY `datetime` DESC
									 LIMIT 1;');

		$row = mysqli_fetch_array($result);

		$con->close();

		if ($row) {
			return new TicketTransfer($row['id'],
									  $row['ticketId'], 
								      $row['fromId'],
							          $row['toId'],
							          $row['datetime'],							  
							          $row['revertable']);
		}
	}
	
	public static function createTransfer($ticket, $user, $revertable) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$con->query('INSERT INTO `' . Settings::db_table_infected_tickets_tickettransfers . '` (`ticketId`, `fromId`, `toId`, `datetime`, `revertable`)
					 VALUES (\'' . $con->real_escape_string($ticket->getId()) . ', 
							 \'' . $con->real_escape_string($ticket->getUser()->getId()) . ', 
							 \'' . $con->real_escape_string($user->getId()) . ',
							 \'' . date('Y-m-d H:i:s') . ',
							 \'' . $revertable . ');');

		$con->close();
	}
	
	/*
	 * Transfers a given ticket from it's current user to the user specified.
	 */
	public static function transfer($ticket, $user) {
		// Check that the new user isn't the same as the old one.
		if ($ticket->getUser()->getId != $user->getId()) {
			$con = MySQL::open(Settings::db_name_infected_tickets);

			// Add row to ticket transfers table.
			self::createTransfer($ticket, $user, true);
										
			// Actually change the user of the ticket.
			TicketHandler::updateTicketUser($ticket, $user);

			$con->close();
		}
	}
	
	/*
	 * Reverts most recent transfer for given ticket, if a 
	 */
	public static function revertTransfer($ticket, $user) {
		$transfer = self::getTransfer($ticket, $user);
		$timeLimit = 86400;
		
		// Check if the user specified matches the former user of the ticket.
		if ($transfer->getFrom()->getId() == $user->getId()) {
			// Check if the ticket is revertable and that the time limit isn't run out.
			if ($transfer->getDateTime() + $timeLimit <= time() && $transfer->isRevertable()) {
				// Add row to ticket transfers table.
				self::createTransfer($ticket, $transfer->getFrom(), false);
				
				// Actually change the user of the ticket.
				TicketHandler::updateTicketUser($user);
			}
		}
	}
}
?>