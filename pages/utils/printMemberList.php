<?php
require_once 'session.php';
require_once 'handlers/eventhandler.php';

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*')) {
		if (isset($_GET['year']) &&
			is_numeric($_GET['year'])) {
			$eventList = EventHandler::getEventsByYear($year);
			
			if (!empty($eventList)) {
				$userList = EventHandler::getMembersAndParticipantsForEvents(array());
				
				foreach ($userList as $user) {
					echo $user->getDisplayName();
				}
			} else {
				echo 'Det var ingen arrangementer dette året.';
			}
		} else {
			echo 'Du må oppgi et gyldig år.';
		}
	} else {
		echo 'Du har ikke tillatelse til dette!';
	}
} else {
	echo 'Du er ikke logget inn.';
}
?>