<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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
require_once 'database.php';
require_once 'localization.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasAvatar()) {
		$avatar = $user->getAvatar();

		if ($avatar->getState() == 0) {
			if (isset($_GET['x']) &&
				isset($_GET['y']) &&
				isset($_GET['w']) &&
				isset($_GET['h'])) {
				$x = max($_GET['x'], 0);
				$y = max($_GET['y'], 0);
				$w = $_GET['w'];
				$h = $_GET['h'];

				//Get extension
				$temp = explode('.', $avatar->getTemp());
				$extension = strtolower(end($temp));

				if ($extension == 'png' ||
					$extension == 'jpeg' ||
					$extension == 'jpg') {
					//Load the image
					$image = 0;

					if ($extension == 'png') {
						$image = imagecreatefrompng(Settings::getValue("dynamic_path") . $avatar->getTemp());
					} else if ($extension == 'jpeg' ||
							   $extension == 'jpg') {
						$image = imagecreatefromjpeg(Settings::getValue("dynamic_path") . $avatar->getTemp())	;
					}

					if ($image != 0) {
						//Get scale factor. Image scaler is 800 px wide.
						$scalefactor = imagesx($image) / 800;

						$cropWidth = ceil($w * $scalefactor);
						$cropHeight = ceil($h * $scalefactor);

						if ($cropWidth >= Settings::getValue("avatar_minimum_width") &&
							$cropHeight >= Settings::getValue("avatar_minimum_height")) {
							// Render to tumbnail
							$target = imagecreatetruecolor(Settings::getValue("avatar_thumb_w"), Settings::getValue("avatar_thumb_h"));
							imagecopyresized($target, $image, 0, 0, $x*$scalefactor, $y*$scalefactor, Settings::getValue("avatar_thumb_w"), Settings::getValue("avatar_thumb_h"), $w*$scalefactor, $h*$scalefactor);
							$imagePath = Settings::getValue("dynamic_path") . $avatar->getThumbnail();
							imagejpeg($target, str_replace_last($extension, 'jpg', $imagePath), Settings::getValue("thumbnail_compression_rate"));

							// Render to sd
							$target = imagecreatetruecolor(Settings::getValue("avatar_sd_w"), Settings::getValue("avatar_sd_h"));
							imagecopyresized($target, $image, 0, 0, $x*$scalefactor, $y*$scalefactor, Settings::getValue("avatar_sd_w"), Settings::getValue("avatar_sd_h"), $w*$scalefactor, $h*$scalefactor);
							$imagePath = Settings::getValue("dynamic_path") . $avatar->getSd();
							imagejpeg($target, str_replace_last($extension, 'jpg', $imagePath), Settings::getValue("sd_compression_rate"));

							// Render to hq
							$target = imagecreatetruecolor(Settings::getValue("avatar_hd_w"), Settings::getValue("avatar_hd_h"));
							imagecopyresized($target, $image, 0, 0, $x*$scalefactor, $y*$scalefactor, Settings::getValue("avatar_hd_w"), Settings::getValue("avatar_hd_h"), $w*$scalefactor, $h*$scalefactor);
							$imagePath = Settings::getValue("dynamic_path") . $avatar->getHd();
							imagejpeg($target, str_replace_last($extension, 'jpg', $imagePath), Settings::getValue("hd_compression_rate"));

							unlink(Settings::getValue("dynamic_path") . $avatar->getTemp());

							$avatar->setFileName(str_replace_last($extension, 'jpg', $avatar->getFileName()));
							$avatar->setState(1);
							$result = true;
							$message = Localization::getLocale('the_image_was_scaled');
						} else {
							$message = Localization::getLocale('you_must_choose_a_larger_section_of_the_image');
						}
					} else {
						$message = Localization::getLocale('the_image_was_not_found');
					}
				} else {
					$message = Localization::getLocale('invalid_file_format');
				}
			}
		} else {
			$message = Localization::getLocale('you_have_no_avatar_that_has_not_already_been_cropped');
		}
	} else {
		$message = Localization::getLocale('you_have_no_avatar');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

function str_replace_last($search, $replace, $str) {
	if (($pos = strrpos($str, $search)) !== false) {
		$search_length = strlen( $search );
		$str = substr_replace($str, $replace, $pos, $search_length);
	}

	return $str;
}

header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();
?>
