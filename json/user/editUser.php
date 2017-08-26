<?php
include 'database.php';
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
require_once 'handlers/userhandler.php';
require_once 'handlers/emergencycontacthandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_POST['id']) &&
		is_numeric($_POST['id'])) {
		$editUser = UserHandler::getUser($_POST['id']);

		if ($editUser != null) {
			if ($user->hasPermission('user.edit') ||
				$user->equals($editUser)) {
				if (isset($_POST['firstname']) &&
					isset($_POST['lastname']) &&
					isset($_POST['username']) &&
					isset($_POST['email']) &&
					isset($_POST['confirmemail']) &&
					isset($_POST['gender']) &&
					isset($_POST['birthday']) &&
					isset($_POST['birthmonth']) &&
					isset($_POST['birthyear']) &&
					isset($_POST['phone']) &&
					isset($_POST['address']) &&
					isset($_POST['postalcode']) &&
					!empty($_POST['firstname']) &&
					!empty($_POST['lastname']) &&
					!empty($_POST['username']) &&
					!empty($_POST['email']) &&
					!empty($_POST['confirmemail']) &&
					is_numeric($_POST['gender']) &&
					is_numeric($_POST['birthday']) &&
					is_numeric($_POST['birthmonth']) &&
					is_numeric($_POST['birthyear']) &&
					is_numeric($_POST['phone']) &&
					!empty($_POST['address']) &&
					is_numeric($_POST['postalcode'])) {
					$firstname = ucfirst($_POST['firstname']);
					$lastname = ucfirst($_POST['lastname']);
					$username = $_POST['username'];
					$email = $_POST['email'];
					$confirmEmail = $_POST['confirmemail'];
					$gender = $_POST['gender'];
					$birthdate = $_POST['birthyear'] . '-' . $_POST['birthmonth'] . '-' . $_POST['birthday'];
					$phone = str_replace('+47', '', str_replace(' ', '', $_POST['phone']));
					$address = ucfirst($_POST['address']);
					$postalcode = $_POST['postalcode'];
					$nickname = !empty($_POST['nickname']) ? $_POST['nickname'] : $editUser->getUsername();
					$emergencyContactPhone = isset($_POST['emergencycontactphone']) ? str_replace('+47', '', str_replace(' ', '', $_POST['emergencycontactphone'])) : 0;

					if ($username != $editUser->getUsername() && UserHandler::hasUser($username)) {
						$message = Localization::getLocale('the_username_is_already_in_use_by_someone_else');
					} else if ($email != $editUser->getEmail() && UserHandler::hasUser($email)) {
						$message = Localization::getLocale('the_email_address_is_already_in_use_by_someone_else');
					} else if ($phone != $editUser->getPhone() && UserHandler::hasUser($phone)) {
						$message = Localization::getLocale('the_phone_number_is_already_in_use_by_someone_else');
					} else if (empty($firstname) || strlen($firstname) > 32) {
						$message = Localization::getLocale('you_must_enter_a_first_name');
					} else if (empty($lastname) || strlen($lastname) > 32) {
						$message = Localization::getLocale('you_must_enter_a_last_name');
					} else if (!preg_match('/^[a-zæøåA-ZÆØÅ0-9_-]{2,16}$/', $username)) {
						$message = Localization::getLocale('the_username_is_not_valid_it_must_consist_of_at_least_2_characters_and_a_maximum_of_16_characters');
					} else if (empty($email) || !preg_match('/^([a-zæøåA-ZÆØÅ0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$message = Localization::getLocale('the_email_address_is_not_valid');
					} else if ($email != $confirmEmail) {
						$message = Localization::getLocale('the_email_addresses_does_not_match');
					} else if (!is_numeric($gender)) {
						$message = Localization::getLocale('you_have_entered_an_invalid_gender');
					} else if (!is_numeric($phone) || strlen($phone) < 8 || strlen($phone) > 8) {
						$message = Localization::getLocale('the_phone_number_is_not_valid');
					} else if (empty($address) && strlen($address) > 32) {
						$message = Localization::getLocale('you_must_enter_a_valid_address');
					} else if (!is_numeric($postalcode) || strlen($postalcode) > 4 || !CityDictionary::hasPostalCode($postalcode)) {
						$message = Localization::getLocale('the_postcode_is_not_valid_the_postcode_consists_of_4_characters');
					} else if (!preg_match('/^[a-zæøåA-ZÆØÅ0-9_-]{2,16}$/', $nickname)) {
						$message = Localization::getLocale('the_nickname_is_not_valid_it_must_consist_of_at_least_2_characters_and_maximum_16_characters');
					} else if (date_diff(date_create($birthdate), date_create('now'))->y < 18 && (!isset($_POST['emergencycontactphone']) || !is_numeric($emergencyContactPhone) || strlen($emergencyContactPhone) < 8 || strlen($emergencyContactPhone) > 8)) {
						if (!is_numeric($emergencyContactPhone)) {
							$message = Localization::getLocale('parent_phone_must_be_a_number');
						} else if (strlen($emergencyContactPhone) < 8) {
							$message = Localization::getLocale('parent_phone_is_too_short_it_must_consist_of_at_least_8_characters');
						}

						$message = Localization::getLocale('you_are_below_the_age_of_18_and_must_therefore_provide_a_phone_number_to_a_guardian');
					} else {
						UserHandler::updateUser($editUser,
																		$firstname,
																		$lastname,
																		$username,
																		$email,
																		$birthdate,
																		$gender,
																		$phone,
																		$address,
																		$postalcode,
																		$nickname);

						if (($editUser->hasEmergencyContact() ||
							isset($_POST['emergencycontactphone'])) &&
							is_numeric($emergencyContactPhone) &&
							strlen($emergencyContactPhone) >= 8) {
							EmergencyContactHandler::createEmergencyContact($editUser, $emergencyContactPhone);
						}

						$result = true;
					}
				} else {
					$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
				}
			} else {
				$message = Localization::getLocale('you_do_not_have_permission_to_do_that');
			}
		} else {
			$message = Localization::getLocale('this_user_does_not_exist');
		}
	} else {
		$message = Localization::getLocale('no_user_specified');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();
?>
