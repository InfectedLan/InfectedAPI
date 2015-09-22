<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'mailmanager.php';
require_once 'localization.php';
require_once 'handlers/useroptionhandler.php';
require_once 'handlers/citydictionary.php';
require_once 'handlers/registrationcodehandler.php';
require_once 'handlers/permissionhandler.php';
require_once 'handlers/userpermissionhandler.php';
require_once 'handlers/emergencycontacthandler.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/passwordresetcodehandler.php';
require_once 'handlers/avatarhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'handlers/userhistoryhandler.php';
require_once 'handlers/usernotehandler.php';
require_once 'objects/object.php';

class User extends Object {
	private $firstname;
	private $lastname;
	private $username;
	private $password;
	private $email;
	private $birthdate;
	private $gender;
	private $phone;
	private $address;
	private $postalcode;
	private $nickname;
	private $registereddate;

	/*
	 * Returns the users firstname.
	 */
	public function getFirstname() {
		return $this->firstname;
	}

	/*
	 * Returns the users lastname.
	 */
	public function getLastname() {
		return $this->lastname;
	}

	/*
	 * Returns the users username
	 */
	public function getUsername() {
		return $this->username;
	}

	/*
	 * Returns the users password as a sha256 hash.
	 */
	public function getPassword() {
		return $this->password;
	}

	/*
	 * Returns the users email address.
	 */
	public function getEmail() {
		return $this->email;
	}

	/*
	 * Returns the users birthdate.
	 */
	public function getBirthdate() {
		return strtotime($this->birthdate);
	}

	/*
	 * Returns the users gender.
	 */
	public function getGender() {
		return $this->gender == 0 ? true : false;
	}

	/*
	 * Returns the users gendername.
	 */
	public function getGenderAsString() {
		if ($this->getAge() < 18) {
			return Localization::getLocale($this->getGender() ? 'boy' : 'girl');
		}

		return Localization::getLocale($this->getGender() ? 'male' : 'female');
	}

	/*
	 * Returns the users phone number, if hidden it return zero.
	 */
	public function getPhone() {
		return !$this->hasPrivatePhone() ? $this->phone : 0;
	}

	/*
	 * Returns the users phone number formatted as a string.
	 */
	public function getPhoneAsString() {
		return rtrim('(+47) ' . chunk_split($this->getPhone(), 2, ' '));
	}

	/*
	 * Returns the users address.
	 */
	public function getAddress() {
		return $this->address;
	}

	/*
	 * Returns the users postalcode.
	 */
	public function getPostalCode() {
		return sprintf('%04u', $this->postalcode);
	}

	/*
	 * Returns the users city, based on the postalcode.
	 */
	public function getCity() {
		return CityDictionary::getCity($this->getPostalCode());
	}

	/*
	 * Returns the users nickname.
	 */
	public function getNickname() {
		return $this->nickname;
	}

	/*
	 * Returns the date which this user was registered.
	 */
	public function getRegisteredDate() {
		return strtotime($this->registereddate);
	}

	/*
	 * Returns users fullname.
	 */
	public function getFullName() {
		return $this->getFirstname() . ' ' . $this->getLastname();
	}

	/*
	 * Returns users displayname.
	 */
	public function getDisplayName() {
		return $this->getFirstname() . ' "' . $this->getUsername() . '" ' . $this->getLastname();
	}

	/*
	 * Returns the users age.
	 */
	public function getAge() {
		$birthdate = new DateTime(date('Y-m-d', $this->getBirthdate()));
		$now = new DateTime('now');

		return $birthdate->diff($now)->y;
	}

	/*
	 * Returns true if the given users account is activated.
	 */
	public function isActivated() {
		return !RegistrationCodeHandler::hasRegistrationCodeByUser($this);
	}

	/*
	 * Returns true if the given users phone number is private.
	 */
	public function hasPrivatePhone() {
		return UserOptionHandler::hasUserPrivatePhone($this);
	}

	/*
	 * Returns true if the given users phone number is private.
	 */
	public function isReservedFromNotifications() {
		return UserOptionHandler::isUserReservedFromNotifications($this);
	}

	/*
	 * Returns true the user is set for swimming.
	 */
	public function isSwimming() {
		return UserOptionHandler::isUserSwimming($this);
	}

	/*
	 * Set whether user is swimming or not.
	 */
	public function setSwimming($swimming) {
		UserOptionHandler::setUserSwimming($this, $swimming);
	}

	/*
	 * Returns true if user has easter egg.
	 */
	public function hasEasterEgg() {
		return UserOptionHandler::hasUserEasterEgg($this);
	}

	/*
	 * Returns true if user have specified permission, otherwise false.
	 */
	public function hasPermission($value) {
		// Match wildcard permissions, if value is admin.permissions and user has permission "admin.*" this would return true.
		$wildcardValue = preg_replace('/[^\.]([^.]*)$/', '*', $value);

		// This makes sure that if user have a child permissions, that it also matches the parent.
		// i.e "admin.permissions" would also match for just "admin"
		foreach ($this->getPermissions() as $permission) {
			$permissionValue = $permission->getValue();

			// Accept permission if user has a god permission ("*") or a valid wildcard.
			if ($permissionValue == '*' ||
				$permissionValue == $wildcardValue ||
				preg_replace('/[\.*](.*)/', '', $permissionValue) == $value ||
				$permissionValue == $value) {
				return true;
			}
		}

		return false;
	}

	/*
	 * Returns the permissions assigned to this user.
	 */
	public function getPermissions() {
		$permissionList = UserPermissionHandler::getUserPermissions($this);

		// Give access to default permission for certain users.
		if ($this->isGroupMember()) {
			array_push($permissionList, PermissionHandler::getPermissionByValue('event.checklist'));

		  // Give leaders access to permissions by default.
		  if ($this->isGroupLeader() || $this->isGroupCoLeader()) {
		    array_push($permissionList, PermissionHandler::getPermissionByValue('chief.*'));
			// Give team leaders access to permissions by default.
			}

			if ($this->isTeamMember() && $this->isTeamLeader()) {
		    array_push($permissionList, PermissionHandler::getPermissionByValue('chief.team'));
				array_push($permissionList, PermissionHandler::getPermissionByValue('event.checklist'));
		  }
		}

		return $permissionList;
	}

	/*
	 * Returns true if user has an emergency contact linked to this account.
	 */
	public function hasEmergencyContact() {
		return EmergencyContactHandler::hasEmergencyContactByUser($this);
	}

	/*
	 * Returns emergency contact linked to this account.
	 */
	public function getEmergencyContact() {
		return EmergencyContactHandler::getEmergencyContactByUser($this);
	}

	/*
	 * Returns true if user has an ticket for the current/upcoming event.
	 */
	public function hasTicket() {
		return TicketHandler::hasTicketByUser($this);
	}

	/*
	 * Returns true if user has an ticket for the specified event.
	 */
	public function hasTicketByEvent(Event $event) {
		return TicketHandler::hasTicketByUserAndEvent($this, $event);
	}

	/*
	 * Returns the first ticket for the current/upcoming event is found for this user.
	 */
	public function getTicket() {
		return TicketHandler::getTicketByUser($this);
	}

	/*
	 * Returns the first ticket for the specified event is found for this user.
	 */
	public function getTicketByEvent(Event $event) {
		return TicketHandler::getTicketByUserAndEvent($this, $event);
	}

	/*
	 * Returns the tickets for the current/upcoming event linked to this account.
	 */
	public function getTickets() {
		return TicketHandler::getTicketsByUser($this);
	}

	/*
	 * Returns the tickets for the current/upcoming event linked to this account.
	 */
	public function getTicketsByEvent(Event $event) {
		return TicketHandler::getTicketsByUserAndEvent($this, $event);
	}

	public function hasTicketsByAllEvents() {
		return TicketHandler::hasTicketsByUserAndAllEvents($this);
	}

	public function getTicketsByAllEvents() {
		return TicketHandler::getTicketsByUserAndAllEvents($this);
	}

	/*
	 * Returns true if users has a seat.
	 */
	public function hasSeat() {
		return self::getTicket()->isSeated();
	}

	/*
	 * Sends an mail to the users address with an activation link.
	 */
	public function sendRegistrationEmail() {
		// Put the code in the database.
		$code = RegistrationCodeHandler::createRegistrationCode($this);

		// Send an email to the user with a link for resetting the password.
		$url = 'https://' . $_SERVER['HTTP_HOST'] . '/v2/index.php?page=activation&code=' . $code;
		$message = [];
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>For å aktivere din bruker på ' . $_SERVER['HTTP_HOST'] . ', klikk på denne: <a href="' . $url . '">' . $url . '</a>.</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';

		return MailManager::sendEmail($this, 'Infected registrering', implode("\r\n", $message));
	}

	/*
	 * Sends a mail to the user with a link where they can reset the password.
	 */
	public function sendPasswordResetEmail() {
		// Put the code in the database.
		$code = PasswordResetCodeHandler::createPasswordResetCode($this);

		// Send an email to the user with a link for resetting the password.
		$url = 'https://' . $_SERVER['HTTP_HOST'] . '/v2/index.php?page=reset-password&code=' . $code;
		$message = [];
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>For å tilbakestille ditt passord på ' . $_SERVER['HTTP_HOST'] . ', klikk på denne linken: <a href="' . $url . '">' . $url . '</a>.</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';

		return MailManager::sendEmail($this, 'Infected tilbakestilling av passord', implode("\r\n", $message));
	}

	/*
	 * Sends a mail to the user that the avatar was accepted or rejected, depening on the accepted boolean.
	 */
	public function sendAvatarEmail($isAccepted) {
		if ($isAccepted) {
			$text = 'Din avatar på <a href="' . $_SERVER['HTTP_HOST'] . '">' . $_SERVER['HTTP_HOST'] . '</a> har blitt godjent!';
		} else {
			$text = 'Din avatar på <a href="' . $_SERVER['HTTP_HOST'] . '">' . $_SERVER['HTTP_HOST'] . '</a> ble ikke godkjent, vennligst last opp en ny en.';
		}

		$message = [];
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>' . $text . '</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';

		return MailManager::sendEmail($this, 'Infected avatar', implode("\r\n", $message));
	}

	/*
	 * Returns true if the user has an avatar.
	 */
	public function hasAvatar() {
		return AvatarHandler::hasAvatar($this);
	}

	/*
	 * Returns true if the user has an successfully cropped avatar.
	 */
	public function hasCroppedAvatar() {
		return AvatarHandler::hasCroppedAvatar($this);
	}

	/*
	 * Returns true if the user has an accpeted avatar.
	 */
	public function hasValidAvatar() {
		return AvatarHandler::hasValidAvatar($this);
	}

	/*
	 * Returns the avatar linked to this user.
	 */
	public function getAvatar() {
		return AvatarHandler::getAvatarByUser($this);
	}

	/*
	 * Returns the default avatar, determined by gender of this user.
	 */
	public function getDefaultAvatar() {
		return AvatarHandler::getDefaultAvatar($this);
	}

	/*
	 * Returns the users group for the fiven event.
	 */
	public function getGroupByEvent(Event $event) {
		return GroupHandler::getGroupByUserAndEvent($this, $event);
	}

	/*
	 * Returns the users group.
	 */
	public function getGroup() {
		return GroupHandler::getGroupByUser($this);
	}

	/*
	 * Is member of a group for the given event.
	 */
	public function isGroupMemberByEvent(Event $event) {
		return GroupHandler::isGroupMemberByEvent($this, $event);
	}

	/*
	 * Is member of a group.
	 */
	public function isGroupMember() {
		return GroupHandler::isGroupMember($this);
	}

	/*
	 * Return true if user is leader of a group for the given event.
	 */
	public function isGroupLeaderByEvent(Event $event) {
		return GroupHandler::isGroupLeaderByEvent($this, $event);
	}

	/*
	 * Return true if user is leader of a group.
	 */
	public function isGroupLeader() {
		return GroupHandler::isGroupLeader($this);
	}

	/*
	 * Return true if user is co-leader of a group for the given event.
	 */
	public function isGroupCoLeaderByEvent(Event $event) {
		return GroupHandler::isGroupCoLeaderByEvent($this, $event);
	}

	/*
	 * Return true if user is co-leader of a group.
	 */
	public function isGroupCoLeader() {
		return GroupHandler::isGroupCoLeader($this);
	}

	/*
	 * Returns the team for the given event.
	 */
	public function getTeamByEvent(Event $event) {
		return TeamHandler::getTeamByUserAndEvent($this, $event);
	}

	/*
	 * Returns the team.
	 */
	public function getTeam() {
		return TeamHandler::getTeamByUser($this);
	}

	/*
	 * Is member of a team for the given event.
	 */
	public function isTeamMemberByEvent(Event $event) {
		return TeamHandler::isTeamMemberByEvent($this, $event);
	}

	/*
	 * Is member of a team.
	 */
	public function isTeamMember() {
		return TeamHandler::isTeamMember($this);
	}

	/*
	 * Return true if user is leader of a team for the given event.
	 */
	public function isTeamLeaderByEvent(Event $event) {
		return TeamHandler::isTeamLeaderByEvent($this, $event);
	}

	/*
	 * Return true if user is leader of a team.
	 */
	public function isTeamLeader() {
		return TeamHandler::isTeamLeader($this);
	}

	public function getParticipatedEvents() {
		return UserHistoryHandler::getUserParticipatedEvents($this);
	}

	public function hasSpecialRoleByEvent(Event $event) {
		if ($this->isGroupMemberByEvent($event)) {
			return $this->isGroupLeaderByEvent($event) ||
						$this->isGroupCoLeaderByEvent($event) ||
						($this->isTeamMemberByEvent($event) && $this->isTeamLeaderByEvent($event));
		}

		return false;
	}

	public function hasSpecialRole() {
		return $this->hasSpecialRoleByEvent(EventHandler::getCurrentEvent());
	}

	/*
	 * Returns the name of the users position.
	 */
	public function getRoleByEvent(Event $event) {
		if ($this->isGroupMemberByEvent($event)) {
			$group = $this->getGroupByEvent($event);

			if ($this->isGroupLeaderByEvent($event)) {
				return 'Leder i ' . $group->getTitle();
			} else if ($this->isGroupCoLeaderByEvent($event)) {
				return 'Co-leder i ' . $group->getTitle();
			} else if ($this->isTeamMemberByEvent($event) &&
				$this->isTeamLeaderByEvent($event)) {
				$team = $this->getTeam();

				return 'Lag-leder i ' . $group->getTitle() . ":" . $team->getTitle();
			}

			return 'Medlem';
		} else if ($this->hasTicketByEvent($event)) {
			return 'Deltaker';
		}

		return 'Ingen';
	}

	/*
	 * Returns the name of the users position.
	 */
	public function getRole() {
		return $this->getRoleByEvent(EventHandler::getCurrentEvent());
	}

	/*
	 * Returns true if this user has a note.
	 */
	public function hasNote() {
		return UserNoteHandler::hasUserNoteByUser($this);
	}

	/*
	 * Returns the note for this user.
	 */
	public function getNote() {
		return UserNoteHandler::getUserNoteByUser($this);
	}

	/*
	 * Sets the note for this user.
	 */
	public function setNote($content) {
		UserNoteHandler::setUserNote($this, $content);
	}

	/*
	 * Returns true if user is eligible to play in a infected compo
	 */
	public function isEligibleForCompos() {
		return $this->hasTicket() || $this->isGroupMember();
	}

	/*
	 * Returns the full name with nickname instead of username for use in compos.
	 */
	public function getCompoDisplayName() {
		return $this->getFirstname() . ' "' . $this->getNickname() . '" ' . $this->getLastname();
	}
}
?>
