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
		if (isset($_GET['seatmap'])) {
			$seatmap = SeatmapHandler::getSeatmap($_GET['seatmap']);
			
			if ($seatmap != null) {
				if (isset($_GET['x']) && 
					isset($_GET['y'])) {
					$row = $seatmap->addRow($_GET['x'], $_GET['y']);
					$id = $row->getId();
					$result = true;
				} else {
					$message = '<p>Posisjonen er ikke satt!</p>';
				}
			} else {
				$message = '<p>Seatmappet eksisterer ikke!</p>';
			}
		} else {
			$message = '<p>Seatmap er ikke satt!</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til å legge til en rad!</p>';
	}
} else {
	$message = '<p>Du må logge inn først!</p>';
}

if ($result) {
	echo json_encode(array('result' => $result, 'id' => $id));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>