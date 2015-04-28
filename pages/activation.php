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

require_once 'handlers/registrationcodehandler.php';

if (isset($_GET['code'])) {
	$code = $_GET['code'];

	if (RegistrationCodeHandler::hasRegistrationCode($code)) {
		RegistrationCodeHandler::removeRegistrationCode($_GET['code']);
		
		echo '<p>Brukeren din er nå aktivert og klar for bruk.</p>';
	} else {
		echo '<p>Brukeren din er allerede aktivert.</p>';
	}
} else {
	echo '<p>En feil oppstod.</p>';
}
?>