<?php
require_once 'session.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'handlers/userhandler.php';
require_once 'objects/eventobject.php';

class Team extends EventObject {
	private $groupId;
	private $name;
	private $title;
	private $description;
	private $leaderId;
	
	/*
	 * Returns the group for this team.
	 */
	public function getGroup() {
		return GroupHandler::getGroup($this->groupId);
	}
	
	/*
	 * Returns the name of this team.
	 */
	public function getName() {
		return $this->name;
	}
	
	/*
	 * Returns the title of this team.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/*
	 * Return the description for this team.
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/*
	 * Returns the leader of this team.
	 */
	public function getLeader() {
		return UserHandler::getUser($this->leaderId);
	}
	
	/* 
	 * Returns an array of users that are members of this group.
	 */
	public function getMembers() {
		return TeamHandler::getMembers($this->getGroup(), $this);
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
						echo '<p>Navn: ' . $member->getDisplayName() . '<br>';

						if ($member->isGroupLeader()) {
							echo 'Stilling: Chief<br>';
						} else if ($member->isGroupCoLeader()) {
							echo 'Stilling: Co-chief<br>';
						} else if ($member->isTeamMember() && $member->isTeamLeader()) {
							echo 'Stilling: Shift-leder<br>';
						}

						echo 'Telefon: ' . $member->getPhoneAsString() . '<br>';
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