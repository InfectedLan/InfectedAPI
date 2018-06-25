<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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
require_once 'handlers/userhandler.php';
require_once 'handlers/emergencycontacthandler.php';
require_once 'utils/dateutils.php';

if (Session::isAuthenticated()) {
	$id = isset($_GET['id']) ? $_GET['id'] : Session::getCurrentUser()->getId();
	$user = Session::getCurrentUser();
	$editUser = UserHandler::getUser($id);

	if ($editUser != null) {
		if ($user->hasPermission('user.edit') ||
			$user->equals($editUser)) {
			echo '<script src="../api/scripts/edit-profile.js"></script>';
			echo '<script src="../api/scripts/lookupCity.js"></script>';

			echo '<h3>' . Localization::getLocale('change_user') . '</h3>';

			echo '<table>';
				echo '<form class="edit-profile" method="post">';
					echo '<input type="hidden" name="id" value="' . $editUser->getId() . '">';
					echo '<tr>';
						echo '<td>' . Localization::getLocale('firstname') . ':</td>';
						echo '<td><input type="text" name="firstname" value="' . $editUser->getFirstname() . '" required autofocus></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>' . Localization::getLocale('lastname') . ':</td>';
						echo '<td><input type="text" name="lastname" value="' . $editUser->getLastname() . '" required></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>' . Localization::getLocale('username') . ':</td>';
						echo '<td><input type="text" name="username" value="' . $editUser->getUsername() . '" required></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>' . Localization::getLocale('email') . ':</td>';
						echo '<td><input type="email" name="email" value="' . $editUser->getEmail() . '" required></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>' . Localization::getLocale('repeat_email') . ':</td>';
						echo '<td><input type="email" name="confirmemail" required></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>' . Localization::getLocale('gender') . ':</td>';
						echo '<td>';
							echo '<select name="gender">';
								echo '<option value="0"' . ($editUser->getGender() ? ' selected' : null) . '>' . Localization::getLocale($editUser->getAge() < 18 ? 'boy' : 'male') . '</option>';
								echo '<option value="1"' . (!$editUser->getGender() ? ' selected' : null) . '>' . Localization::getLocale($editUser->getAge() < 18 ? 'girl' : 'female') . '</option>';
							echo '</select>';
						echo '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>' . Localization::getLocale('birthdate') . ':</td>';
						echo '<td>';
							$birthdate = $editUser->getBirthdate();

							echo '<select name="birthday">';
								for ($day = 1; $day <= 31; $day++) {
									if ($day == date('d', $birthdate)) {
										echo '<option value="' . $day . '" selected>' . $day . '</option>';
									} else {
										echo '<option value="' . $day . '">' . $day . '</option>';
									}
								}
							echo '</select>';
							echo '<select name="birthmonth">';
								for ($month = 1; $month <= 12; $month++) {
									if ($month == date('m', $birthdate)) {
										echo '<option value="' . $month . '" selected>' . DateUtils::getMonthFromInt($month) . '</option>';
									} else {
										echo '<option value="' . $month . '">' . DateUtils::getMonthFromInt($month) . '</option>';
									}
								}
							echo '</select>';
							echo '<select name="birthyear">';
								for ($year = date('Y') - 100; $year <= date('Y'); $year++) {
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
						echo '<td>' . Localization::getLocale('phone') . ':</td>';
						echo '<td>(+47) <input type="tel" name="phone" value="' . $editUser->getPhone() . '" required></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>' . Localization::getLocale('street_address') . ':</td>';
						echo '<td><input type="text" name="address" value="' . $editUser->getAddress() . '" required></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>' . Localization::getLocale('zip_code') . ':</td>';
						echo '<td><input class="postalcode" type="number" name="postalcode" min="1" max="10000" value="' . $editUser->getPostalCode() . '" required></td>';
						echo '<td><span class="city">' . $editUser->getCity() . '</span></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>' . Localization::getLocale('nickname') . ':</td>';
						echo '<td><input type="text" name="nickname" value="' . $editUser->getNickname() . '"></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>' . Localization::getLocale('guardians_phone') . ':</td>';
							if ($editUser->hasEmergencyContact()) {
								$emergencyContactPhone = $editUser->getEmergencyContact()->getPhone();

								echo '<td>(+47) <input name="emergencycontactphone" type="tel" value="' . $emergencyContactPhone . '"></td>';
							} else {
								echo '<td>(+47) <input name="emergencycontactphone" type="tel"></td>';
							}
						echo '<td><i>(' . Localization::getLocale('required_if_you_are_under_the_age_of_value', 18) . ')</i></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td><input type="submit" value="' . Localization::getLocale('save') . '"></td>';
					echo '</tr>';
				echo '</form>';

				if ($user->equals($editUser)) {
					echo '<tr>';
						echo '<td></td>';
						echo '<td><a href="index.php?page=edit-avatar">' . Localization::getLocale('change_upload_profile_photo') . '</a></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td></td>';
						echo '<td><a href="index.php?page=edit-password">' . Localization::getLocale('change_password') . '</a></td>';
					echo '</tr>';
				}
			echo '</table>';
		} else {
			echo Localization::getLocale('you_do_not_have_permission_to_do_that');
		}
	} else {
		echo Localization::getLocale('the_user_does_not_exist');
	}
} else {
	echo Localization::getLocale('you_are_not_logged_in');
}
?>
