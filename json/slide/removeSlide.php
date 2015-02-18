<?php
require_once 'session.php';
require_once 'handlers/slidehandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('event.screen')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$slide = SlideHandler::getSlide($_GET['id']);
				
			if ($slide != null) {
				SlideHandler::removeSlide($slide);
				$result = true;
			} else {
				$message = '<p>Sliden du prøvde å slette, finnes ikke.</p>';
			}
		} else {
			$message = '<p>Ikke noen agenda spesifisert.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>