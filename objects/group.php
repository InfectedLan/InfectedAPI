<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/teamhandler.php';

class Group {
	private $id;
	private $name;
	private $title;
	private $description;
	private $chief;
	
	public function Group($id, $name, $title, $description, $chief) {
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
		return UserHandler::getUser($this->chief);
	}
	
	/* Returns an array of users that are members of this group */
	public function getMembers() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_infected_users . ' 
									  LEFT JOIN ' . Settings::db_name_infected_crew . '.' . Settings::db_table_infected_crew_memberof . ' ON ' . Settings::db_table_infected_users . '.id = userId 
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
	
	/* Retuns an array of all teams in this group */
	public function getTeams() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_infected_crew_teams . ' WHERE groupId=\'' . $this->getId() . '\'');
		
		$teamList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($teamList, TeamHandler::getTeam($row['id']));
		}
		
		MySQL::close($con);
		
		return $teamList;
	}
	
	public function getPendingApplications() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$applicationList = array();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_infected_crew_applications . ' WHERE groupId=\'' . $this->getId() . '\' AND state=\'1\'');
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, ApplicationHandler::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $applicationList;
	}
	
	public function displayWithInfo() {
		echo '<div class="crewParagraph">';
			echo '<h1>' . $this->getTitle() . '</h1>';
			echo $this->getDescription();
		echo '</div>';
			
		if (Utils::isAuthenticated()) {
			$this->display();
		}
	}
	
	public function display() {
		$user = Utils::getUser();
		
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