<?php
	require_once 'session.php';
	require_once 'handlers/seatmaphandler.php';

	$result = false;
	$message = null;
	$seatmapData = null; //Array of rows
	$backgroundImage = null; //File name of background image. Didnt know how else to do this.

	if(!isset($_GET["id"]))
	{
		$message = "ID er ikke satt!";
	}
	else
	{
		if(!Session::isAuthenticated())
		{
			$message = "Du er ikke logget inn!";
		}
		else
		{
			$seatmap = SeatmapHandler::getSeatmap($_GET["id"]);
			if(!isset($seatmap))
			{
				$message = "Seatmappet eksisterer ikke!";
			}
			else
			{
				$result = true;
				$rows = SeatmapHandler::getRows($seatmap);
				$seatmapData = array();
				$backgroundImage = $seatmap->getBackgroundImage();
				foreach ($rows as $row) 
				{
					$seats = RowHandler::getSeats($row);
					$seatData = array();

					foreach ($seats as $seat) 
					{
						$data = array();

						$data['id'] = $seat->getId();
						$data['number'] = $seat->getNumber();
						$data['humanName'] = SeatHandler::getHumanString($seat);

						$owner = SeatHandler::getOwner($seat);

						if(!isset($owner))
						{
							$data['occupied'] = false;
						}
						else
						{
							$data['occupied'] = true;
							$ticket = SeatHandler::getTicket($seat);
							$data['occupiedTicket'] = array('id' => $ticket->getId(), 
															'owner' => htmlspecialchars($owner->getDisplayName()) );
						}

						array_push($seatData, $data);
					}

					$rowData = array('seats' => $seatData, 'id' => $row->getId(), 'x' => $row->getX(), 'y' => $row->getY(), 'number' => $row->getNumber());
					array_push($seatmapData, $rowData);
				}
			}
		}
	}
	if($result)
	{
		echo json_encode(array('result' => $result, 'rows' => $seatmapData, 'backgroundImage' => $backgroundImage));
	}
	else
	{
		echo json_encode(array('result' => $result, 'message' => $message));
	}
?>