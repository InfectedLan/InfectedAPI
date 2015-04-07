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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Localization {
	private static $localizationList;

	/*
	 * Get locale by key.
	 */
	public static function getLocale($key) {
		// If key exists in array, return the value.
		if (in_array($key, $localizationList)) {
			return $localizationList[$key];
		}

		// Otherwise, return an error string.
		return 'Locale not found, this is a bug.';
	}

	/*
	 * Get locale by key, with the given replace argument.
	 */
	public static function getLocaleWithArgument($key, $argument) {
		return str_replace('%s', $argument, self::getLocale($key));
	}
}

// Initialize the list.
Localization::$localizationList = array('you_are_not_logged_in' 							 => 'Du er ikke logget inn.',
										'you_do_not_have_permission_to_do_that' 			 => 'Du har ikke rettigheter til dette.',
										'you_have_not_filled_out_the_required_fields'		 => 'Du har ikke fylt ut de nødvendige feltene.',
										'no_object_specified' 								 => 'Ikke noe objekt spesifisert.',
										'you_are_already_in_a_group'						 => 'Du er allerede i et crew.';
										
										/* JSON */
										// Agenda
										'the_agenda_you_are_trying_to_change_does_not_exist' => 'Agenda\'en du prøver å endre, finnes ikke.',
										'the_agenda_you_are_trying_to_remove_does_not_exist' => 'Agenda\'en du prøver å fjerne, finnes ikke.',

										// Application
										'this_application_does_not_exist'	=> 'Denne søknaden finnes ikke.',
										'no_application_specified'			=> 'Ingen søknad er spesifisert.',
										
										'you_have_already_submitted_an_application_to_value_you_can_apply_again_if_your_application_should_be_denied' 	=> 'Du har allerede søkt til "%s". Du kan søke igjen hvis søknaden din skulle bli avslått.',
										'you_must_upload_a_valid_picture_before_you_can_submit_an_application' 											=> 'Du må laste opp en gyldig bilde før du kan levere en søknad.',
										'your_appliction_to_value_is_now_submitted' 																	=> 'Din søknad til crewet "%s" er nå sendt.',
										
										'you_can_not_approve_applications_from_previous_events' 			=> 'Du kan ikke godkjenne søknader fra tidligere arrangementer.',
										'you_can_not_reject_applications_from_previous_events' 				=> 'Du kan ikke avslå søknader fra tidligere arrangementer.',
										'you_must_provide_a_reason_why_the_application_shall_be_rejected' 	=> 'Du må oppgi en grunn på hvorfor søkneden skal bli avvist.',
										'you_can_not_queue_applications_from_previous_events' 				=> 'Du kan ikke putte søknader fra tidligere arrangementer i køen.',
										'you_can_not_unqueue_applications_from_previous_events'				=> 'Du kan ikke ta søknader fra tidligere arrangementer ut av køen.',
										'' => '');
?>