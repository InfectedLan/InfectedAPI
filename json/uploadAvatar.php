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
	if(($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png")) {
		if (($_FILES["file"]["size"] < 7000000)) {
			if(in_array($extension, $allowedExts)) {
				if ($_FILES["file"]["error"] == 0) {
					$name = md5(time() . "This is a random seed. Nothing to see here. 31.08.2014") . $user->getUsername();
					$path = AvatarHandler::createAvatar($name . '.' . $extension, $user);
					move_uploaded_file($_FILES["file"]["tmp_name"], $path);
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
		$message = "Filen har ikke riktig MIME-format";
	} 
} else {
	$message = "Du er allerede logget inn!";
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>