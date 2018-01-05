<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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
require_once 'handlers/friendhandler.php';
require_once 'handlers/networkhandler.php';
require_once 'objects/databaseobject.php';

class User extends DatabaseObject {
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
	private $registerdate;

	/*
	 * Returns the users firstname.
	 */
	public function getFirstname(): string {
		return $this->firstname;
	}

	/*
	 * Returns the users lastname.
	 */
	public function getLastname(): string {
		return $this->lastname;
	}

	/*
	 * Returns the users username
	 */
	public function getUsername(): string {
		return $this->username;
	}

	/*
	 * Returns the users password as a SHA-256 hash.
	 */
	public function getPassword(): string {
		return $this->password;
	}

	/*
	 * Returns the users email address.
	 */
	public function getEmail(): string {
		return $this->email;
	}

	/*
	 * Returns the users birthdate.
	 */
	public function getBirthdate(): int {
		return strtotime($this->birthdate);
	}

	/*
	 * Returns the users gender.
	 */
	public function getGender(): bool {
		return $this->gender == 0 ? true : false;
	}

	/*
	 * Returns the users gendername.
	 */
	public function getGenderAsString(): string {
		if ($this->getAge() < 18) {
			return Localization::getLocale($this->getGender() ? 'boy' : 'girl');
		}

		return Localization::getLocale($this->getGender() ? 'male' : 'female');
	}

	/*
	 * Returns the users phone number, if hidden it return zero.
	 */
	public function getPhone(): int {
		return !$this->hasPrivatePhone() ? $this->phone : 0;
	}

	/*
	 * Returns the users phone number formatted as a string.
	 */
	public function getPhoneAsString(): string {
		return rtrim('(+47) ' . chunk_split($this->getPhone(), 2, ' ')); // TODO: Determine this based on country area code.
	}

	/*
	 * Returns the users address.
	 */
	public function getAddress(): string {
		return $this->address;
	}

	/*
	 * Returns the users postalcode.
	 */
	public function getPostalCode(): string {
		return sprintf('%04u', $this->postalcode);
	}

	/*
	 * Returns the users city, based on the postalcode.
	 */
	public function getCity(): string {
		return CityDictionary::getCity($this->getPostalCode());
	}

	/*
	 * Returns the users nickname.
	 */
	public function getNickname(): string {
		return $this->nickname;
	}

	/*
	 * Returns the date which this user was registered.
	 */
	public function getRegisterDate(): int {
		return strtotime($this->registerdate);
	}

	/*
	 * Returns users fullname.
	 */
	public function getFullName(): string {
		return $this->getFirstname() . ' ' . $this->getLastname();
	}

	/*
	 * Returns users displayname.
	 */
	public function getDisplayName(): string {
		return $this->getFirstname() . ' "' . $this->getUsername() . '" ' . $this->getLastname();
	}

	/*
	 * Returns the users age.
	 */
	public function getAge(Event $event = null): int {
		$birthdate = new DateTime(date('Y-m-d', $this->getBirthdate()));
		$from = $event != null ? new DateTime(date('Y-m-d', $event->getStartTime())) : new DateTime('now');

		return $birthdate->diff($from)->y;
	}

	/*
	 * Returns true if the given users account is activated.
	 */
	public function isActivated(): bool {
		return !RegistrationCodeHandler::hasRegistrationCodeByUser($this);
	}

	/*
	 * Returns true if the given users phone number is private.
	 */
	public function hasPrivatePhone(): bool {
		return UserOptionHandler::hasUserPrivatePhone($this);
	}

	/*
	 * Returns true if the given users phone number is private.
	 */
	public function isReservedFromNotifications(): bool {
		return UserOptionHandler::isUserReservedFromNotifications($this);
	}

	/*
	 * Returns true if user has easter egg.
	 */
	public function hasEasterEgg(): bool {
		return UserOptionHandler::hasUserEasterEgg($this);
	}

	/*
	 * Returns true if user have specified permission, otherwise false.
	 */
	public function hasPermission(string $value): bool {
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
	public function getPermissions(): array {
		$permissionList = UserPermissionHandler::getUserPermissions($this);

		// Give access to default permission for certain users.
		if ($this->isGroupMember()) {
			$permissionList[] = PermissionHandler::getPermissionByValue('event.checklist');

		  // Give leaders access to permissions by default.
		  if ($this->isGroupLeader()) {
		    $permissionList[] = PermissionHandler::getPermissionByValue('chief.*');
			// Give team leaders access to permissions by default.
			}

			if ($this->isTeamMember() && $this->isTeamLeader()) {
		    $permissionList[] = PermissionHandler::getPermissionByValue('chief.team');
				$permissionList[] = PermissionHandler::getPermissionByValue('event.checklist');
		  }
		}

		return $permissionList;
	}

	/*
	 * Returns true if user has an emergency contact linked to this account.
	 */
	public function hasEmergencyContact(): bool {
		return EmergencyContactHandler::hasEmergencyContactByUser($this);
	}

	/*
	 * Returns emergency contact linked to this account.
	 */
	public function getEmergencyContact(): EmergencyContact {
		return EmergencyContactHandler::getEmergencyContactByUser($this);
	}

	public function getFriends(): array {
		return FriendHandler::getFriendsByUser($this);
	}

	public function isFriendsWith(User $friend): bool {
		return FriendHandler::isUserFriendsWith($this, $friend);
	}

	public function addFriend(User $friend) {
		FriendHandler::addUserFriend($this, $friend);
	}

	public function removeFriend(User $friend) {
		FriendHandler::removeUserFriend($this, $friend);
	}

	/*
	 * Returns true if user has an ticket for the current/upcoming event.
	 */
	public function hasTicket(Event $event = null): bool {
		return TicketHandler::hasTicketByUser($this, $event);
	}

	/*
	 * Returns the first ticket for the current/upcoming event is found for this user.
	 */
	public function getTicket(Event $event = null): Ticket {
		return TicketHandler::getTicketByUser($this, $event);
	}

	/*
	 * Returns the tickets for the current/upcoming event linked to this account.
	 */
	public function getTickets(Event $event = null): array {
		return TicketHandler::getTicketsByUser($this, $event);
	}

	public function hasTicketsByAllEvents(): bool {
		return TicketHandler::hasTicketsByUserAndAllEvents($this);
	}

	public function getTicketsByAllEvents(): array {
		return TicketHandler::getTicketsByUserAndAllEvents($this);
	}

	/*
	 * Returns true if users has a seat.
	 */
	public function hasSeat(): bool {
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
		$url = 'https://' . $_SERVER['HTTP_HOST'] . '/v2/index.php?page=password-reset&code=' . $code;
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
				$message[] = '<p>Med vennlig hilsen <a href="' . $_SERVER['HTTP_HOST'] . '">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';

		return MailManager::sendEmail($this, 'Infected avatar', implode("\r\n", $message));
	}

	/*
	 * Returns true if the user has an avatar.
	 */
	public function hasAvatar(): bool {
		return AvatarHandler::hasAvatar($this);
	}

	/*
	 * Returns true if the user has an successfully cropped avatar.
	 */
	public function hasCroppedAvatar(): bool {
		return AvatarHandler::hasCroppedAvatar($this);
	}

	/*
	 * Returns true if the user has an accpeted avatar.
	 */
	public function hasValidAvatar(): bool {
		return AvatarHandler::hasValidAvatar($this);
	}

	/*
	 * Returns the avatar linked to this user.
	 */
	public function getAvatar(): Avatar {
		return AvatarHandler::getAvatarByUser($this);
	}

	/*
	 * Returns the default avatar, determined by gender of this user.
	 */
	public function getDefaultAvatar(): string {
		return AvatarHandler::getDefaultAvatar($this);
	}

	/*
	 * Is member of a group.
	 */
	public function isGroupMember(Event $event = null): bool {
		return GroupHandler::isGroupMember($this, $event);
	}

	/*
	 * Return true if user is leader of a group.
	 */
	public function isGroupLeader(Event $event = null): bool {
		return GroupHandler::isGroupLeader($this, $event);
	}

	/*
	 * Returns the users group.
	 */
	public function getGroup(Event $event = null): Group {
		return GroupHandler::getGroupByUser($this, $event);
	}

	/*
	 * Is member of a team.
	 */
	public function isTeamMember(Event $event = null): bool {
		return TeamHandler::isTeamMember($this, $event);
	}

	/*
	 * Return true if user is leader of a team.
	 */
	public function isTeamLeader(Event $event = null): bool {
		return TeamHandler::isTeamLeader($this, $event);
	}

	/*
	 * Returns the team.
	 */
	public function getTeam(Event $event = null): Team {
		return TeamHandler::getTeamByUser($this, $event);
	}

	public function getParticipatedEvents(): array {
		return UserHistoryHandler::getParticipatedEvents($this);
	}

	public function hasSpecialRole(Event $event = null): bool {
		if ($this->isGroupMember($event)) {
			return $this->isGroupLeader($event) ||
						($this->isTeamMember($event) && $this->isTeamLeader($event));
		}

		return false;
	}

	/*
	 * Returns the name of the users position.
	 */
	public function getRole(Event $event = null): string {
		if ($this->isGroupMember($event)) {
			$group = $this->getGroup($event);

			if ($this->isGroupLeader($event)) {
				return 'Leder i ' . $group->getTitle();
			} else if ($this->isTeamMember($event) &&
				$this->isTeamLeader($event)) {
				$team = $this->getTeam($event);

				// Check if the user is leader of this team.
				if ($team->isLeader($this, $event)) {
					return 'Lag-leder i ' . $group->getTitle() . ":" . $team->getTitle();
				}
			}

			return 'Medlem';
		} else if ($this->hasTicket($event)) {
			return 'Deltaker';
		}

		return 'Ingen';
	}

	/*
	 * Returns true if this user has a note.
	 */
	public function hasNote(): bool {
		return UserNoteHandler::hasUserNoteByUser($this);
	}

	/*
	 * Returns the note for this user.
	 */
	public function getNote(): ?string {
		return UserNoteHandler::getUserNoteByUser($this);
	}

	/*
	 * Sets the note for this user.
	 */
	public function setNote(string $content) {
		UserNoteHandler::setUserNote($this, $content);
	}

	/*
	 * Return true if this user have network acces to the given port type.
	 */
	public function hasNetworkAccess(NetworkType $networkType): bool {
		return NetworkHandler::hasNetworkAccess($this, $networkType);
	}

	/*
	 * Get network for this user.
	 */
	public function getNetwork(NetworkType $networkType): Network {
		return NetworkHandler::getNetworkByUser($this, $networkType);
	}

	/*
	 * Returns true if user is eligible to play in a infected compo
	 */
	public function isEligibleForCompos(): bool {
		return $this->hasTicket() || $this->isGroupMember();
	}

	public function isEligibleForPreSeating(): bool {
		return count(TicketHandler::getTicketsSeatableByUser($this)) >= Settings::prioritySeatingReq;
	}

	/*
	 * Returns the steam id of this user. Null if not existent
	 */
	public function getSteamId(): ?string {
		return UserHandler::getSteamId($this);
	}

	/*
	 * Sets the users steam id
	 */
	public function setSteamId(string $steamId) {
		UserHandler::setSteamId($this, $steamId);
	}
}