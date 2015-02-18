<?php
require_once 'session.php';
require_once 'handlers/rowhandler.php';
require_once 'handlers/seathandler.php';

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
					$numSeats = $_GET['numSeats'];
					
					if (is_numeric($numSeats)) {
						$seats = RowHandler::getSeats($row);
						
						if (count($seats)>=$numSeats) {
							$startIndex = count($seats) - 1;
							$endIndex = $startIndex - $numSeats;
							$safeToDelete = true;
							
							for ($i = $startIndex; $i > $endIndex; $i--) {
								if (SeatHandler::hasTicket($seats[$i])) {
									$safeToDelete = false;
								}
							}
							
							if ($safeToDelete) {
								for ($i = $startIndex; $i > $endIndex; $i--) {
									SeatHandler::removeSeat($seats[$i]);
								}
								
								$result = true;
							} else {
								$message = '<p>Noen sitter på et av setene du prøver å slette!</p>';
							}
						} else {
							$message = '<p>Det er færre seter i raden enn det du prøver å slette!</p>';
						}
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