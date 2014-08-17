<?php
	require_once 'session.php';
	require_once 'handlers/tickethandler.php';

	$result = false;
	$message = null;
	$ticketData = null;
	if(Session::isAuthenticated())
	{
		if(isset($_GET['seatmap']))
		{
			//TODO
			$event = EventHandler::getEvent($_GET['event']);
			if(isset($event))
			{
				$user = Session::getCurrentUser();
				$tickets = TicketHandler::getTicketsSeatableByUser($user, $event);
				foreach($tickets as $ticket)
				{
					$ticketOwner = $ticket->getOwner();
					array_push($ticketData, array('id' => $ticket->getId(), 
													'owner' => $ticketOwner->getDisplayName() ));
				}
				$result = true;
			}
			else
			{
				$message = "Eventet finnes ikke!";
			}
		}
		else
		{
			$message = "Mangler event-id";
		}
	}
	else
	{
		$message = "Du er ikke logget inn!";
	}

	if($result)
	{
		echo json_encode(array('result' => $result, 'tickets' => $ticketData));
	}
	else
	{
		echo json_encode(array('result' => $result, 'message' => $message));
	}
?>