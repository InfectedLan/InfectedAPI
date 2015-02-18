<?php
require_once 'session.php';
require_once 'handlers/rowhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.seatmap')) {
		if (isset($_GET['row'])) {
			$row = RowHandler::getRow($_GET['row']);
			
			if ($row != null) {
				if (isset($_GET['numSeats'])) {
					$seats = $_GET['numSeats'];
					
					if (is_numeric($seats)) {
						for ($i = 0; $i < $seats; $i++) {
							$row->addSeat();
						}
						
						$result = true;
					} else {
						$message = '<p>Antall seter er ikke et tall!</p>';
					}
				} else {
					$message = '<p>Antall seter er ikke satt!</p>';
				}
			} else {
				$message = '<p>Raden eksisterer ikke!</p>';
			}
		} else {
			$message = '<p>Raden er ikke satt!</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til å legge til en rad!</p>';
	}
} else {
	$message = '<p>Du må logge inn først!</p>';
}

if ($result) {
	echo json_encode(array('result' => $result));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>