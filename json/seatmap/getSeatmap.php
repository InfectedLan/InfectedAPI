<?php
require_once 'session.php';
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/rowhandler.php';
require_once 'handlers/seathandler.php';

$result = false;
$message = null;
$seatmapData = null; //Array of rows
$backgroundImage = null; //File name of background image. Didnt know how else to do this.

if (!isset($_GET["id"])) {
	$message = "ID er ikke satt!";
} else {
	if (!Session::isAuthenticated()) {
		$message = "Du er ikke logget inn!";
	} else {
		$seatmap = SeatmapHandler::getSeatmap($_GET["id"]);
		
		if (!isset($seatmap)) {
			$message = "Seatmappet eksisterer ikke!";
		} else {
			$result = true;
			$rows = SeatmapHandler::getRows($seatmap);
			$seatmapData = array();
			$backgroundImage = $seatmap->getBackgroundImage();
			
			foreach ($rows as $row) {
				$seats = RowHandler::getSeats($row);
				$seatData = array();

				foreach ($seats as $seat) {
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

if ($result) {
	echo json_encode(array('result' => $result, 'rows' => $seatmapData, 'backgroundImage' => $backgroundImage));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>