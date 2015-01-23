<?php
require_once 'session.php';
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'objects/object.php';

/*
 * Used to store information about a group.
 */
class Group extends Object {
	private $name;
	private $title;
	private $description;
	private $leader;
	private $coleader;
	private $queuing;
	
	public function __construct($id, $name, $title, $description, $leader, $coleader, $queuing) {
		parent::__construct($id);
	
		$this->name = $name;
		$this->title = $title;
		$this->description = $description;
		$this->leader = $leader;
		$this->coleader = $coleader;
		$this->queuing = $queuing;
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
	 * Returns the user which is the co-leader of this group. 
	 */
	public function getCoLeader() {
		return UserHandler::getUser($this->coleader);
	}
	
	/*
	 * Return true if new applications for this group automatically should be queued.
	 */
	public function isQueuing() {
		return $this->queuing ? true : false;
	}
	
	/* 
	 * Returns an array of users that are member of this group. 
	 */
	public function getMembers() {
		return GroupHandler::getMembers($this);
	}
	
	/* 
	 * Returns an array of all teams connected to this group.
	 */
	public function getTeams() {		
		return TeamHandler::getTeamsForGroup($this);
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
			
			if (!empty($memberList)) {
				$index = 0;
				
				foreach ($memberList as $member) {
					echo '<div class="';
						
						if ($index % 2 == 0) {
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
					
						echo '<a href="index.php?page=my-profile&id=' . $member->getId() . '"><img src="../api/' . $avatarFile . '" width="146" height="110" style="float: right;"></a>';
						echo '<p>Navn: ' . $member->getFirstname() . ' "' . $member->getNickname() . '" ' . $member->getLastname() . '<br>';
						echo 'Stilling: ';
						
						if ($member->isGroupLeader()) {
							echo 'Chief';
						} else if ($member->isGroupCoLeader()) {
							echo 'Co-chief';
						} else if ($member->isTeamMember()) {
							if ($member->isTeamLeader()) {
								echo 'Shift-leder i ' . $member->getTeam()->getTitle();
							} else {
								echo $member->getTeam()->getTitle();
							}
						} else {
							echo 'Medlem';
						}
						
						echo '<br>';
						
						echo 'Telefon: ' . $member->getPhoneString() . '<br>';
						echo 'E-post: ' . $member->getEmail() . '</p>';
					echo '</div>';
						
					$index++;
				}
			} else {
				echo '<p>Det er ingen medlemmer av dette crewet.</p>';
			}
		}
	}
}
?>