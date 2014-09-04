<?php
require_once 'mailmanager.php';
require_once 'handlers/citydictionary.php';
require_once 'handlers/userpermissionshandler.php';
require_once 'handlers/registrationcodehandler.php';
require_once 'handlers/passwordresetcodehandler.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/emergencycontacthandler.php';
require_once 'handlers/avatarhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'handlers/eventhandler.php';

/*
 * Used to store information about a user.
 */
class User {	
	private $id;
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
	
	public function __construct($id, $firstname, $lastname, $username, $password, $email, $birthdate, $gender, $phone, $address, $postalcode, $nickname) {
		$this->id = $id;
		$this->firstname = $firstname;
		$this->lastname = $lastname;
		$this->username = $username;
		$this->password = $password;
		$this->email = $email;
		$this->birthdate = $birthdate;
		$this->gender = $gender;
		$this->phone = $phone;
		$this->address = $address;
		$this->postalcode = $postalcode;
		$this->nickname = $nickname;
	}
	
	/* 
	 * Returns the users internal id.
	 */
	public function getId() {
		return $this->id;
	}
	
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
		return $this->gender;
	}
	
	/* 
	 * Returns the users gendername.
	 */
	public function getGenderName() {
		return $this->getGender() ? 'Kvinne' : 'Mann';
	}
	
	/* 
	 * Returns the users phone number.
	 */
	public function getPhone() {
		return chunk_split($this->phone, 2, ' ');
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
	 * Returns users fullname.
	 */
	public function getFullName() {
		return $this->getFirstname() . ' ' . $this->getLastname();
	}
	
	/* 
	 * Returns the users age.
	 */
	public function getAge() {
		return date_diff(date_create(date('Y-m-d', $this->getBirthdate())), date_create('now'))->y;
	}
	
	/* 
	 * Returns users displayname.
	 */
	public function getDisplayName() {
		return $this->getFirstname() . ' "' . $this->getNickname() . '" ' . $this->getLastname();
	}
	
	/* 
	 * Returns true if the given users account is activated.
	 */
	public function isActivated() {
		return !RegistrationCodeHandler::hasUserRegistrationCode($this);
	}
	
	/* 
	 * Returns true if user have specified permission, otherwise false.
	 */
	public function hasPermission($value) {
		return UserPermissionsHandler::hasUserPermission($this, $value);
	}
	
	/* 
	 * Returns the permissions assigned to this user.
	 */
	public function getPermissions() {
		return UserPermissionsHandler::getUserPermissions($this);
	}
	
	/*
	 * Returns true if user has an emergency contact linked to this account.
	 */
	public function hasEmergencyContact() {
		return EmergencyContactHandler::getEmergencyContactForUser($this) != null;
	}
	
	/*
	 * Returns emergency contact linked to this account.
	 */
	public function getEmergencyContact() {
		return EmergencyContactHandler::getEmergencyContactForUser($this);
	}
	
	/*
	 * Returns true if user has an ticket for the current/upcoming event.
	 */
	public function hasTicket() {
		return TicketHandler::hasTicket(EventHandler::getCurrentEvent(), $this);
	}
	
	/*
	 * Returns the ticket for the current/upcoming event linked to this account.
	 */
	public function getTicket() {
		return TicketHandler::getTicketForUser(EventHandler::getCurrentEvent(), $this);
	}
	
	/*
	 * Returns true if users has a seat.
	 */
	public function hasSeat() {
		return self::getTicket()->getSeat() != null;
	}
	
	/*
	 * Sends an mail to the users address with an activation link.
	 */
	public function sendRegistrationMail() {
		// Put the code in the database.
		$code = RegistrationCodeHandler::createRegistrationCode($this);
		
		// Send an email to the user with a link for resetting the password.
		$url = 'https://' . $_SERVER['HTTP_HOST'] . '/v2/index.php?page=activation&code=' . $code;
		$message = array();
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>For å aktivere din bruker på ' . $_SERVER['HTTP_HOST'] . ', klikk på <a href="' . $url . '">denne</a> linken.</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';
			
		return MailManager::sendMail($this, 'Infected registrering', implode("\r\n", $message));
	}
	
	/*
	 * Sends a mail to the user with a link where they can reset the password.
	 */
	public function sendPasswordResetMail() {
		// Put the code in the database.
		$code = PasswordResetCodeHandler::createPasswordResetCode($this);
		
		// Send an email to the user with a link for resetting the password.
		$url = 'https://' . $_SERVER['HTTP_HOST'] . '/v2/index.php?page=reset-password&code=' . $code;
		$message = array();
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>For å tilbakestille ditt passord på ' . $_SERVER['HTTP_HOST'] . ', klikk på <a href="' . $url . '">denne</a> linken.</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';
			
		return MailManager::sendMail($this, 'Infected tilbakestilling av passord', implode("\r\n", $message));
	}
	
	/*
	 * Sends a mail to the user that the avatar was accepted or rejected, depening on the accepted boolean.
	 */
	public function sendAvatarMail($accepted) {
		if ($accepted) {
			$text = 'Din avatar på <a href="' . $_SERVER['HTTP_HOST'] . '">' . $_SERVER['HTTP_HOST'] . '</a> har blitt godjent!';
		} else {
			$text = 'Din avatar på <a href="' . $_SERVER['HTTP_HOST'] . '">' . $_SERVER['HTTP_HOST'] . '</a> ble ikke godkjent, vennligst last opp en ny en.';
		}
	
		$message = array();
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>' . $text . '</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';
			
		return MailManager::sendMail($this, 'Infected avatar', implode("\r\n", $message));
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
		return AvatarHandler::getAvatarForUser($this);
	}
	
	/* 
	 * Returns the users group.
	 */
	public function getGroup() {
		return GroupHandler::getGroupForUser($this->getId());
	}
	
	/* 
	 * Is member of a group which means it's not a plain user.
	 */
	public function isGroupMember() {
		return GroupHandler::isGroupMember($this->getId());
	}
	
	/* 
	 * Return true if user is leader of a group.
	 */
	public function isGroupLeader() {
		return GroupHandler::isGroupLeader($this->getId());
	}
	
	/* 
	 * Returns the team.
	 */
	public function getTeam() {
		return TeamHandler::getTeamForUser($this->getId());
	}
	
	/* 
	 * Is member of a team which means it's not a plain user.
	 */
	public function isTeamMember() {
		return TeamHandler::isTeamMember($this->getId());
	}
	
	/*
	 * Return true if user is leader of a team.
	 */
	public function isTeamLeader() {
		return TeamHandler::isTeamLeader($this->getId());
	}
	
	/* 
	 * Returns the name of the users position.
	 */
	public function getPosition() {
		if ($this->isGroupMember()) {
			if ($this->isGroupLeader()) {
				return 'Chief';
			} else if ($this->isTeamLeader()) {
				return 'Shift-leder';
			} else {
				return 'Medlem';
			}
		} else {
			return 'Deltaker';
		}
	}
}
?>