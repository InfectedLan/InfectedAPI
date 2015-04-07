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
		return TeamHandler::getMembers($this);
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