<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result), JSON_PRETTY_PRINT);
} else {
	echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
}
?>