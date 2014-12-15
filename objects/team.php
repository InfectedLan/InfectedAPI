<?php
require_once 'session.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'handlers/userhandler.php';
require_once 'objects/object.php';

class Team extends Object {
	private $groupId;
	private $name;
	private $title;
	private $description;
	private $leader;
	
	public function __construct($id, $groupId, $name, $title, $description, $leader) {
		parent::__construct($id);
		
		$this->groupId = $groupId;
		$this->name = $name;
		$this->title = $title;
		$this->description = $description;
		$this->leader = $leader;
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
	
	public function getLeader() {
		return UserHandler::getUser($this->leader);
	}
	
	/* Returns an array of users that are members of this group */
	public function getMembers() {
		return TeamHandler::getMembers($this->groupId, $this->id);
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
						echo 'Stilling: ' . $member->getPosition() . '<br>';
						echo 'Telefon: ' . $member->getPhoneString() . '<br>';
						echo 'E-post: ' . $member->getEmail() . '</p>';
					echo '</div>';
						
					$index++;
				}
			} else {
					echo '<p>Det er ingen medlemmer av dette laget.</p>';
			}
		}
	}
}
?>