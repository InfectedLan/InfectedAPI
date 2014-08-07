<?php
	require_once 'session.php';
	require_once 'handlers/seatmaphandler.php';

	$result = false;
	$message = null;
	$id = null;

	if (Session::isAuthenticated()) {
		$user = Session::getCurrentUser();
		if ($user->hasPermission('admin.seatmap') ||
			$user->hasPermission('admin')) {
			if(isset($_GET["name"]))
			{
				$seatmap = SeatmapHandler::createNewSeatmap($_GET["name"], "default.png");
				$result = true;
				$id = $seatmap->getId();
			}
			else
			{
				$message = "Navn er ikke satt!";
			}
		}
		else
		{
			$message = "Du har ikke tillatelse til å lage et seatmap!";
		}
	}
	else
	{
		$message = "Du må logge inn først!";
	}

	if($result)
	{
		echo json_encode(array('result' => $result, 'id' => $id));
	}
	else
	{
		echo json_encode(array('result' => $result, 'message' => $message));
	}

?>