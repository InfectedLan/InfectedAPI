<?php
require_once 'objects/emergencycontact.php';
require_once 'objects/object.php';

class EmergencyContact extends Object {
	private $userId;
	private $phone;
	
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
