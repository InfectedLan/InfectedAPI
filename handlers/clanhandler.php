<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/clan.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/invitehandler.php';
class ClanHandler {
	public static function getClan($id) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_clans . '` WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if($row) {
			return new Clan($row['id'], $row['chief'], $row['name'], $row['event'], $row['tag']);
		}
	}

	public static function getClansForUser($user) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` WHERE `userId` = ' . $con->real_escape_string($user->getId()) . ';');

		$clanArray = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($clanArray, self::getClan($row['clanId']));
		}
		/*
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_clans . '` WHERE `chief` = ' . $con->real_escape_string($user->getId()) . ';');

		while($row = mysqli_fetch_array($result)) {
			array_push($clanArray, self::getClan($row['id']));
		}*/

		return $clanArray;
	}

	public static function getCompo($clan) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_participantof . '` WHERE `clanId` = ' . $con->real_escape_string( $clan->getId() ) . ';');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return CompoHandler::getCompo($row['compoId']);
		}
	}

	public static function getInvites($clan) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_invites . '` WHERE `clanId` = ' . $con->real_escape_string($clan->getId()) . ';');

		$peopleArray = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($peopleArray, InviteHandler::getInvite($row['id']));
		}

		MySQL::close($con);

		return $peopleArray;
	}

	public static function getMembers($clan) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` WHERE `clanId` = ' . $con->real_escape_string($clan->getId()) . ';');

		$peopleArray = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($peopleArray, UserHandler::getUser($row['userId']));
		}

		MySQL::close($con);

		return $peopleArray;
	}

	public static function isMember($user, $clan) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` WHERE `clanId` = ' . $con->real_escape_string($clan->getId()) . ' AND `userId` = '. $con->real_escape_string($user->getId()) . ';');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		return null ==! $row;
	}

	public static function inviteUser($clan, $user)
	{
		$con = MySQL::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_invites . '` (`userId`, `clanId`) VALUES (\'' . $con->real_escape_string($user->getId()) . '\', \'' . $con->real_escape_string($clan->getId()) . '\');');

		MySQL::close($con);
	}

	/*public static function createClan($owner, $name)
	{
		$event = EventHandler::getCurrentEvent();

		$con = MySQL::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_clans . '` (`chief`, `name`, `event`) 
					VALUES (\'' . $owner->getId() . '\', \'' . $con->real_escape_string($name) . '\', \'' . $event->getId() . '\');');
		
		MySQL::close($con);
	}*/
	public static function registerClan($name, $tag, $compo, $user) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		$event = EventHandler::getCurrentEvent();

		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_clans . '` (`chief`, `name`, `tag`, `event`) VALUES (\'' . 
																																		$con->real_escape_string($user->getId()) . '\', \'' . 
																																		$con->real_escape_string( htmlentities($name, ENT_QUOTES, 'UTF-8') ) . '\', \'' . 
																																		$con->real_escape_string( htmlentities($tag, ENT_QUOTES, 'UTF-8') ) . '\', \'' . 
																																		$con->real_escape_string( $event->getId() ) . '\');');
		//Fetch the id of the clan we just added
		$fetchedId = mysqli_insert_id($con);

		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_participantof . '` (`clanId`, `compoId`) VALUES (\'' . $con->real_escape_string($fetchedId) . '\', \'' . $con->real_escape_string($compo) . '\');');
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`clanId`, `userId`) VALUES (\'' . $con->real_escape_string($fetchedId) . '\', \'' . $con->real_escape_string($user->getId()) . '\');');

		MySQL::close($con);

		return $fetchedId;
	}
}
?>