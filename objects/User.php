<?php
require_once '/../Settings.php';
require_once '/../MySQL.php';
require_once '/../handlers/PermissionsHandler.php';
require_once '/../objects/Avatar.php';

class User {	
	private $id;
	private $firstname;
	private $lastname;
	private $username;
	private $password;
	private $email;
	private $birthDate;
	private $gender;
	private $phone;
	private $address;
	private $postalCode;
	private $nickname;
	
	public function User($id, $firstname, $lastname, $username, $password, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
		$this->id = $id;
		$this->firstname = $firstname;
		$this->lastname = $lastname;
		$this->username = $username;
		$this->password = $password;
		$this->email = $email;
		$this->birthDate = $birthDate;
		$this->gender = $gender;
		$this->phone = $phone;
		$this->address = $address;
		$this->postalCode = $postalCode;
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
		return strtotime($this->birthDate);
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
		return sprintf('%04u', $this->postalCode);
	}
	
	/* Returns the users city as string, based on the postalCode */
	public function getCity() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT city FROM ' . Settings::db_table_postalcodes . ' WHERE code = \'' . $this->getPostalCode() . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return ucfirst(strtolower($row['city']));
		}
	}
	
	/* Returns the users nickname as string */
	public function getNickname() {
		return $this->nickname;
	}
	
	/* Returns the users age as int */
	public function getAge() {
		return date('Y') - date('Y', $this->getBirthdate());
	}
	
	/* Linked tables database functions */
	
	/* Returns the users avatar image link as string */
	public function getAvatar() {
		return $this->getAvatarType('sd');
	}

	public function getThumbnailAvatar() {
		return $this->getAvatarType('thumbnail');
	}
	
	public function getHqAvatar() {
		return $this->getAvatarType('hg');
	}
	
	public function getAvatarType($type) {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT relativeUrl FROM ' . Settings::db_table_avatars . ' WHERE userId = \'' . $this->getId() . '\' AND state = \'2\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			$relativeUrl = $type . '/' . $row['relativeUrl'];
		} else {
			if ((date('Y') - date('Y', $this->getBirthdate())) > 18) {
				if ($this->getGender() == 0) {
					$relativeUrl = 'default.png';
				} else {
					$relativeUrl = 'default_jente.png';
				}
			} else {
				$relativeUrl = 'default18.png';
			}
		}
		
		MySQL::close($con);
		
		return new Avatar(null, $this->getId(), 'images/avatars/' . $relativeUrl, null);
	}
	
	public function getPendingAvatar() {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT relativeUrl FROM ' . Settings::db_table_avatars . ' WHERE userId = \'' . $this->getId() . '\' AND state = \'1\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			$relativeUrl = 'sd/' . $row['relativeUrl'];
		} else {
			if ((date('Y') - date('Y', $this->getBirthdate())) > 18) {
				if ($this->getGender() == 0) {
					$relativeUrl = 'default.png';
				} else {
					$relativeUrl = 'default_jente.png';
				}
			} else {
				$relativeUrl = 'default18.png';
			}
		}
		
		MySQL::close($con);
		
		return new Avatar(null, $this->getId(), 'images/avatars/' . $relativeUrl, null);
	}
	
	public function hasPendingAvatar() {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT state FROM ' . Settings::db_table_avatars . ' WHERE userId = \'' . $this->getId() . '\' AND state = \'1\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	public function hasAvatar() {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT state FROM ' . Settings::db_table_avatars . ' WHERE userId = \'' . $this->getId() . '\' AND state = \'2\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	/* Returns the users group */
	public function getGroup() {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT groupId FROM ' . Settings::db_table_memberof . ' WHERE userId = \'' . $this->getId() . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return $this->database->getGroup($row['groupId']);
		}
		
		MySQL::close($con);
	}
	
	/* Sets the users group */
	public function setGroup($groupId) {
		$con = MySQL::open(Settings::db_name_crew);
		
		if ($this->isGroupMember) {	
			mysqli_query($con, 'UPDATE ' . Settings::db_table_memberof . ' SET groupId = \'' . $groupId . '\', teamId = \'0\' WHERE userId = \'' . $this->getId() . '\'');
		} else {
			mysqli_query($con, 'INSERT INTO ' . Settings::db_table_memberof . ' (userId, groupId, teamId) VALUES (\'' . $this->getId() . '\', \'' . $groupId . '\', \'0\')');
		}
		
		MySQL::close($con);
	}
	
	/* Is member of a group which means it's not a plain user */
	public function isGroupMember() {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT groupId FROM ' . Settings::db_table_memberof . ' WHERE userId = \'' . $this->getId() . '\' AND groupId != \'0\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	/* Return true if user is chief for a group */
	public function isGroupChief() {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT chief FROM ' . Settings::db_table_groups . ' WHERE chief = \'' . $this->getId() . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	/* Returns the users team */
	public function getTeam() {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT teamId FROM ' . Settings::db_table_memberof . ' WHERE userId = \'' . $this->getId() . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return $this->database->getTeam($row['teamId']);
		}
	}
	
	/* Sets the users team */
	public function setTeam($teamId) {
		$con = MySQL::open(Settings::db_name_crew);
		
		if ($this->isGroupMember) {	
			mysqli_query($con, 'UPDATE ' . Settings::db_table_memberof . ' SET teamId = \'' . $teamId . '\' WHERE userId = \'' . $this->getId() . '\' AND groupId = \'' . $this->getGroup()->getId() . '\'');	
		}
		
		MySQL::close($con);
	}
	
	/* Is member of a team which means it's not a plain user */
	public function isTeamMember() {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT teamId FROM ' . Settings::db_table_memberof. ' WHERE userId = \'' . $this->getId() . '\' AND teamId != \'0\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	/* Return true if user is chief for a team */
	public function isTeamChief() {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT chief FROM ' . Settings::db_table_teams . ' WHERE chief = \'' . $this->getId() . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	/* Returns true if user have specified permission, otherwise false */
	public function hasPermission($permission) {
		return PermissionsHandler::hasPermission($this->getId(), $permission);
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
	
	public function sendForgottenEmail() {
		$code = md5($this->getId() + time() * rand());
		
		// Put the code in the database.
		$this->database->setResetCode($this->getId(), $code);
		
		// Send an email to the user with a link for resetting the password.
		$url = 'https://' . $_SERVER['HTTP_HOST'] . 'index.php?page=reset&code=' . $code;
		$message = '<html>
						<head>
							<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
						</head>
						<body>
							<h3>Hei!</h3>
							<p>For å tilbakestille passordet ditt må du klikke <a href="' . $url . '">her</a>.</p>
						</body>
					</html>';
			
		return Utils::sendEmail($this, 'Infected.no - Tilbakestill passord', $message);
	}
}
?>