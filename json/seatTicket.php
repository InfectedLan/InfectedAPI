<?php
	require_once 'session.php';
	require_once 'handlers/tickethandler.php';
	require_once 'handlers/seathandler.php';

	$result = false;
	$message = null;

	if(Session::isAuthenticated())
	{
		$user = Session::getCurrentUser();
		if(isset($_GET["ticket"])&&
			isset($_GET["seat"]))
		{
			$ticket = TicketHandler::getTicket($_GET['ticket']);
			$seat = SeatHandler::getSeat($_GET['seat']);
			if(isset($ticket))
			{
				if(isset($seat))
				{
					if($ticket->canSeat($user) || $user->hasPermission('*') || $user->hasPermission('functions.tickets'))
					{
						if(!SeatHandler::hasOwner($seat))
						{
							$seatEvent = SeatHandler::getEvent($seat);
							$ticketEvent = $ticket->getEvent();
							if($seatEvent->getId() == $ticketEvent->getId() )
							{
								TicketHandler::changeSeat($ticket, $seat);
								$result = true;
								$message = "Billetten har fått nytt sete ^-^";
							}
							else
							{
								$message = "Billetten og setet er ikke fra samme event!";
							}
						}
						else
						{
							$message = "Det er noen som sitter i det setet! n_n";
						}
					}
					else
					{
						$message = "Du har ikke tillatelse til å seate den billetten!";
					}
				}
				else
				{
					$message = "Setet eksisterer ikke!";
				}
			}
			else
			{
				$message = "Biletten eksisterer ikke!";
			}
		}
		else
		{
			$message = "Du har ikke fylt inn alle feltene!";
		}
	}
	else
	{
		$message = "Du er ikke logget inn!";
	}

	echo json_encode(array('result' => $result, 'message' => $message));
?>