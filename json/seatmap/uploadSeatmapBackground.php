<?php
include 'database.php';
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'localization.php';
require_once 'handlers/seatmaphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('admin.seatmap')) {
		if (isset($_POST['seatmapId'])) {
			$seatmap = SeatmapHandler::getSeatmap($_POST['seatmapId']);

			if ($seatmap != null) {
				//TODO cleanup if the upload 'overwrites' another image
				//Validate image
				$allowedExts = ['jpeg', 'jpg', 'png'];
				$temp = explode('.', $_FILES['bgImageFile']['name']);
				$extension = strtolower(end($temp));

				if (($_FILES['bgImageFile']['type'] == 'image/jpeg') ||
					($_FILES['bgImageFile']['type'] == 'image/jpg') ||
					($_FILES['bgImageFile']['type'] == 'image/x-png') ||
					($_FILES['bgImageFile']['type'] == 'image/png')) {

					if (($_FILES['bgImageFile']['size'] < 7000000)) {
						if (in_array($extension, $allowedExts)) {
							if ($_FILES['bgImageFile']['error'] == 0) {
								$name = md5(time()); //Randomly generate filename
								move_uploaded_file($_FILES['bgImageFile']['tmp_name'], '../../content/seatmapBackground/' . $name . '.' . $extension);
								SeatmapHandler::setBackground($seatmap, $name . '.' . $extension);

								$result = true;
							} else {
								$message = Localization::getLocale('an_error_occurred_while_uploading_your_image');
							}
						}
					} else {
						$message = Localization::getLocale('the_file_size_is_too_large');
					}
				} else {
					$message = Localization::getLocale('invalid_file_format');
				}
			} else {
				$message = Localization::getLocale('this_seatmap_does_not_exist');
			}
		} else {
			$message = Localization::getLocale('no_seatmap_specified');
		}
	} else {
		$message = Localization::getLocale('you_do_not_have_permission_to_do_that');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}


header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result), JSON_PRETTY_PRINT);
} else {
	echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}
Database::cleanup();
?>
