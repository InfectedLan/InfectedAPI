<?php
require_once 'session.php';
require_once 'utils.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/emergencycontacthandler.php';

$id = isset($_GET['id']) ? $_GET['id'] : Session::getCurrentUser()->getId();

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	$editUser = UserHandler::getUser($id);
	
	if ($editUser != null) {
		if ($user->hasPermission('*') ||
			$user->getId() == $editUser->getId()) {
			echo '<script src="../api/scripts/edit-profile.js"></script>';
			echo '<script src="../api/scripts/lookupCity.js"></script>';
			
			echo '<h3>Endre bruker</h3>';
			
			echo '<table>';
				echo '<form class="edit-profile" method="post">';
					echo '<input type="hidden" name="id" value="' . $editUser->getId() . '">';
					echo '<tr>';
						echo '<td>Fornavn:</td>';
						echo '<td><input type="text" name="firstname" value="' . $editUser->getFirstname() . '" required autofocus></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Etternavn:</td>';
						echo '<td><input type="text" name="lastname" value="' . $editUser->getLastname() . '" required></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Brukernavn:</td>';
						echo '<td><input type="text" name="username" value="' . $editUser->getUsername() . '" required></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>E-post:</td>';
						echo '<td><input type="email" name="email" value="' . $editUser->getEmail() . '" required></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Kjønn:</td>';
						echo '<td>';
							echo '<select name="gender">';
								$gender = $editUser->getGender();
								
								if ($gender == 0) {
									echo '<option value="0" selected>Mann</option>';
									echo '<option value="1">Kvinne</option>';
								} else if ($gender == 1) {
									echo '<option value="0">Mann</option>';
									echo '<option value="1" selected>Kvinne</option>';
								}
							echo '</select>';
						echo '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Fødselsdato:</td>';
						echo '<td>';
							$birthdate = $editUser->getBirthdate();
						
							echo '<select name="birthday">';
								for ($day = 1; $day < 32; $day++) {
									if ($day == date('d', $birthdate)) {
										echo '<option value="' . $day . '" selected>' . $day . '</option>';
									} else {
										echo '<option value="' . $day . '">' . $day . '</option>';
									}
								}
							echo '</select>';
							echo '<select name="birthmonth">';					
								for ($month = 1; $month < 13; $month++) {
									if ($month == date('m', $birthdate)) {
										echo '<option value="' . $month . '" selected>' . Utils::getMonthFromInt($month) . '</option>';
									} else {
										echo '<option value="' . $month . '">' . Utils::getMonthFromInt($month) . '</option>';
									}
								}
							echo '</select>';
							echo '<select name="birthyear">';
								for ($year = date('Y') - 100; $year < date('Y'); $year++) {
									if ($year == date('Y', $birthdate)) {
										echo '<option value="' . $year . '" selected>' . $year . '</option>';
									} else {
										echo '<option value="' . $year . '">' . $year . '</option>';
									}
								}
							echo '</select>';
						echo '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Telefon:</td>';
						echo '<td><input type="tel" name="phone" value="' . $editUser->getPhone() . '" required></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Gateadresse:</td>';
						echo '<td><input type="text" name="address" value="' . $editUser->getAddress() . '" required></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Postnummer:</td>';
						echo '<td><input class="postalcode" type="number" name="postalcode" min="1" max="9999" value="' . $editUser->getPostalCode() . '" required></td>';
						echo '<td><span class="city">' . $editUser->getCity() . '</span></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Nickname:</td>';
						echo '<td><input type="text" name="nickname" value="' . $editUser->getNickname() . '"></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>Foresatte\'s telefon:</td>';
							if (EmergencyContactHandler::hasEmergencyContact($editUser)) {
								$emergencycontactphone = EmergencyContactHandler::getEmergencyContactForUser($editUser)->getPhone();
							
								echo '<td><input name="emergencycontactphone" type="tel" value="' . $emergencycontactphone . '"></td>';
							} else {
								echo '<td><input name="emergencycontactphone" type="tel"></td>';
							}
						echo '<td><i>(Påkrevd hvis du er under 18)</i></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td><input type="submit" value="Lagre"></td>';
					echo '</tr>';
				echo '</form>';
				echo '<tr>';
					echo '<td></td>';
					echo '<td><a href="index.php?page=edit-avatar">Endre/Last opp profilbilde</a></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td></td>';
					echo '<td><a href="index.php?page=edit-password">Endre passord</a></td>';
				echo '</tr>';
			echo '</table>';
		} else {
			echo '<p>Du har ikke rettigehter til dette.</p>';
		}
	} else {
		echo '<p>Brukeren du ser etter finnes ikke.</p>';
	}
} else {
	echo '<p>Du er ikke logget inn!</p>';
}
?>