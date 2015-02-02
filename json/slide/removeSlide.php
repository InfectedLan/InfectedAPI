<?php
require_once 'session.php';
require_once 'handlers/slidehandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$slide = SlideHandler::getSlide($_GET['id']);
				
			if ($slide != null) {
				SlideHandler::removeSlide($slide);
				$result = true;
			} else {
				$message = 'Sliden du prøvde å slette, finnes ikke.';
			}
		} else {
			$message = 'Ikke noen agenda spesifisert.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>