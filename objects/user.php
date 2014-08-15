<?php
require_once 'mailmanager.php';
require_once 'handlers/citydictionary.php';
require_once 'handlers/permissionshandler.php';
require_once 'handlers/registrationcodehandler.php';
require_once 'handlers/passwordresetcodehandler.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/emergencycontacthandler.php';
require_once 'handlers/avatarhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';

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
		return $this->getGender() ? "Kvinne" : "Mann";
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
	 * Returns true if user have specified permission, otherwise false.
	 */
	public function hasPermission($value) {
		return PermissionsHandler::hasPermission($this, $value);
	}
	
	/* 
	 * Returns the permissions assigned to this user.
	 */
	public function getPermissions() {
		return PermissionsHandler::getPermissions($this);
	}
	
	/* 
	 * Returns true if the given users account is activated.
	 */
	public function isActivated() {
		return RegistrationCodeHandler::getRegistrationCode($this) == null;
	}
	
	/*
	 * Sends an mail to the users address with an activation link.
	 */
	public function sendRegistrationMail() {
		// Put the code in the database.
		$code = RegistrationCodeHandler::createRegistrationCode($this);
		
		// Send an email to the user with a link for resetting the password.
		$url = 'https://' . $_SERVER['HTTP_HOST'] . '/v2/index.php?page=activation&code=' . $code;
		$message = '<html>' .
						'<body>' .
							'<h3>Hei!</h3>' .
							'<p>For å aktivere din bruker på ' . $_SERVER['HTTP_HOST'] . ', trenger du bare å klikke på <a href="' . $url . '">denne</a> linken.</p>' .
						'</body>' .
					'</html>';
			
		return MailManager::sendMail($this, 'Infected brukerregistrering', $message);
	}
	
	/*
	 * Sends a mail to the user with a link where they can reset the password.
	 */
	public function sendPasswordResetMail() {
		// Put the code in the database.
		$code = PasswordResetCodeHandler::createPasswordResetCode($this);
		
		// Send an email to the user with a link for resetting the password.
		$url = 'https://' . $_SERVER['HTTP_HOST'] . '/v2/index.php?page=reset-password&code=' . $code;
		$message = '<html>' .
						'<body>' .
							'<h3>Hei!</h3>' .
							'<p>For å tilbakestille passordet ditt på ' . $_SERVER['HTTP_HOST'] . ', trenger du bare å klikke på <a href="' . $url . '">denne</a> linken.</p>' .
						'</body>' .
					'</html>';
			
		return MailManager::sendMail($this, 'Infected tilbakestilling av passord', $message);
	}
	
	public function hasEmergencyContact() {
		return EmergencyContactHandler::getEmergencyContactForUser($this) != null;
	}
	
	public function getEmergencyContact() {
		return EmergencyContactHandler::getEmergencyContactForUser($this);
	}
	
	public function hasTicket() {
		return TicketHandler::hasTicket($this);
	}
	
	public function getTicket() {
		return TicketHandler::getTicketForUser($this);
	}
	
	/* 
	 * Returns users displayname.
	 */
	public function getDisplayName() {
		$nickname = $this->getNickname();
	
		if (!empty($nickname)) {
			$displayName = $this->getFirstname() . ' "' . $nickname  . '" ' . $this->getLastname();
		} else {
			$displayName = $this->getFirstname() . ' ' . $this->getLastname();
		}
		
		return $displayName;
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
	
	public function getAvatar() {
		return AvatarHandler::getAvatarForUser($this->getId());
	}
	
	public function getPendingAvatar() {
		return AvatarHandler::getPendingAvatarForUser($this->getId());
	}
	
	public function hasAvatar() {
		return AvatarHandler::getAvatarForUser($this->getId()) != null;
	}
	
	public function hasPendingAvatar() {
		return AvatarHandler::getPendingAvatarForUser($this->getId()) != null;
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
}
?>