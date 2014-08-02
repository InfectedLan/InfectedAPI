<?php
/*
 * Page.php
 * Page used by main page on infected.no
 * 
 * While merging, i found that there are two different pages for the crewpage and the main page.
 * This is the main page. The crew page is refactored to CrewPage
*/
class Page {
	private $id;
	private $name;
	private $title;
	private $content;
	
	public function __construct($id, $name, $title, $content) {
		$this->id = $id;
		$this->name = $name;
		$this->title = $title;
		$this->content = $content;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getContent() {
		return $this->content;
	}
}
?>