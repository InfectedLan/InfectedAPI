<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'localization.php';
require_once 'utils/dateutils.php';

if (!Session::isAuthenticated()) {
	echo '<script src="../api/scripts/register.js"></script>';
	echo '<script src="../api/scripts/lookupCity.js"></script>';
	echo '<form class="register" method="post">';
		echo '<h2>Registrer</h2>';
		echo '<table>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('firstname') . ':</td>';
				echo '<td><input type="text" name="firstname" required autofocus></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('lastname') . ':</td>';
				echo '<td><input type="text" name="lastname" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('username') . ':</td>';
				echo '<td><input type="text" name="username" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('password') . ':</td>';
				echo '<td><input type="password" name="password" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('repeat_password') . ':</td>';
				echo '<td><input type="password" name="confirmpassword" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('email') . ':</td>';
				echo '<td><input type="email" name="email" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('repeat_email') . ':</td>';
				echo '<td><input type="email" name="confirmemail" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('gender') . ':</td>';
				echo '<td>';
					echo '<select name="gender">';
						echo '<option value="0">' . Localization::getLocale('male') . '</option>';
						echo '<option value="1">' . Localization::getLocale('female') . '</option>';
					echo '</select>';
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('birthdate') . ':</td>';
				echo '<td>';
					echo '<select name="birthday">';
						for ($day = 1; $day <= 31; $day++) {
							echo '<option value="' . $day . '">' . $day . '</option>';
						}
					echo '</select>';
					echo '<select name="birthmonth">';
						for ($month = 1; $month <= 12; $month++) {
							echo '<option value="' . $month . '">' . DateUtils::getMonthFromInt($month) . '</option>';
						}
					echo '</select>';
					echo '<select name="birthyear">';
						for ($year = date('Y') - 100; $year <= date('Y'); $year++) {
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
				echo '<td>' . Localization::getLocale('phone') . ':</td>';
				echo '<td>(+47) <input type="tel" name="phone" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('street_address') . ':</td>';
				echo '<td><input type="text" name="address" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('zip_code') . ':</td>';
				echo '<td><input class="postalcode" type="number" name="postalcode" min="1" max="9999" required></td>';
				echo '<td><span class="city"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('nickname') . ':</td>';
				echo '<td><input type="text" name="nickname"></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>' . Localization::getLocale('guardians_phone') . ':</td>';
				echo '<td>(+47) <input type="text" name="emergencycontactphone"></td>';
				echo '<td><i>(' . Localization::getLocale('required_if_you_are_under_the_age_of_value', 18) . ')</i></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><input type="submit" value="' . Localization::getLocale('sign_up') . '"></td>';
			echo '</tr>';
		echo '</table>';
	echo '</form>';
} else {
	echo Localization::getLocale('you_are_already_logged_in_and_therefore_you_cannot_register_again');
}
?>
