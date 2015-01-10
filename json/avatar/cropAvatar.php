<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/avatarhandler.php';

$result = false;
$message = null;

function str_replace_last( $search , $replace , $str ) {
    if( ( $pos = strrpos( $str , $search ) ) !== false ) {
        $search_length  = strlen( $search );
        $str    = substr_replace( $str , $replace , $pos , $search_length );
    }
    return $str;
}

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	$avatar = $user->getAvatar();
	if($user->hasAvatar()) {
		// Sjekk om avataren skal croppes
		$state = $avatar->getState();
		if($state == 0) {
			if(isset($_GET["x"])&&isset($_GET["y"])&&isset($_GET["w"])&&isset($_GET["h"])) {
				$x = max($_GET["x"], 0);
				$y = max($_GET["y"], 0);
				$w = $_GET["w"];
				$h = $_GET["h"];

				//Get extension
				$temp = explode(".", $avatar->getTemp());
				$extension = strtolower(end($temp));

				if($extension=="png" || $extension=="jpeg" || $extension=="jpg") {
					//Load the image
					$image = 0;

					if($extension=="png")
					{
						$image = imagecreatefrompng(Settings::api_path . $avatar->getTemp());
					}
					elseif($extension=="jpeg"||$extension=="jpg")
					{
						$image = imagecreatefromjpeg(Settings::api_path . $avatar->getTemp())	;
					}

					if($image != 0) {
						//Get scale factor. Image scaler is 800 px wide.
						$scalefactor = imagesx($image)/800;

						$cropWidth = ceil($w*$scalefactor);
						$cropHeight = ceil($h*$scalefactor);

						if($cropWidth >= Settings::avatar_minimum_width && $cropHeight >= Settings::avatar_minimum_height) {
							//Render to tumbnail
							$target = imagecreatetruecolor(Settings::avatar_thumb_w, Settings::avatar_thumb_h);
							imagecopyresized($target, $image, 0, 0, $x*$scalefactor, $y*$scalefactor, Settings::avatar_thumb_w, Settings::avatar_thumb_h, $w*$scalefactor, $h*$scalefactor);
							$imagePath = Settings::api_path . $avatar->getThumbnail();
							imagejpeg($target, str_replace_last($extension, "jpg", $imagePath), Settings::thumbnail_compression_rate);

							//Render to sd
							$target = imagecreatetruecolor(Settings::avatar_sd_w, Settings::avatar_sd_h);
							imagecopyresized($target, $image, 0, 0, $x*$scalefactor, $y*$scalefactor, Settings::avatar_sd_w, Settings::avatar_sd_h, $w*$scalefactor, $h*$scalefactor);
							$imagePath = Settings::api_path . $avatar->getSd();
							imagejpeg($target, str_replace_last($extension, "jpg", $imagePath), Settings::sd_compression_rate);

							//Render to hq
							$target = imagecreatetruecolor(Settings::avatar_hd_w, Settings::avatar_hd_h);
							imagecopyresized($target, $image, 0, 0, $x*$scalefactor, $y*$scalefactor, Settings::avatar_hd_w, Settings::avatar_hd_h, $w*$scalefactor, $h*$scalefactor);
							$imagePath = Settings::api_path . $avatar->getHd();
							imagejpeg($target, str_replace_last($extension, "jpg", $imagePath), Settings::hd_compression_rate);

							unlink(Settings::api_path . $avatar->getTemp());

							$avatar->setFileName(str_replace_last($extension, "jpg", $avatar->getFileName()));
							$avatar->setState(1);
							$result = true;
							$message = "Avataren har blitt skalert!";
						} else {
							$message = "Du har valgt et for lite omeråde! Dette er ikke lov, ettersom det kan medføre et pikselert bilde.";
						}
					} else {
						$message = "Bildet ble ikke funnet!";
					}
				} else {
					$message = "Avataren din har et ugyldig filformat!";
				}
			} else {
				$message = "Felt mangler!";
			}
		} else {
			$message = "Du har ingen avatar som ikke har blitt beskjært!";
		}
	} else {
		$message = "Du har ingen avatar!";
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));

?>