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
}

// Initialize the list.
Localization::$localizationList = array('you_are_not_logged_in' 								=> 'Du er ikke logget inn.',
										'you_do_not_have_permission_to_do_that' 				=> 'Du har ikke rettigheter til dette.',
										'you_have_not_filled_out_the_required_fields'			=> 'Du har ikke fylt ut de nødvendige feltene.',
										'the_object_you_are_trying_to_change_does_not_exist' 	=> 'Objektet du prøver å endre, finnes ikke.',
										'the_object_you_are_trying_to_remove_does_not_exist' 	=> 'Objektet du prøver å fjerne, finnes ikke.',
										'no_object_specified' 									=> 'Ikke noe objekt spesifisert.',
										
										/* JSON */
										// Application
										'you_can_not_approve_applications_from_previous_events' => 'Du kan ikke godkjenne søknader fra tidligere arrangementer.',
										'this_application_does_not_exist' 						=> 'Denne søknaden finnes ikke.',

										'no_application_specified' 								=> 'Ingen søknad er spesifisert.',
										'' => '',
										'' => '',
										'' => '',
										'' => '',
										'' => '',
										'' => '',
										'' => '',
										'' => '');
?>