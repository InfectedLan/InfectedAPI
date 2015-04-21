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
 * but WITHOUT ANY WARRANTY, without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Localization {
	static $localizationList,

	/*
	 * Get locale by key.
	 */
	public static function getLocale($key) {
		// If key exists in array, return the value.
		if (array_key_exists($key, self::$localizationList)) {
			return self::$localizationList[$key];
		}

		// Otherwise, return an error string.
		return 'Locale not found, this is a bug.',
	}

	/*
	 * Get locale by key, with the given replace argument.
	 */
	public static function getLocaleWithArgument($key, $argument) {
		return str_replace('%s', $argument, self::getLocale($key));
	}
}

// Initialize the list.
Localization::$localizationList = array(
	'you_are_not_logged_in' 							 => 'Du er ikke logget inn.',
	'you_do_not_have_permission_to_do_that' 			 => 'Du har ikke rettigheter til dette.',
	'you_have_not_filled_out_the_required_fields'		 => 'Du har ikke fylt ut de nødvendige feltene.',
	'you_are_already_in_a_group'						 => 'Du er allerede i et crew.',
	
	/* JSON */
	// Agenda
	'the_agenda_you_are_trying_to_change_does_not_exist' => 'Agenda\'en du prøver å endre, finnes ikke.',
	'the_agenda_you_are_trying_to_remove_does_not_exist' => 'Agenda\'en du prøver å fjerne, finnes ikke.',

	// Application
	'this_application_does_not_exist'																				=> 'Denne søknaden finnes ikke.',
	'no_application_specified'																						=> 'Ingen søknad er spesifisert.',
	
	'you_have_already_submitted_an_application_to_value_you_can_apply_again_if_your_application_should_be_denied' 	=> 'Du har allerede søkt til "%s". Du kan søke igjen hvis søknaden din skulle bli avslått.',
	'you_must_upload_a_valid_picture_before_you_can_submit_an_application' 											=> 'Du må laste opp en gyldig bilde før du kan levere en søknad.',
	'your_appliction_to_value_is_now_submitted' 																	=> 'Din søknad til crewet "%s" er nå sendt.',
	
	'you_can_not_approve_applications_from_previous_events' 														=> 'Du kan ikke godkjenne søknader fra tidligere arrangementer.',
	'you_can_not_reject_applications_from_previous_events' 															=> 'Du kan ikke avslå søknader fra tidligere arrangementer.',
	'you_must_provide_a_reason_why_the_application_shall_be_rejected' 												=> 'Du må oppgi en grunn på hvorfor søkneden skal bli avvist.',
	'you_can_not_queue_applications_from_previous_events' 															=> 'Du kan ikke putte søknader fra tidligere arrangementer i køen.',
	'you_can_not_unqueue_applications_from_previous_events'															=> 'Du kan ikke ta søknader fra tidligere arrangementer ut av køen.',

	// Avatar
	'' => '',

	// Chat
	'' => '',

	// Clan
	'' => '',

	// Compo
	'' => '',

	// E-mail
	'your_email_was_sent_to_the_selected_user' => 'Din e-post ble sendt til den valgte brukeren.',
	'your_email_was_sent_to_the_selected_users' => 'Din e-post ble sendt til de valgte brukerene.',


	'' => '',

	// Event
	'' => '',

	// Group
	'' => '',

	// Invite
	'' => '',

	// Location
	'' => '',

	// Match
	'' => '',

	// Page
	'' => '',

	// Pay
	'' => '',

	// Permissions
	'' => '',

	// Restricted Page
	'' => '',

	// Row
	'' => '',

	// Seatmap
	'' => '',

	// Session
	'' => '',

	// Slide
	'' => '',

	// Team
	'' => '',

	// Ticket
	'' => '',

	// User
	'this_user_does_not_exist'																																=> 'Denne brukeren finnes ikke.',
	'no_user_specified'																																		=> 'Ingen bruker er spesifisert.',

	'the_username_is_already_in_use_by_someone_else' 																										=> 'Brukernavnet er allerede i bruk av en annen.',
	'the_email_address_is_already_in_use_by_someone_else' 																									=> 'E-post adressen er allerede i bruk av en annen.',
	'the_phone_number_is_already_in_use_by_someone_else' 																									=> 'Telefonnummeret er allerede i bruk av en annen.',
	'you_must_enter_a_first_name' 																															=> 'Du må skrive inn et fornavn.',
	'you_must_enter_a_last_name' 																															=> 'Du må skrive inn et etternavn.',	
	'the_username_is_not_valid_it_must_consist_of_at_least_2_characters_and_a_maximum_of_16_characters' 													=> 'Brukernavnet er ikke gyldig, det må bestå av minst 2 tegn og maksimum 16 tegn.',
	'you_must_enter_a_password' 																															=> 'Du må skrive inn et passord.',
	'the_password_is_too_short_it_must_consist_of_at_least_8_characters' 																					=> 'Passordet er for kort, det må minst bestå av 8 tegn.',
	'the_password_is_too_long_it_can_not_exceed_16_characters' 																								=> 'Passordet er for langt, det kan maksimum bestå av 16 tegn.',
	'passwords_does_not_match' 																																=> 'Passordene er ikke like.',
	'the_email_address_is_not_valid' 																														=> 'E-post adressen er ikke gyldig.',
	'the_email_addresses_does_not_match' 																													=> 'E-post adressene er ikke like.',
	'you_have_entered_an_invalid_gender' 																													=> 'Du har oppgitt et ugyldig kjønn.',
	'the_phone_number_is_not_valid' 																														=> 'Telefonnummeret er ikke gyldig.',
	'you_must_enter_a_valid_address' 																														=> 'Du må skrive inn en gyldig adresse.',
	'the_postcode_is_not_valid_the_postcode_consists_of_4_characters' 																						=> 'Postnummeret er ikke gyldig, postnummeret består av 4 tegn.',
	'the_nickname_is_not_valid_it_must_consist_of_at_least_2_characters_and_maximum_16_characters' 															=> 'Kallenavnet er ikke gyldig, det må bestå av minst 2 tegn og maksimum 16 tegn.',
	'parent_phone_must_be_a_number' 																														=> 'Foresatte\'s telefonnummer må være et tall.',
	'parent_phone_is_too_short_it_must_consist_of_at_least_8_characters' 																					=> 'Foresatte\'s telefonnummer er for kort, det må bestå av minst 8 tegn.',
	'you_are_below_the_age_of_18_and_must_therefore_provide_a_phone_number_to_a_guardian' 																	=> 'Du er under 18 år, og må derfor oppgi et telefonnummer til en foresatt.',
	'your_account_is_now_successfully_registered_you_will_now_receive_an_activation_link_per_email_remember_to_check_spam_folder_if_you_should_not_find_it' => 'Din bruker har nå blitt registrert! Du vil nå motta en aktiveringslink per E-post. Husk å sjekk søppelpost/spam hvis du ikke skulle finne den.',
	
	'the_old_password_you_entered_was_incorrect' 																											=> 'Det gamle passordet du skrev inn var feil.',

	'no_results_found' 																																		=> 'Fant ingen resultater.';
	'no_keyword_provided' 																																	=> 'Ikke noe søkeord oppgitt.';

	'the_user_got_a_ticket_and_can_therefore_not_be_removed' 																								=> 'Brukeren har en billett, og kan derfor ikke fjernes.';

	'an_email_has_been_sent_to_your_registered_address_click_the_link_to_change_your_password' 																=> 'En e-post har blitt sendt til din registrerte adresse, klikk på linken for å endre passordet ditt.';
	'could_not_find_the_user_in_the_database' 																												=> 'Kunne ikke finne brukeren i databasen.';
	'you_must_enter_a_username_an_email_address_or_a_phone_number' 																							=> 'Du må skrive inn et brukernavn, en e-post adresse eller et telefonnummer.';
	'your_password_is_now_changed' 																															=> 'Passordet ditt er nå endret.';
	'the_link_to_reset_your_password_is_no_longer_valid' 																									=> 'Linken til å resette passordet ditt, er ikke lengre gyldig.';

	'the_user_you_tried_to_switch_to_does_not_exist' => 'Brukeren du prøvde å bytte til eksisterer ikke.';



































	'' => '');
?>