<?php
require_once 'session.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/rowhandler.php';
require_once 'handlers/seathandler.php';

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('event.table-labels')) {
		echo '<html>';
			echo '<head>';
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
				echo '<link href="../../styles/ticketlabelstyle.css" rel="stylesheet" type="text/css" />';
			echo '</head>';
			echo '<body>';
				$currentEvent = EventHandler::getCurrentEvent();
				$seatmap = $currentEvent->getSeatmap());
				$rowList = SeatmapHandler::getRows($seatmap);
				
				foreach ($rowList as $row) {
					$seatList = RowHandler::getSeats($row);
					
					foreach ($seats as $seat) {
						echo '<div id="name">';
							echo '<img width="600px" src="../../content/static/infected_logo_print_all.jpg">';
							echo '<br>';
							if (SeatHandler::hasOwner($seat)) {
								$owner = SeatHandler::getOwner($seat);
								echo $owner->getDisplayName();
							} else {
								echo 'Ledig plass!';
							}
						echo '</div>';
						echo '<div id="seat">';
							echo SeatHandler::getHumanString($seat);
						echo '</div>';
						echo '<br />';
					}
				}
			echo '</body>';
		echo '</html>';

	} else {
		echo 'Du har ikke tillatelse til dette.';
	}
} else {
	echo 'Du er ikke logget inn.';
}
?>