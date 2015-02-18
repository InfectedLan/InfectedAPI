<?php
require_once 'session.php';
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/rowhandler.php';
require_once 'handlers/seathandler.php';

$result = false;
$message = null;
$seatmapData = null; //Array of rows
$backgroundImage = null; //File name of background image. Didnt know how else to do this.

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id'])) {
		$seatmap = SeatmapHandler::getSeatmap($_GET['id']);
		
		if ($seatmap != null) {
			$seatmapData = array();
			$backgroundImage = $seatmap->getBackgroundImage();
			
			foreach ($seatmap->getRows() as $row) {
				$seatData = array();

				foreach ($row->getSeats() as $seat) {
					array_push($seatData, array('id' => $seat->getId(), 
												'number' => $seat->getNumber(), 
												'humanName' => $seat->getString()));
				}

				array_push($seatmapData, array('seats' => $seatData, 
											   'id' => $row->getId(), 
											   'x' => $row->getX(), 
											   'y' => $row->getY(), 
											   'number' => $row->getNumber());
			}

			$result = true;
		} else {
			$message = '<p>Seatmappet eksisterer ikke!</p>';
		}
	} else {
		$message = '<p>Seatmappet ikke spesifisert.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

if ($result) {
	echo json_encode(array('result' => $result, 'rows' => $seatmapData, 'backgroundImage' => $backgroundImage));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>