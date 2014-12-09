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
		if (isset($_GET["seatmap"])) {
			$seatmap = SeatmapHandler::getSeatmap($_GET["seatmap"]);
			
			if (isset($seatmap)) {
				if (isset($_GET["x"]) && isset($_GET["y"])) {
					$row = RowHandler::createNewRow($_GET["seatmap"], $_GET["x"], $_GET["y"]);
					$result = true;
					$id = $row->getId();
				} else {
					$message = "Posisjonen er ikke satt!";
				}
			} else {
				$message = "Seatmappet eksisterer ikke!";
			}
		} else {
			$message = "Seatmap er ikke satt!";
		}
	} else {
		$message = "Du har ikke tillatelse til å legge til en rad!";
	}
} else {
	$message = "Du må logge inn først!";
}

if ($result) {
	echo json_encode(array('result' => $result, 'id' => $id));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>