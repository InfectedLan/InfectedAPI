<?php
require_once 'session.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/rowhandler.php';
require_once 'handlers/seathandler.php';
if(Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	if($user->hasPermission("functions.print-ticket-labels") || $user->hasPermission("*")) {

		echo '<html xmlns="http://www.w3.org/1999/xhtml">';
			echo '<head>';
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
				echo '<link href="../style/ticketlabelstyle.css" rel="stylesheet" type="text/css" />';
				echo '<style type="text/css">';
					
				echo '</style>';
			echo '</head>';
			echo '<body>';
				$currEvent = EventHandler::getCurrentEvent();
				$seatmap = SeatmapHandler::getSeatmap($currEvent->getSeatmap());

				$rows = SeatmapHandler::getRows($seatmap);
				foreach($rows as $row) {
					$seats = RowHandler::getSeats($row);
					foreach($seats as $seat) {
						echo '<div id="navn">';
							echo '<img width="600px" src="../content/static/infected_logo_print_all.jpg">';
							echo '<br>';
							if(SeatHandler::hasOwner($seat)) {
								$owner = SeatHandler::getOwner($seat);
								echo $owner->getDisplayName();
							} else {
								echo 'Ledig plass!';
							}
						echo '</div>';
						echo '<div id="sete">';
							echo SeatHandler::getHumanString($seat);
						echo '</div>';
						echo '<br />';
					}
				}
			echo '</body>';
		echo '</html>';

	} else {
		echo "Du har ikke tillatelse til dette!";
	}
} else {
	echo "Du er ikke logget inn! ;(";
}
?>