<?php
require_once 'session.php';
require_once 'handlers/rowhandler.php';
require_once 'handlers/seatmaphandler.php';

$result = false;
$message = null;
$id = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.seatmap')) {
		if(isset($_GET["row"])) {
			$row = RowHandler::getRow($_GET["row"]);
			
			if (isset($row)) {
				if (isset($_GET["numSeats"])) {
					$seats = $_GET["numSeats"];
					
					if (is_numeric($seats)) {
						for ($i = 0; $i < $seats; $i++) {
							RowHandler::addSeat($row);
						}
						
						$result = true;
					} else {
						$message = "Antall seter er ikke et tall!";
					}
				} else {
					$message = "Antall seter er ikke satt!";
				}
			} else {
				$message = "Raden eksisterer ikke!";
			}
		} else {
			$message = "Raden er ikke satt!";
		}
	} else {
		$message = "Du har ikke tillatelse til å legge til en rad!";
	}
} else {
	$message = "Du må logge inn først!";
}

if ($result) {
	echo json_encode(array('result' => $result));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>