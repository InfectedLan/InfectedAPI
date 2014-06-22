<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'mysql.php';
require_once 'utils.php';

class Group {
	private $settings;
	private $database;
	private $mysql;
	private $utils;
	
	private $id;
	private $name;
	private $title;
	private $description;
	private $chief;
	
	public function Group($id, $name, $title, $description, $chief) {
		$this->settings = new Settings();
		$this->database = new Database();
		$this->mysql = new MySQL();
		$this->utils = new Utils();
		
		$this->id = $id;
		$this->name = $name;
		$this->title = $title;
		$this->description = $description;
		$this->chief = $chief;
	}
	
	/* Returns the internal id for this group */
	public function getId() {
		return $this->id;
	}
	
	/* Returns the name of this group as string */
	public function getName() {
		return $this->name;
	}
	
	/* Returns the title of this group as string */
	public function getTitle() {
		return $this->title;
	}
	
	/* Returns the description of this group as string */
	public function getDescription() {
		return $this->description;
	}
	
	/* Returns the user which is chief for this group */
	public function getChief() {
		return $this->database->getUser($this->chief);
	}
	
	/* Returns an array of users that are members of this group */
	public function getMembers() {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[0][9] . ' 
									  LEFT JOIN infecrjn_crew.' . $this->settings->tableList[1][3] . ' ON ' . $this->settings->tableList[0][9] . '.id = userId 
									  WHERE groupId = \'' . $this->getId() . '\'
									  ORDER BY firstname ASC');
		
		$memberList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($memberList, new User($row['id'], 
										   $row['firstname'], 
										   $row['lastname'], 
										   $row['username'], 
										   $row['password'], 
										   $row['email'], 
										   $row['birthDate'], 
										   $row['gender'], 
										   $row['phone'], 
										   $row['address'], 
										   $row['postalCode'], 
										   $row['nickname']));
		}
		
		$this->mysql->close($con);
		
		return $memberList;
	}
	
	/* Retuns an array of all teams in this group */
	public function getTeams() {
		$con = $this->mysql->open(1);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][6] . ' WHERE groupId=\'' . $this->getId() . '\'');
		
		$teamList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($teamList, $this->database->getTeam($row['id']));
		}
		
		$this->mysql->close($con);
		
		return $teamList;
	}
	
	public function getPendingApplications() {
		$con = $this->mysql->open(1);
		
		$applicationList = array();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][0] . ' WHERE groupId=\'' . $this->getId() . '\' AND state=\'1\'');
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, $this->database->getApplication($row['id']));
		}
		
		$this->mysql->close($con);
		
		return $applicationList;
	}
	
	public function displayWithInfo() {
		echo '<div class="crewParagraph">';
			echo '<h1>' . $this->getTitle() . '</h1>';
			echo $this->getDescription();
		echo '</div>';
			
		if ($this->utils->isAuthenticated()) {
			$this->display();
		}
	}
	
	public function display() {
		$user = $this->utils->getUser();
		
		if ($user->isGroupMember()) {
			$memberList = $this->getMembers();
			
			$i = 0;
			
			foreach ($memberList as $member) {
				echo '<div class="';
					
					if ($i % 2 == 0) {
						echo 'crewEntryLeft';
					} else {
						echo 'crewEntryRight';
					}
					
				echo '">';
					echo '<a href="index.php?page=profile&id=' . $member->getId() . '"><img src="' . $member->getThumbnailAvatar()->getRelativeUrl() . '" width="146" height="110" style="float: right;"></a>';
					echo '<p>Navn: ' . $member->getFirstname() . ' "' . $member->getNickname() . '" ' . $member->getLastname() . '<br>';
					echo 'Stilling: ' . $member->getPosition() . '<br>';
					echo 'Telefon: ' . $member->getPhone() . '<br>';
					echo 'E-post: ' . $member->getEmail() . '</p>';
				echo '</div>';
					
				$i++;
			}
		}
	}
}
?>