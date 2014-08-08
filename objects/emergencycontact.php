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
	
	public function getId() {
		return $this->id;
	}
	
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}
	
	public function getPhone() {
		return chunk_split($this->phone, 2, ' ');
	}
}
?>
