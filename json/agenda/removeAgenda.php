<?php
require_once 'session.php';
require_once 'handlers/agendahandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$agenda = AgendaHandler::getAgenda($_GET['id']);
				
			if ($agenda != null) {
				AgendaHandler::removeAgenda($agenda);
				$result = true;
			} else {
				$message = 'Agendaen du prøvde å slette, finnes ikke.';
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