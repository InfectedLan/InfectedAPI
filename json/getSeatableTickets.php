<?php
	require_once 'session.php';
	require_once 'handlers/tickethandler.php';
	require_once 'handlers/seatmaphandler.php';

	$result = false;
	$message = null;
	$ticketData = array();
	if(Session::isAuthenticated())
	{
		if(isset($_GET['seatmap']))
		{
			$seatmap = SeatmapHandler::getSeatmap($_GET['seatmap']);
			if(isset($seatmap))
			{
				$event = SeatmapHandler::getEvent($seatmap);
				$user = Session::getCurrentUser();
				$tickets = TicketHandler::getTicketsSeatableByUser($user, $event);
				foreach($tickets as $ticket)
				{
					$ticketOwner = $ticket->getOwner();
					$data = array();
					$data['id'] = $ticket->getId();
					$data['owner'] = $ticketOwner->getDisplayName();
					array_push($ticketData, $data);
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