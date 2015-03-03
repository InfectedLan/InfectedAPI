<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'utils/dateutils.php';

if (!Session::isAuthenticated()) {
	echo '<script src="../api/scripts/register.js"></script>';
	echo '<script src="../api/scripts/lookupCity.js"></script>';
	echo '<form class="register" method="post">';
		echo '<h2>Registrer</h2>';
		echo '<table>';
			echo '<tr>';
				echo '<td>Fornavn:</td>';
				echo '<td><input type="text" name="firstname" required autofocus></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Etternavn:</td>';
				echo '<td><input type="text" name="lastname" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Brukernavn:</td>';
				echo '<td><input type="text" name="username" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Passord:</td>';
				echo '<td><input type="password" name="password" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Gjenta passord:</td>';
				echo '<td><input type="password" name="confirmpassword" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>E-post:</td>';
				echo '<td><input type="email" name="email" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Gjenta e-post:</td>';
				echo '<td><input type="email" name="confirmemail" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Kjønn:</td>';
				echo '<td>';
					echo '<select name="gender">';
						echo '<option value="0">Mann</option>';
						echo '<option value="1">Kvinne</option>';
					echo '</select>';
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Fødselsdato:</td>';
				echo '<td>';
					echo '<select name="birthday">';
						for ($day = 1; $day < 32; $day++) {
							echo '<option value="' . $day . '">' . $day . '</option>';
						}
					echo '</select>';
					echo '<select name="birthmonth">';
						for ($month = 1; $month < 13; $month++) {
							echo '<option value="' . $month . '">' . DateUtils::getMonthFromInt($month) . '</option>';
						}
					echo '</select>';
					echo '<select name="birthyear">';
						for ($year = date('Y') - 100; $year < date('Y'); $year++) {
							if ($year == date('Y') - 18) {
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
				echo '<td><input type="tel" name="phone" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Gateadresse:</td>';
				echo '<td><input type="text" name="address" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Postnummer:</td>';
				echo '<td><input class="postalcode" type="number" name="postalcode" min="1" max="9999" required></td>';
				echo '<td><span class="city"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Kallenavn:</td>';
				echo '<td><input type="text" name="nickname"></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Foresatte\'s telefon:</td>';
				echo '<td><input type="text" name="emergencycontactphone"></td>';
				echo '<td>(Påkrevd hvis du er under 18)</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><input type="submit" value="Registrer deg"></td>';
			echo '</tr>';
		echo '</table>';
	echo '</form>';
} else {
	echo '<p>Du er logget inn, og kan derfor ikke registrere deg.';
}
?>