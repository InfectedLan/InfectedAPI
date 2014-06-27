<?php
//Crewpage database
require_once 'settings.php';
require_once 'mysql.php';
require_once 'utils.php';

require_once 'user.php';
require_once 'crewPage.php';
require_once 'sitePage.php';

require_once 'application.php';
require_once 'avatar.php';
require_once 'group.php';
require_once 'team.php';

class Database {
	private $settings;
	private $mysql;
	private $utils;
	
	public function __construct() {
		$this->settings = new Settings();
		$this->mysql = new MySQL();
		$this->utils = new Utils();
    }
	
	/*
	 *	Group
	 */
	
	/* Get a group by id */
	public function getGroup($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[1][2] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Group($row['id'], 
							$row['name'], 
							$row['title'], 
							$row['description'], 
							$row['chief']);
		}
		
		$this->mysql->close($con);
	}
	
	/* Get an array of all groups */
	public function getGroups() {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][2] . ' ORDER BY name');
		
		$groupList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($groupList, $this->getGroup($row['id']));
		}
		
		$this->mysql->close($con);
		
		return $groupList;
	}
	
	/* Create a new group */
	public function createGroup($name, $title, $description, $chief) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tableList[1][2] . ' (name, title, description, chief) 
							VALUES (\'' . $name . '\', 
									\'' . $title . '\', 
									\'' . $description . '\', 
									\'' . $chief . '\')');
		
		$this->mysql->close($con);
	}
	
	/* Remove a page */
	public function removeGroup($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'DELETE FROM ' . $this->settings->tableList[1][2] . ' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	/* Update a page */
	public function updateGroup($id, $name, $title, $description, $chief) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'UPDATE ' . $this->settings->tableList[1][2] . ' SET name=\'' . $name . '\', title=\'' . $title . '\', description=\'' . $description . '\', chief=\'' . $chief . '\' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	/*
	 *	Team
	 */
	
	/* Get a team by id */
	public function getTeam($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[1][6] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Team($row['id'], 
							$row['groupId'], 
							$row['name'], 
							$row['title'], 
							$row['description'], 
							$row['chief']);
		}
		
		$this->mysql->close($con);
	}
	
	/* Get an array of all teams */
	public function getTeams() {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][6]);
		
		$teamList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($teamList, $this->getTeam($row['id']));
		}
		
		$this->mysql->close($con);
		
		return $teamList;
	}
	
	/* Create a new team */
	public function createTeam($groupId, $name, $title, $description, $chief) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tableList[1][6] . ' (groupId, name, title, description, chief) 
							VALUES (\'' . $groupId . '\', 
									\'' . $name . '\', 
									\'' . $title . '\', 
									\'' . $description . '\', 
									\'' . $chief . '\')');
		
		$this->mysql->close($con);
	}
	
	/* Remove a team */
	public function removeTeam($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'DELETE FROM ' . $this->settings->tableList[1][6] . ' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	/* Update a team */
	public function updateTeam($id, $groupId, $name, $title, $description, $chief) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'UPDATE ' . $this->settings->tableList[1][6] . ' SET groupId=\'' . $groupId . '\', name=\'' . $name . '\', title=\'' . $title . '\', description=\'' . $description . '\', chief=\'' . $chief . '\' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	/*
	 *	Avatar
	 */
	
	/* Get a avatar by id */
	public function getAvatar($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[1][1] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Avatar($row['id'], 
							$row['userId'], 
							$row['relativeUrl'], 
							$row['state']);
		}
		
		$this->mysql->close($con);
	}
	
	public function getAvatars() {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][1]);
		
		$avatarList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($groupList, $this->getAvatar($row['id']));
		}
		
		$this->mysql->close($con);
		
		return $avatarList;
	}
	
	public function getPendingAvatars() {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][1] . ' WHERE state=\'1\'');
		
		$avatarList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($groupList, $this->getAvatar($row['id']));
		}
		
		$this->mysql->close($con);
		
		return $avatarList;
	}
	
	public function getAvatarForUser($userId) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][1] . ' WHERE userId=\'' . $userId . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return $this->getAvatar($row['id']);
		}
		
		$this->mysql->close($con);
	}
	
	/*
	 *	Application
	 */
	
	/* Get a application by id */
	public function getApplication($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[1][0] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Application($row['id'], 
								$row['userId'], 
								$row['groupId'], 
								$row['content'], 
								$row['state'], 
								$row['datetime'], 
								$row['reason']);
		}
		
		$this->mysql->close($con);
	}
	
	/* Get a list of all applications */
	public function getApplications() {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][0]);
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, $this->getApplication($row['id']));
		}
		
		$this->mysql->close($con);
		
		return $applicationList;
	}
	
	/*
	 *	Password resets
	 */
	
	/* Get a password reset code by userId */
	public function getResetCode($userId) {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		$result = mysqli_query($con, 'SELECT code FROM ' . $this->settings->tableList[0][10] . ' WHERE userId=\'' . $userId . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return $row['code'];
		}
		
		$this->mysql->close($con);
	}
	
	/* Set a password reset code for user */
	public function setResetCode($userId, $code) {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tableList[0][10] . ' (userId, code) 
							VALUES (\'' . $userId . '\', 
									\'' . $code . '\')');
		
		$this->mysql->close($con);
	}
}
?>