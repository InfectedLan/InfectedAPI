<?php
class GameApplication {
	private $id;
	private $game;
	private $name;
	private $tag;
	private $contactname;
	private $contactnick;
	private $email;
	private $phone;
	
	public function GameApplication($id, $game, $name, $tag, $contactname, $contactnick, $phone, $email) {
		$this->id = $id;
		$this->game = $game;
		$this->name = $name;
		$this->tag = $tag;
		$this->contactname = $contactname;
		$this->contactnick = $contactnick;
		$this->email = $email;
		$this->phone = $phone;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getGame() {
		return $this->game;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getTag() {
		return $this->tag;
	}	
	
	public function getContactname() {
		return $this->contactname;
	}
	
	public function getContactnick() {	
		return $this->contactnick;
	}
	
	public function getEmail() {
		return $this->email;
	}
	
	public function getPhone() {
		return $this->phone;
	}
}
?>