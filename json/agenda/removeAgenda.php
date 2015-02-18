<?php
require_once 'session.php';
require_once 'handlers/agendahandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('event.agenda')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$agenda = AgendaHandler::getAgenda($_GET['id']);
				
			if ($agenda != null) {
				AgendaHandler::removeAgenda($agenda);
				$result = true;
			} else {
				$message = '<p>Agendaen du prøvde å slette, finnes ikke.</p>';
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