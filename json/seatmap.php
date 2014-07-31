<?php
	require_once 'session.php';
	require_once 'handlers/seatmaphandler.php';

	$result = false;
	$message = null;
	$seatmapData = null; //Array of rows

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
				foreach ($rows as $row) 
				{
					$seats = RowHandler::getSeats($row);
					$seatData = array();

					foreach ($seats as $seat) 
					{
						array_push($seatData, array('id' => $seat->getId(), 
													'number' => $seat->getNumber(), 
													'humanName' => SeatHandler::getHumanString($seat) ));
					}

					$rowData = array('seats' => $seatData, 'id' => $row->getId(), 'x' => $row->getX(), 'y' => $row->getY(), 'number' => $row->getNumber());
					array_push($seatmapData, $rowData);
				}
			}
		}
	}
	if($result)
	{
		echo json_encode(array('result' => $result, 'rows' => $seatmapData));
	}
	else
	{
		echo json_encode(array('result' => $result, 'message' => $message));
	}
?>