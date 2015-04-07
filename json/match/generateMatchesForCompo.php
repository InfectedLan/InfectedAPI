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

require_once 'session.php';
require_once 'handlers/compohandler.php';

$message = null;
$result = false;

// TODO: This should not be stored in the database as this is constant information, we'll have to move this to some static variable.
if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('event.compo')) {

		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$compo = CompoHandler::getCompo($_GET['id']);

			if ($compo != null) {
				if (isset($_GET['startTime']) && 
					!empty($_GET['startTime']) && 
					isset($_GET['compoSpacing']) && 
					!empty($_GET['compoSpacing'])) {

					if (!CompoHandler::hasGeneratedMatches($compo)) {
						CompoHandler::generateDoubleElimination($compo, $_GET['startTime'], $_GET['compoSpacing']);
						$result = true;
					} else {
						$message = '<p>Compoen har allerede genererte matcher.</p>';
					}
				} else {
					$message = '<p>Felt mangler.</p>';
				}
			} else {
				$message = '<p>Compoen finnes ikke!</p>';
			}
		} else {
			$message = '<p>Felt mangler!</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til å gjøre dette!</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn!</p>';
}

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>