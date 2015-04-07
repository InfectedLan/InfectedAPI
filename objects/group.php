<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'objects/eventobject.php';

/*
 * Used to store information about a group.
 */
class Group extends EventObject {
	private $name;
	private $title;
	private $description;
	private $leaderId;
	private $coleaderId;
	private $queuing;
	
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
		return UserHandler::getUser($this->leaderId);
	}
	
	/* 
	 * Returns the user which is the co-leader of this group. 
	 */
	public function getCoLeader() {
		return UserHandler::getUser($this->coleaderId);
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
		return TeamHandler::getTeamsByGroup($this);
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
						
						echo 'Telefon: ' . $member->getPhoneAsString() . '<br>';
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