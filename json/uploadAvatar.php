<?php
require_once 'session.php';
require_once 'handlers/avatarhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	$avatar = $user->getAvatar();

	if(isset($avatar))
	{
		AvatarHandler::deleteAvatar($avatar);
	}

	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = strtolower(end($temp));
	$allowedExts = array("jpeg", "jpg", "png");
	if(($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png")) {
		if (($_FILES["file"]["size"] < 7000000)) {
			if(in_array($extension, $allowedExts)) {
				if ($_FILES["file"]["error"] == 0) {
					//Validate size
					$image = 0;
					if($extension=="png")
					{
						$image = imagecreatefrompng($_FILES["file"]["tmp_name"]);
					}
					elseif($extension=="jpeg"||$extension=="jpg")
					{
						$image = imagecreatefromjpeg($_FILES["file"]["tmp_name"]);
					}

					if(imagesx($image) >= Settings::avatar_minimum_width && imagesy($image) >= Settings::avatar_minimum_height) {
						$name = bin2hex(openssl_random_pseudo_bytes(16)) . $user->getUsername();
						$path = AvatarHandler::createAvatar($name . '.' . $extension, $user);
						move_uploaded_file($_FILES["file"]["tmp_name"], $path);
						$result = true;
					} else {
						$message = "Bildet er for smått! Det må være minimum " . Settings::avatar_minimum_width . ' x ' . Settings::avatar_minimum_height . ' piksler stort.';
					}
				} else {
					$message = urlencode($_FILES["file"]["error"]);
				}
			} else {
				$message = "Ugyldig filtype";
			}
		} else {
			$message = "Filen er for stor!";
		}
	} else {
		$message = "Filen har ikke riktig MIME-format(" . $_FILES["file"]["type"] . ")";
	} 
} else {
	$message = "Du er allerede logget inn!";
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>