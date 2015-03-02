/*
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

<?php
require_once 'session.php';
require_once 'handlers/seatmaphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.seatmap')) {
		if (isset($_POST['seatmapId'])) {
			$seatmap = SeatmapHandler::getSeatmap($_POST['seatmapId']);
			
			if ($seatmap != null) {
				//TODO cleanup if the upload 'overwrites' another image
				//Validate image
				$allowedExts = array('jpeg', 'jpg', 'png');
				$temp = explode('.', $_FILES['bgImageFile']['name']);
				$extension = strtolower(end($temp));

				if (($_FILES['bgImageFile']['type'] == 'image/jpeg') || 
				    ($_FILES['bgImageFile']['type'] == 'image/jpg') || 
				    ($_FILES['bgImageFile']['type'] == 'image/x-png') || 
				    ($_FILES['bgImageFile']['type'] == 'image/png')) {
					
					if (($_FILES['bgImageFile']['size'] < 7000000)) {
						if (in_array($extension, $allowedExts)) {
							if ($_FILES['bgImageFile']['error'] == 0) {
								$name = md5(time() . 'yoloswag'); // TODO: Refactor this shitty, messy, and terible petterroea code.
								move_uploaded_file($_FILES['bgImageFile']['tmp_name'], '../content/seatmapBackground/' . $name . '.' . $extension);

								SeatmapHandler::setBackground($seatmap, $name . '.' . $extension);

								$result = true;
							} else {
								$message = 'Det skjedde en feil under opplastingen av bildet!</p>';
							}
						} else {
							$message = 'Feil filformat!</p>';
						}
					} else {
						$message = 'Bildet er for stort!</p>';
					}
				} else {
					$message = 'Filformatet er ikke riktig!</p>';
				}
			} else {
				$message = 'Seatmappet finnes ikke!</p>';
			}
		} else {
			$message = 'SeatmapId er ikke satt!</p>';
		}
	} else {
		$message = 'Du har ikke tillatelse til å legge til en rad!</p>';
	}
} else {
	$message = 'Du er ikke logget inn.</p>';
}

if ($result) {
	echo json_encode(array('result' => $result));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>