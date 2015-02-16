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
		
		if (isset($_GET["row"])) {
			$row = RowHandler::getRow($_GET["row"]);
			
			if ($row != null) {
				if (isset($_GET["numSeats"])) {
					$numSeats = $_GET["numSeats"];
					
					if (is_numeric($numSeats)) {
						$seats = RowHandler::getSeats($row);
						
						if (count($seats)>=$numSeats) {
							$startIndex = count($seats) - 1;
							$endIndex = $startIndex - $numSeats;
							$safeToDelete = true;
							
							for ($i = $startIndex; $i > $endIndex; $i--) {
								if (SeatHandler::hasOwner($seats[$i])) {
									$safeToDelete = false;
								}
							}
							
							if ($safeToDelete) {
								for ($i = $startIndex; $i > $endIndex; $i--) {
									SeatHandler::deleteSeat($seats[$i]);
								}
								
								$result = true;
							} else {
								$message = 'Noen sitter på et av setene du prøver å slette!';
							}
						} else {
							$message = 'Det er færre seter i raden enn det du prøver å slette!';
						}
					} else {
						$message = 'Antall seter er ikke et tall!';
					}
				} else {
					$message = 'Antall seter er ikke satt!';
				}
			} else {
				$message = 'Raden eksisterer ikke!';
			}
		} else {
			$message = 'Raden er ikke satt!';
		}
	} else {
		$message = 'Du har ikke tillatelse til å legge til en rad!';
	}
} else {
	$message = 'Du må logge inn først!';
}

if ($result) {
	echo json_encode(array('result' => $result));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>