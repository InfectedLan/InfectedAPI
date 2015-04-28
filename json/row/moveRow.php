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
require_once 'localization.php';
require_once 'handlers/rowhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.seatmap')) {		
		if (isset($_GET['row'])) {
			$row = RowHandler::getRow($_GET['row']);
			
			if ($row != null) {
				if (isset($_GET['x']) && 
				    isset($_GET['y'])) {
					RowHandler::updateRow($row,  $_GET['x'], $_GET['y']);
					$result = true;
				} else {
					$message = Localization::getLocale('position_not_set');
				}
			} else {
				$message = Localization::getLocale('this_row_does_not_exist');
			}
		} else {
			$message = Localization::getLocale('no_row_specified');
		}
	} else {
		$message = Localization::getLocale('you_do_not_have_permission_to_do_that');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result), JSON_PRETTY_PRINT);
} else {
	echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
}
?>