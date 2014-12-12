<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/checkinstatehandler.php';

echo '<html>';
	echo '<head>';
		echo '<script src="../scripts/jquery-1.11.1.min.js"></script>';
	echo '</head>';
	echo '<body>';
		echo '<script>';
			if (Session::isAuthenticated()) {
				$user = Session::getCurrentUser();
				if($user->hasPermission("*") || $user->hasPermission("functions.checkin")) {
					if(isset($_GET['id'])) {

						$ticket = TicketHandler::getTicket($_GET['id']);

						if(!CheckinStateHandler::isCheckedIn($ticket)) {
							echo "$.getJSON('../json/ticket/getTicketData.php?id=" . htmlentities($_GET['id'], ENT_QUOTES, 'UTF-8') . "', function(data){";
								echo "if(data.result == true) {";
									echo 'if(confirm("Sjekk at disse detaljene er riktige:\\nNavn: " + data.userData.fullName + "\\nKjønn: " + data.userData.gender + "\\nFødt: " + data.userData.birthDate + "\\nAlder: " + data.userData.age)) {';
										echo "$.getJSON('../json/checkinticket.php?id=" . htmlentities($_GET['id'], ENT_QUOTES, 'UTF-8') . "', function(data){";
											echo "if(data.result == true) {";
												echo "alert('Brukeren har blitt sjekket inn!');";
												echo 'close()';
											echo "} else {";
												echo "alert(data.message);";
												echo 'close();';
											echo "}";
										echo "});";
									echo '} else {';
										echo 'alert("Godkjenningen har blitt avbrutt!");';
										echo 'close();';
									echo '}';
								echo "} else {";
									echo "error(data.message);";
								echo "}";
							echo "});";
						} else {
							echo "alert('Denne billetten er allerede sjekket inn!');";
						}
					} else {
						echo 'alert("Vi mangler felt");';
					}
				} else {
					echo "alert('Du har ikke tillatelse til dette!');";
				}
			} else {
				echo 'alert("Du er ikke logget inn.");';
			}
		echo '</script>';
	echo '</body>';
echo '</html>';
?>