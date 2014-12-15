<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/application.php';

class ApplicationHandler {
	/* 
	 * Get an application by it's internal id (No matter event).
	 */
	public static function getApplication($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = $con->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '` 
							   WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		$con->close();

		if ($row) {
			return new Application($row['id'], 
								   $row['eventId'], 
								   $row['groupId'],
								   $row['userId'], 								   
								   $row['openedTime'], 
								   $row['closedTime'], 
								   $row['state'], 
								   $row['content'], 
								   $row['comment']);
		}
	}
	
	/*
	 * Returns a list of all applications (For all events)
	 */
	public static function getApplications() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = $con->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`;');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		$con->close();
		
		return $applicationList;
	}
	
	/* 
	 * Returns a list of pending applications.
	 */
	public static function getPendingApplications() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = $con->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.`id` FROM `' . Settings::db_table_infected_crew_applications . '`
							   LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
							   ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
							   WHERE `applicationId` IS NULL
							   AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
							   AND `state` = \'1\'
							   ORDER BY `openedTime`;');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		$con->close();
		
		return $applicationList;
	}
	
	/* 
	 * Returns a list of pending applications.
	 */
	public static function getPendingApplicationsForGroup($group) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = $con->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.`id` FROM `' . Settings::db_table_infected_crew_applications . '`
							   LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
							   ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
							   WHERE `applicationId` IS NULL
							   AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
							   AND `groupId` = \'' . $con->real_escape_string($group->getId()) .  '\'
							   AND `state` = \'1\'
							   ORDER BY `openedTime`;');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		$con->close();
		
		return $applicationList;
	}
	
	/*
	 * Returns a list of all queued applications.
	 */
	public static function getQueuedApplications() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = $con->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.`id` FROM `' . Settings::db_table_infected_crew_applications . '`
							   LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
							   ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
							   WHERE `applicationId` IS NOT NULL
							   AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
							   AND `state` = \'1\'
							   ORDER BY `' . Settings::db_table_infected_crew_applicationqueue . '`.`id`;');
		
		$queuedApplicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($queuedApplicationList, self::getApplication($row['id']));
		}
		
		$con->close();
		
		return $queuedApplicationList;
	}
	
	/*
	 * Returns a list of all queued applications for a given group.
	 */
	public static function getQueuedApplicationsForGroup($group) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = $con->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.`id` FROM `' . Settings::db_table_infected_crew_applications . '`
							   LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
							   ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
							   WHERE `applicationId` IS NOT NULL
							   AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
							   AND `groupId` = \'' . $con->real_escape_string($group->getId()) .  '\'
							   AND `state` = \'1\'
							   ORDER BY `' . Settings::db_table_infected_crew_applicationqueue . '`.`id`;');
		
		$queuedApplicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($queuedApplicationList, self::getApplication($row['id']));
		}
		
		$con->close();
		
		return $queuedApplicationList;
	}
	
	/* 
	 * Create a new application. 
	 */
	public static function createApplication($event, $group, $user, $content) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$con->query('INSERT INTO `' . Settings::db_table_infected_crew_applications . '` (`eventId`, `groupId`, `userId`, `openedTime`, `state`, `content`) 
					 VALUES (\'' . $con->real_escape_string($event->getId()) . '\', 
							 \'' . $con->real_escape_string($group->getId()) . '\', 
							 \'' . $con->real_escape_string($user->getId()) . '\', 
							 \'' . date('Y-m-d H:i:s') . '\',
							 \'1\',
							 \'' . $con->real_escape_string($content) . '\');');
		
		$con->close();
		
		// Notify the group leader by email.
		self::sendApplicationCreatedMail($user, $group);
	}
	
	/*
	 * Sends an mail to the users address with status information.
	 */
	public function sendApplicationCreatedMail($user, $group) {
		if ($group->getLeader() != null) {
			$message = array();
			$message[] = '<!DOCTYPE html>';
			$message[] = '<html>';
				$message[] = '<body>';
					$message[] = '<h3>Hei!</h3>';
					$message[] = '<p>Du har fått en ny søknad til crewet ditt (' . $group->getTitle() . ') fra ' . $user->getFullName() . '<p>';
					$message[] = '<p>Klikk <a href="https://crew.infected.no/v2/index.php?page=chief-applications">her</a> for å se den.</p>';
					$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
				$message[] = '</body>';
			$message[] = '</html>';
				
			return MailManager::sendMail($group->getLeader(), 'Ny søknad til ' . $group->getTitle() . ' crew', implode("\r\n", $message));
		}
	}
	
	/* 
	 * Remove an application.
	 */
	public static function removeApplication($application) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		// Remove the application.
		$con->query('DELETE FROM `' . Settings::db_table_infected_crew_applications . '` 
							WHERE `id` = \'' . $con->real_escape_string($application->getId()) . '\';');
		
		// Remove the application from the queue, if present.
		self::unqueueApplication($application);
		
		$con->close();
	}
	
	/*
	 * Accepts an application, with a optional comment.
	 */
	public static function acceptApplication($application, $comment) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$con->query('UPDATE `' . Settings::db_table_infected_crew_applications . '` 
					 SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
						 `state` = \'2\',
						 `comment` = \'' . $con->real_escape_string($comment) . '\'
					 WHERE `id` = \'' . $con->real_escape_string($application->getId()) . '\';');
		
		// Remove the application from the queue, if present.
		self::unqueueApplication($application);
		
		// Set the user in the new group
		GroupHandler::changeGroupForUser($application->getUser(), $application->getGroup());
		
		// Send email notification to the user.
		self::sendApplicationAccpetedMail($application);
		
		$con->close();
	}
	
	/*
	 * Sends an mail to the users address with status information.
	 */
	public function sendApplicationAccpetedMail($application) {
		$message = array();
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>Din crew søknad til ' . $application->getGroup()->getTitle() . ' crew har blitt godkjent.</p>';
				$message[] = 'Du kan nå logge inn på <a href="https://crew.infected.no/">Infected Crew</a> å bli kjent med det nye crewet ditt.<br>';
				$message[] = 'Ta deg tid til å gå igjennom profilen din å sjekk at du har oppgitt alle og riktige opplysninger da dette blir brukt til adgangskort osv. under arrangementet.</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';
			
		return MailManager::sendMail($application->getUser(), 'Din Infected Crew søknad har blitt oppdatert', implode("\r\n", $message));
	}
	
	/*
	 * Rejects an application, with a optional comment.
	 */
	public static function rejectApplication($application, $comment) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$con->query('UPDATE `' . Settings::db_table_infected_crew_applications . '` 
					 SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
					     `state` = \'3\', 
						 `comment` = \'' . $con->real_escape_string($comment) . '\'
					 WHERE `id` = \'' . $con->real_escape_string($application->getId()) . '\';');
							
		// Remove the application from the queue, if present.
		self::unqueueApplication($application);
		
		// Send email notification to the user.
		self::sendApplicationRejectedMail($application);
		
		$con->close();
	}
	
	/*
	 * Sends an mail to the users address with status information.
	 */
	public function sendApplicationRejectedMail($application) {
		$message = array();
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>Din crew søknad til ' . $application->getGroup()->getTitle() . ' crew har blitt avvist.<br>';
				$message[] = 'Du er velkommen til å søke til et annet crew eller prøve på nytt neste gang.</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';
			
		return MailManager::sendMail($application->getUser(), 'Din Infected Crew søknad har blitt oppdatert', implode("\r\n", $message));
	}
	
	/*
	 * Checks if an application is queued.
	 */
	public static function isQueued($application) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = $con->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applicationqueue . '` 
							   WHERE `applicationId` = \'' . $con->real_escape_string($application->getId()) . '\';');
		
		$row = mysqli_fetch_array($result);
		
		$con->close();
		
		return $row ? true : false;
	}
	
	/*
	 * Puts an application in queue.
	 */
	public static function queueApplication($application) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		if (!self::isQueued($application)) {
			$con->query('INSERT INTO `' . Settings::db_table_infected_crew_applicationqueue . '` (`applicationId`) 
						 VALUES (\'' . $con->real_escape_string($application->getId()) . '\');');
		}
									
		$con->close();
		
		// Send email notification to the user.
		self::sendApplicationQueuedMail($application);
	}
	
	/*
	 * Sends an mail to the users address with status information.
	 */
	public function sendApplicationQueuedMail($application) {
		$message = array();
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>Din crew søknad til ' . $application->getGroup()->getTitle() . ' crew har blitt satt i kø.<br>';
				$message[] = 'Dette betyr at crewet du søkte for øyeblikket er fullt, men at er en aktuell kandidat, <br>';
				$message[] = 'søknaden din vil bli godkjent senere dersom det blir behov for flere medlemmer.</p>';
				$message[] = '<p>I mellomtiden er du velkommen til å søke deg inn i andre crew, men merk at det da er den første godkjente søknaden som bil bli godkjent.</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';
			
		return MailManager::sendMail($application->getUser(), 'Din Infected Crew søknad har blitt oppdatert', implode("\r\n", $message));
	}
	
	/*
	 * Removes an application from queue.
	 */
	public static function unqueueApplication($application) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$con->query('DELETE FROM `' . Settings::db_table_infected_crew_applicationqueue . '` 
					 WHERE `applicationId` = \'' . $con->real_escape_string($application->getId()) . '\';');
									
		$con->close();
	}
	
	/*
	 * Returns a true if user has application for group.
	 */
	public static function hasUserApplicationForGroup($user, $group) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = $con->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
							   WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
							   AND `userId` = \'' . $con->real_escape_string($user->getId()) . '\'
							   AND `groupId` = \'' . $con->real_escape_string($group->getId()) . '\'
							   AND `state` = \'1\'
							   OR `state` = \'2\';');
		
		$row = mysqli_fetch_array($result);
		
		$con->close();
		
		return $row ? true : false;
	}
	
	/*
	 * Returns a list of all applications for given user.
	 */
	public static function getUserApplications($user) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = $con->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
							   WHERE `userId` = \'' . $user->getId() . '\';');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		$con->close();
		
		return $applicationList;
	}
	
	/*
	 * Returns a list of all applications for that event.
	 */
	public static function getApplicationsForEvent($event) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = $con->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
							   WHERE `eventId` = \'' . $event->getId() . '\';');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		$con->close();
		
		return $applicationList;
	}
}
?>