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
require_once 'handlers/avatarhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	// Remove avatar if the user already have one.
	if ($user->hasAvatar()) {
		$user->getAvatar()->remove();
	}

	$temp = explode('.', $_FILES['file']['name']);
	$extension = strtolower(end($temp));
	$allowedExts = array('jpeg', 'jpg', 'png');
	
	if (($_FILES['file']['size'] < 7 * 1024 * 1024)) {
		if (in_array($extension, $allowedExts)) {
			if ($_FILES['file']['error'] == 0) {
				// Validate size
				$image = 0;

				if ($extension == 'png') {
					$image = imagecreatefrompng($_FILES['file']['tmp_name']);
				} else if ($extension == 'jpeg' || 
					       $extension == 'jpg') {
					$image = imagecreatefromjpeg($_FILES['file']['tmp_name']);
				}

				if (imagesx($image) >= Settings::avatar_minimum_width && imagesy($image) >= Settings::avatar_minimum_height) {
					$name = bin2hex(openssl_random_pseudo_bytes(16)) . $user->getUsername();
					$path = AvatarHandler::createAvatar($name . '.' . $extension, $user);
					move_uploaded_file($_FILES['file']['tmp_name'], $path);
					$result = true;
				} else {
					$message = '<p>Bildet er for smått! Det må være minimum ' . Settings::avatar_minimum_width . ' x ' . Settings::avatar_minimum_height . ' piksler stort.';
				}
			} else {
				$error = $_FILES['file']['error'];
				
				if ($error == 2 || 
					$error == 1) {
					$message = '<p>Det har skjedd en intern feil: Filen er for stor!</p>';
				} else {
					$message = '<p>Det har skjedd en intern feil da vi behandlet bildet. Vennligst gi oss feilkoden "' . urlencode($_FILES['file']['error']) . '"</p>';
				}
			}
		} else {
			$message = '<p>Ugyldig filtype.</p>';
		}
	} else {
		$message = '<p>Filen er for stor!</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn!</p>';
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>