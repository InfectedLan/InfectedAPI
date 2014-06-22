<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'mysql.php';
require_once 'utils.php';

class Team {
	private $settings;
	private $database;
	private $mysql;
	private $utils;
	
	private $id;
	private $groupId;
	private $name;
	private $title;
	private $description;
	private $chief;
	
	public function Team($id, $groupId, $name, $title, $description, $chief) {
		$this->settings = new Settings();
		$this->database = new Database();
		$this->mysql = new MySQL();
		$this->utils = new Utils();
	
		$this->id = $id;
		$this->groupId = $groupId;
		$this->name = $name;
		$this->title = $title;
		$this->description = $description;
		$this->chief = $chief;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getGroup() {
		return $this->database->getGroup($this->groupId);
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getChief() {
		return $this->database->getUser($this->chief);
	}
	
	/* Returns an array of users that are members of this group */
	public function getMembers() {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[0][9] . ' 
									  LEFT JOIN infecrjn_crew.' . $this->settings->tableList[1][3] . ' ON ' . $this->settings->tableList[0][9] . '.id = userId 
									  WHERE groupId = \'' . $this->getGroup()->getId() . '\' AND teamId = \'' . $this->getId() . '\' 
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
			
			$index = 0;
			
			foreach ($memberList as $member) {
				echo '<div class="';
					
					if ($index % 2 == 0) {
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
					
				$index++;
			}
		}
	}
}
?>