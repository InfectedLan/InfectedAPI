<?php
require_once 'session.php';
require_once 'utils.php';

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	echo '<h3>Endre bruker</h3>';
	echo '<script src="../api/scripts/edit-profile.js"></script>';
	echo '<script src="../api/scripts/lookupCity.js"></script>';
	echo '<form class="edit-profile" method="post">';
		echo '<table>';
			echo '<tr>';
				echo '<td>Fornavn:</td>';
				echo '<td><input type="text" name="firstname" value="' . $user->getFirstname() . '"  required autofocus></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Etternavn:</td>';
				echo '<td><input type="text" name="lastname" value="' . $user->getLastname() . '"  required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Kjønn:</td>';
				echo '<td>';
					echo '<select name="gender">';
						$gender = $user->getGender();
						
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
					$birthdate = $user->getBirthdate();
				
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
				echo '<td><input type="tel" name="phone" value="' .  str_replace(' ', '', $user->getPhone()) . '" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Gateadresse:</td>';
				echo '<td><input type="text" name="address" value="' . $user->getAddress() . '"  required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Postnummer:</td>';
				echo '<td><input class="postalcode" type="number" name="postalcode" min="1" max="9999" value="' . $user->getPostalCode() . '"  required></td>';
				echo '<td><span class="city">' . $user->getCity() . '</span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Nickname:</td>';
				echo '<td><input type="text" name="nickname" value="' . $user->getNickname() . '"></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><input type="submit" value="Lagre"></td>';
			echo '</tr>';
		echo '</table>';
	echo '</form>';
	echo '<a href="index.php?page=edit-password">Endre passord</a>';
	echo '<a href="index.php?page=edit-avatar">Endre/Last opp profilbilde</a>';
} else {
	echo '<p>Du er ikke logget inn!</p>';
}
?>