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

require_once 'localization.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/emergencycontacthandler.php';
require_once 'handlers/citydictionary.php';

$result = false;
$message = null;

if (isset($_GET['firstname']) && 
	isset($_GET['lastname']) && 
	isset($_GET['username']) &&  
	isset($_GET['password']) && 
	isset($_GET['confirmpassword']) && 
	isset($_GET['email']) && 
	isset($_GET['confirmemail']) && 
	isset($_GET['gender']) && 
	isset($_GET['birthday']) && 
	isset($_GET['birthmonth']) && 
	isset($_GET['birthyear']) && 
	isset($_GET['phone'])) {
	$firstname = ucfirst($_GET['firstname']);
	$lastname = ucfirst($_GET['lastname']);
	$username = $_GET['username'];
	$password = hash('sha256', $_GET['password']);
	$confirmPassword = hash('sha256', $_GET['confirmpassword']);
	$email = $_GET['email'];
	$confirmEmail = $_GET['confirmemail'];
	$gender = $_GET['gender'];
	$birthdate = $_GET['birthyear'] . '-' . $_GET['birthmonth'] . '-' . $_GET['birthday']; 
	$phone = $_GET['phone'];
	$address = ucfirst($_GET['address']);
	$postalcode = $_GET['postalcode'];
	$nickname = !empty($_GET['nickname']) ? $_GET['nickname'] : $username;
	$emergencycontactphone = isset($_GET['emergencycontactphone']) ? $_GET['emergencycontactphone'] : null;
	
	if (UserHandler::userExists($username)) {
		$message = Localization::getLocale('the_username_is_already_in_use_by_someone_else');
	} else if (UserHandler::userExists($email)) {
		$message = Localization::getLocale('the_email_address_is_already_in_use_by_someone_else');
	} else if (UserHandler::userExists($phone)) {
		$message = Localization::getLocale('the_phone_number_is_already_in_use_by_someone_else');
	} else if (empty($firstname) || strlen($firstname) > 32) {
		$message = Localization::getLocale('you_must_enter_a_first_name');
	} else if (empty($lastname) || strlen($lastname) > 32) {
		$message = Localization::getLocale('you_must_enter_a_last_name');
	} else if (!preg_match('/^[a-zæøåA-ZÆØÅ0-9_-]{2,16}$/', $username)) {
		$message = Localization::getLocale('the_username_is_not_valid_it_must_consist_of_at_least_2_characters_and_a_maximum_of_16_characters');
	} else if (empty($password)) {
		$message = Localization::getLocale('you_must_enter_a_password');
	} else if (strlen($_GET['password']) < 8) {
		$message = Localization::getLocale('the_password_is_too_short_it_must_consist_of_at_least_8_characters');
	} else if (strlen($_GET['password']) > 32) {
		$message = Localization::getLocale('the_password_is_too_long_it_can_not_exceed_16_characters');
	} else if ($password != $confirmPassword) {
		$message = Localization::getLocale('passwords_does_not_match');
	} else if (empty($email) || !preg_match('/^([a-zæøåA-ZÆØÅ0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$message = Localization::getLocale('the_email_address_is_not_valid');
	} else if ($email != $confirmEmail) {
		$message = Localization::getLocale('the_email_addresses_does_not_match');
	} else if (!is_numeric($gender)) {
		$message = Localization::getLocale('you_have_entered_an_invalid_gender');
	} else if (!is_numeric($phone) || $phone <= 0 || strlen($phone) < 8 || strlen($phone) > 8) {
		$message = Localization::getLocale('the_phone_number_is_not_valid');
	} else if (empty($address) && strlen($address) > 32) {
		$message = Localization::getLocale('you_must_enter_a_valid_address');
	} else if (!is_numeric($postalcode) || strlen($postalcode) != 4 || !CityDictionary::hasPostalCode($postalcode)) {
		$message = Localization::getLocale('the_postcode_is_not_valid_the_postcode_consists_of_4_characters');
	} else if (!preg_match('/^[a-zæøåA-ZÆØÅ0-9_-]{2,16}$/', $nickname) && !empty($nickname)) {
		$message = Localization::getLocale('the_nickname_is_not_valid_it_must_consist_of_at_least_2_characters_and_maximum_16_characters');
	} else if (date_diff(date_create($birthdate), date_create('now'))->y < 18 && (!isset($_GET['emergencycontactphone']) || !is_numeric($emergencycontactphone) || strlen($emergencycontactphone) != 8)) {
		if (!is_numeric($emergencycontactphone)) {
			$message = Localization::getLocale('parent_phone_must_be_a_number');
		} else if (strlen($emergencycontactphone) != 8) {
			$message = Localization::getLocale('parent_phone_is_too_short_it_must_consist_of_at_least_8_characters');
		}
		
		$message = Localization::getLocale('you_are_below_the_age_of_18_and_must_therefore_provide_a_phone_number_to_a_guardian');
	} else {
		// Creates the user in database.
		$user = UserHandler::createUser($firstname, 
										$lastname, 
										$username, 
										$password, 
										$email, 
										$birthdate, 
										$gender, 
										$phone, 
										$address, 
										$postalcode, 
										$nickname);
		
		if (isset($_GET['emergencycontactphone']) &&
			is_numeric($emergencycontactphone) && 
			$emergencycontactphone > 0 &&
			strlen($emergencycontactphone) < 8 &&
			strlen($emergencycontactphone) > 8) {
			EmergencyContactHandler::createEmergencyContact($user, $emergencycontactphone);
		}
		
		$user->sendRegistrationMail();
		
		$result = true;
		$message = Localization::getLocale('your_account_is_now_successfully_registered_you_will_now_receive_an_activation_link_per_email_remember_to_check_spam_folder_if_you_should_not_find_it');
	}
} else {
	$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
}

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>