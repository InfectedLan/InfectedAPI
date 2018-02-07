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
require_once 'handlers/eventhandler.php';
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/rowhandler.php';
require_once 'handlers/seathandler.php';

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('*') ||
		$user->hasPermission('event.table-labels')) {
		echo '<!DOCTYPE html>';
		echo '<html>';
			echo '<head>';
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
				echo '<link href="../../styles/ticketlabelstyle.css" rel="stylesheet" type="text/css" />';
			echo '</head>';
			echo '<body>';
				$event = EventHandler::getCurrentEvent();
				$rowList = $event->getSeatmap()->getRows();

				if (!empty($rowList)) {
					foreach ($rowList as $row) {
						foreach ($row->getSeats() as $seat) {
							echo '<div id="name">';
								echo '<img width="600px" src="../../content/static/infected_logo_print_all.jpg">';
								echo '<br>';

								if ($seat->hasTicket()) {
									$ticketUser = $seat->getTicket()->getUser();
									echo $ticketUser->getDisplayName();
								} else {
									echo 'Ledig plass!';
								}

							echo '</div>';
							echo '<div id="seat">';
								echo $seat->getString();
							echo '</div>';
							echo '<br>';
						}
					}
				} else {
					echo '<p>Det finnes ingen rader eller seter enda.</p>';
				}
			echo '</body>';
		echo '</html>';
	} else {
		echo '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	echo '<p>Du er ikke logget inn.</p>';
}
?>
