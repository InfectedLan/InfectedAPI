<?php
require_once 'session.php';
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';

/*
 * Used to store information about a group.
 */
class Group {
	private $id;
	private $name;
	private $title;
	private $description;
	private $leader;
	
	public function __construct($id, $name, $title, $description, $leader) {
		$this->id = $id;
		$this->name = $name;
		$this->title = $title;
		$this->description = $description;
		$this->leader = $leader;
	}
	
	/* 
	 * Returns the internal id for this group.
	 */
	public function getId() {
		return $this->id;
	}
	
	/* 
	 * Returns the name of this group.
	 */
	public function getName() {
		return $this->name;
	}
	
	/*
	 * Returns the title of this group.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/* 
	 * Returns the description of this group.
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/* 
	 * Returns the user which is the leader of this group. 
	 */
	public function getLeader() {
		return UserHandler::getUser($this->leader);
	}
	
	/* 
	 * Returns an array of users that are member of this group. 
	 */
	public function getMembers() {
		return GroupHandler::getMembers($this->getId());
	}
	
	/* 
	 * Returns an array of all teams connected to this group.
	 */
	public function getTeams() {		
		return TeamHandler::getTeamsForGroup($this->getId());
	}
	
	public function displayWithInfo() {
		echo '<div class="crewParagraph">';
			echo '<h3>' . $this->getTitle() . '</h3>';
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
			
			$i = 0;
			
			foreach ($memberList as $member) {
				echo '<div class="';
					
					if ($i % 2 == 0) {
						echo 'crewEntryLeft';
					} else {
						echo 'crewEntryRight';
					}
				echo '">';
					$avatarFile = null;
			
					if ($member->hasValidAvatar()) {
						$avatarFile = $member->getAvatar()->getThumbnail();
					} else {
						$avatarFile = AvatarHandler::getDefaultAvatar($member);
					}
				
					echo '<a href="index.php?page=profile&id=' . $member->getId() . '"><img src="../api/' . $avatarFile . '" width="146" height="110" style="float: right;"></a>';
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