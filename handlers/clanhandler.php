<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/clan.php';
require_once 'handlers/eventhandler.php';
class ClanHandler {
	public static function getClan($id) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_clans . '` WHERE `id` = \'$id\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if($row) {
			return new Clan($row['id'], $row['chief'], $row['name'], $row['event']);
		}
	}

	public static function createClan($owner, $name)
	{
		$event = EventHandler::getCurrentEvent();

		$con = MySQL::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_clans . '` (`chief`, `name`, `event`) 
					VALUES (\'' . $owner->getId() . '\', \'' . $con->real_escape_string($name) . '\', \'' . $event->getId() . '\');');
		
		MySQL::close($con);
	}


	public static function inviteUser($user, $clan)
	{
		$con = MySQL::open(Settings::db_name_infected_compo);

		$inviteKey = md5(time() . ' ' . $user->getId() . $clan->getId());

		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_invites . '` (`userId`, `clanId`, `inviteCode`) VALUES (\'' . $user->getId() . '\', \'' . $clan->getId() . '\', \'' . $inviteKey . '\');');

		//Generate mail
		$message = array();
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>Du har blitt invitert i klanen <b>' . $clan->getName() . '</b> av ' . $user->getFullName() . '. Dersom du ønsker å være med, <a href="https://compo.infected.no/index.php?page=acceptClan&code=' . $inviteKey . '">klikk her</a>.</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';
			
		return MailManager::sendMail($user, 'Du har blitt invitert til en klan!', implode("\r\n", $message));
	}
}
?>