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
				$seatmap = EventHandler::getCurrentEvent()->getSeatmap();
				
				foreach ($seatmap->getRows() as $row) {
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
			echo '</body>';
		echo '</html>';

	} else {
		echo '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	echo '<p>Du er ikke logget inn.</p>';
}
?>