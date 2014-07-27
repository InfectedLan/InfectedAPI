<?php
require_once 'handlers/citydictionary.php';
require_once 'handlers/permissionshandler.php';

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
	
	/* Returns the users internal id as int */
	public function getId() {
		return $this->id;
	}
	
	/* Returns the users firstname as string */
	public function getFirstname() {
		return $this->firstname;
	}
	
	/* Returns the users lastname as string */
	public function getLastname() {
		return $this->lastname;
	}
	
	/* Returns the users username as string */
	public function getUsername() {
		return $this->username;
	}
	
	/* Returns the users password as a sha256 hash. */
	public function getPassword() {
		return $this->password;
	}
	
	/* Returns the users email address as string */
	public function getEmail() {
		return $this->email;
	}
	
	/* Returns the users birthDate as timestamp */
	public function getBirthdate() {
		return strtotime($this->birthdate);
	}
	
	/* Returns the users gender as boolean */
	public function getGender() {
		return $this->gender;
	}
	
	/* Returns the users gender as string */
	public function getGenderName() {
		return $this->getGender() ? "Kvinne" : "Mann";
	}
	
	/* Returns the users phone number spaces every second number as string */
	public function getPhone() {
		return chunk_split($this->phone, 2, ' ');
	}
	
	/* Returns the users address as a string */
	public function getAddress() {
		return $this->address;
	}
	
	/* Returns the users postalCode as int */
	public function getPostalCode() {
		return sprintf('%04u', $this->postalcode);
	}
	
	/* Returns the users city as string, based on the postalCode */
	public function getCity() {
		return CityDictionary::getCity($this->getPostalCode());
	}
	
	/* Returns the users nickname as string */
	public function getNickname() {
		return $this->nickname;
	}
	
	/* Returns users fullname as string */
	public function getFullName() {
		return $this->getFirstname() . ' ' . $this->getLastname();
	}
	
	/* Returns users displayName as string */
	public function getDisplayName() {
		$nickname = $this->getNickname();
	
		if (!empty($nickname)) {
			$displayName = $this->getFirstname() . ' "' . $nickname  . '" ' . $this->getLastname();
		} else {
			$displayName = $this->getFirstname() . ' ' . $this->getLastname();
		}
		
		return $displayName;
	}
	
	/* Returns the users age as int */
	public function getAge() {
		return date_diff(date_create(date('Y-m-d', $this->getBirthdate())), date_create('now'))->y;
	}
	
	/* Returns true if user have specified permission, otherwise false */
	public function hasPermission($permission) {
		return PermissionsHandler::hasPermission($this->getId(), $permission);
	}
	
	/* Return a string with the name of the position */
	public function getPosition() {
		if ($this->isGroupMember()) {
			if ($this->isGroupChief()) {
				return 'Chief';
			} else if ($this->isTeamChief()) {
				return 'Shift-leder';
			} else {
				return 'Medlem';
			}
		} else {
			return 'Deltaker';
		}
	}
	
		/*
	 * Sends a mail to the user with a link where they can reset the password.
	 */
	public function sendForgottenMail() {
		$code = md5($this->getId() + time() * rand());
		
		// Put the code in the database.
		ResetCodeHandler::setResetCode($this->getId(), $code);
		
		// Send an email to the user with a link for resetting the password.
		$url = 'https://' . $_SERVER['HTTP_HOST'] . 'index.php?page=reset-code=' . $code;
		$message = '<html>' .
						'<head>' .
							'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' .
						'</head>' .
						'<body>' .
							'<h3>Hei!</h3>' .
							'<p>For å tilbakestille passordet ditt må du klikke <a href="' . $url . '">her</a>.</p>' .
						'</body>' .
					'</html>';
			
		return MailManager::sendMail($this, 'Infected.no - Tilbakestill passord', $message);
	}
	
	public function getAvatar() {
		return self::hasAvatar() ? AvatarHandler::getAvatarForUser($this) : null;
	}
	
	public function getPendingAvatar() {
		return self::hasPendingAvatar() ? AvatarHandler::getPendingAvatarForUser($this) : null;
	}
	
	public function hasAvatar() {
		$avatar = AvatarHandler::getAvatarForUser($this);
	
		return $avatar->getState() == 2;
	}
	
	public function hasPendingAvatar() {
		return AvatarHandler::getPendingAvatarForUser($this) != null;
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