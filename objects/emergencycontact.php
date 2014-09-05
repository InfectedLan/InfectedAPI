<?php
require_once 'objects/emergencycontact.php';

class EmergencyContact {
	private $id;
	private $userId;
	private $phone;
	
	public function __construct($id, $userId, $phone) {
		$this->id = $id;
		$this->userId = $userId;
		$this->phone = $phone;
	}
	
	/* 
	 * Returns internal id.
	 */
	public function getId() {
		return $this->id;
	}
	
	/* 
	 * Returns associated user.
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}
	
	/* 
	 * Returns the phone number.
	 */
	public function getPhone() {
		return $this->phone;
	}
	
	/* 
	 * Returns the phone number formatted as a string.
	 */
	public function getPhoneString() {
		return !empty($this->phone) ? chunk_split($this->phone, 2, ' ') : 'Ikke oppgitt';
	}
}
?>
