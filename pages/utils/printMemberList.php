<?php
require_once 'session.php';
require_once 'settings.php';
require_once 'handlers/eventhandler.php';

echo '<!DOCTYPE html>';
echo '<html>';
	echo '<head>';
		echo '<title>Infected Medlemsliste</title>';
		echo '<meta charset="UTF-8">';
	echo '</head>';
	echo '<body>';
		if (Session::isAuthenticated()) {
			$user = Session::getCurrentUser();
			
			echo '<meta charset="UTF-8">';
			
			if ($user->hasPermission('*')) {
				if (isset($_GET['year']) &&
					isset($_GET['ageLimit']) &&
					is_numeric($_GET['year']) &&
					is_numeric($_GET['ageLimit'])) {
					$eventList = EventHandler::getEventsByYear($_GET['year']);
					
					if (!empty($eventList)) {
						$userList = EventHandler::getMembersAndParticipantsByEvents($eventList, $_GET['ageLimit']);
						
						if (!empty($userList)) {
							echo '<p>Fant ' . count($userList) . ' brukere i databasen.</p>';
							
							if (date('Y') <= $_GET['year']) {
								echo '<p>Året er ikke omme enda, det kan hende du ikke får den komplette medlemslisten om du henter den ut nå.</p>';
							}
								
							echo '<table>';
								echo '<tr>';
									echo '<th>Navn:</th>';
									echo '<th>E-post:</th>';
									echo '<th>Telefon:</th>';
									echo '<th>Adresse:</th>';
									echo '<th>Fødselsdato:</th>';
									echo '<th>Alder:</th>';
									echo '<th>Rolle:</th>';
								echo '</tr>';
							
								foreach ($userList as $value) {
									echo '<tr>';
										echo '<td>' . $value->getFullName() . '</td>';
										echo '<td>' . $value->getEmail() . '</td>';
										echo '<td>' . $value->getPhoneAsString() . '</td>';
										echo '<td>' . $value->getAddress() . ', ' . $value->getPostalCode() . ' ' . $value->getCity() . '</td>';
										echo '<td>' . date('d.m.Y', $value->getBirthdate()) . '</td>';
										echo '<td>' . $value->getAge() . ' år</td>';
										echo '<td>' . ($value->isGroupMember() ? 'Crew' : 'Deltaker') . '</td>';
									echo '</tr>';
								}
							echo '</table>';
						} else {
							echo '<p>Ingen brukere funnet.</p>';
						}
					} else {
						echo '<p>Det var ingen arrangementer dette året.</p>';
					}
				} else {
					echo '<p>Du må oppgi et gyldig år.</p>';
				}
			} else {
				echo '<p>u har ikke tillatelse til dette!</p>';
			}
		} else {
			echo '<p>Du er ikke logget inn.</p>';
		}
	echo '</body>';
echo '</html>';
?>