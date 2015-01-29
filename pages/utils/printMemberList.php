<?php
require_once 'session.php';
require_once 'handlers/eventhandler.php';

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	echo '<meta charset="UTF-8">';
	
	if ($user->hasPermission('*')) {
		if (isset($_GET['year']) &&
			is_numeric($_GET['year']) &&
			date('Y') >= $_GET['year']) {
			$eventList = EventHandler::getEventsByYear($_GET['year']);
			
			if (!empty($eventList)) {
				$userList = EventHandler::getMembersAndParticipantsForEvents($eventList);
				
				if (!empty($userList)) {
					echo '<p>Fant ' . count($userList) . ' brukere i databasen.</p>';
					
					echo '<table>';
						echo '<tr>';
							echo '<th>Navn:</th>';
							echo '<th>E-post:</th>';
							echo '<th>Telefon:</th>';
							echo '<th>Adresse:</th>';
							echo '<th>Fødselsdato:</th>';
						echo '</tr>';
					
						foreach ($userList as $value) {
							echo '<tr>';
								echo '<td>' . $value->getFullName() . '</td>';
								echo '<td>' . $value->getEmail() . '</th>';
								echo '<td>' . $value->getPhoneAsString() . '</th>';
								echo '<td>' . $value->getAddress() . ', ' . $value->getCity() . '</th>';
								echo '<td>' . date('d.m.Y', $value->getBirthdate()) . '</th>';
							echo '</tr>';
						}
					echo '</table>';
				} else {
					echo 'Ingen brukere funnet.';
				}
			} else {
				echo 'Det var ingen arrangementer dette året.';
			}
		} else {
			echo 'Du må oppgi et gyldig år, året må være omme før du kan hente ut medlemslister.';
		}
	} else {
		echo 'Du har ikke tillatelse til dette!';
	}
} else {
	echo 'Du er ikke logget inn.';
}
?>