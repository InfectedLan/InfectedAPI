<?php
require_once 'session.php';
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/grouphandler.php';

class Team {
	private $id;
	private $groupId;
	private $name;
	private $title;
	private $description;
	private $leader;
	
	public function __construct($id, $groupId, $name, $title, $description, $leader) {
		$this->id = $id;
		$this->groupId = $groupId;
		$this->name = $name;
		$this->title = $title;
		$this->description = $description;
		$this->leader = $leader;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getGroup() {
		return GroupHandler::getGroup($this->groupId);
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
	
	public function getleader() {
		return UserHandler::getUser($this->leader);
	}
	
	/* Returns an array of users that are members of this group */
	public function getMembers() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_infected_users . ' 
									  LEFT JOIN ' . Settings::db_name_infected_crew . '.' . Settings::db_table_infected_crew_memberof . ' ON ' . Settings::db_table_infected_users . '.id = userId 
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
										   $row['birthdate'], 
										   $row['gender'], 
										   $row['phone'], 
										   $row['address'], 
										   $row['postalcode'], 
										   $row['nickname']));
		}
		
		MySQL::close($con);	
		
		return $memberList;
	}
	
	public function displayWithInfo() {
		echo '<div class="crewParagraph">';
			echo '<h1>' . $this->getTitle() . '</h1>';
			echo $this->getDescription();
		echo '</div>';
		
		if (Session::isAuthenticated()) {
			$this->display();
		}
	}
	
	public function display() {
		$user = Session::getCurrentUser();
		
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
					echo '<a href="index.php?page=profile&id=' . $member->getId() . '"><img src="' . $member->getAvatar()->getFile() . '" width="146" height="110" style="float: right;"></a>';
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