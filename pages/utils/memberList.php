<?php
require_once 'session.php';
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
			$format = isset($_GET['format']) ? $_GET['format'] : 'html';

			if ($user->hasPermission('*') || 
				$user->hasPermission('event.memberlist')) {
				if (isset($_GET['year']) &&
					is_numeric($_GET['year'])) {
					if (isset($_GET['ageLimit']) &&
						is_numeric($_GET['ageLimit'])) {
						$eventList = EventHandler::getEventsByYear($_GET['year']);

						if (!empty($eventList)) {
							$userList = EventHandler::getMembersAndParticipantsByEvents($eventList, $_GET['ageLimit']);
								
							if (!empty($userList)) {
								if ($format == 'html') {
									outputText($userList);
								} else if ($format == 'csv') {
									// Make sure we don't have unsupported characters from error messages etc in here.
									ob_clean();
									outputCsv($userList);
									return;
								} else {
									echo '<p>Formatet er ikke støttet.</p>';
								}
							} else {
								echo '<p>Ingen brukere funnet.</p>';
							}
						} else {
							echo '<p>Det var ingen arrangementer dette året.</p>';
						}
					} else {
						echo '<p>Du må oppgi en aldersgrense.</p>';
					}
				} else {
					echo '<p>Du må oppgi et gyldig år.</p>';
				}
			} else {
				echo '<p>Du har ikke tillatelse til dette!</p>';
			}
		} else {
			echo '<p>Du er ikke logget inn.</p>';
		}
	echo '</body>';
echo '</html>';

/*
 * Returns the member list as HTML.
 */
function outputText(array $userList) {
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
}

/*
 * Returns the member list as CSV, which is compatible with Excel.
 */
function outputCsv(array $userList) {
	$fp = fopen('php://output', 'w');

	$rowList = array(array('Navn:', 'E-post:', 'Telefon:', 'Adresse:', 'Fødselsdato:', 'Alder:', 'Rolle:'));

	// Add each user to the row list.
	foreach ($userList as $userValue) {
		array_push($rowList, array($userValue->getFullName(), 
								   $userValue->getEmail(), 
								   $userValue->getPhoneAsString(),
								   $userValue->getAddress() . ', ' . $userValue->getPostalCode() . ' ' . $userValue->getCity(),
								   date('d.m.Y', $userValue->getBirthdate()),
								   $userValue->getAge() . ' år',
								   $userValue->isGroupMember() ? 'Crew' : 'Deltaker'));
	}

	// Fix UTF-8 charset in excel.
	fputs($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

	// Generate CSV from
	foreach ($rowList as $row) {
		fputcsv($fp, $row, ';');
	}

	// Tell the browser it's going to be a csv file.
	header('Content-Type: application/csv; charset=UTF-8');
	// Tell the browser we want to save it instead of displaying it.
	header('Content-Disposition: attachement; filename=' . 'Medlemmsliste ' .  $_GET['year'] . '.csv');
	fclose($fp);
}
?>