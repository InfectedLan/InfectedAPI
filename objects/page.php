<?php
require_once 'objects/object.php';

/*
 * Page used by main page on infected.no
 * 
 * While merging, i found that there are two different pages for the crew page and the main page.
 * This is the main page. The crew page is refactored to CrewPage
*/
class Page extends Object {
	private $name;
	private $title;
	private $content;
	
	public function __construct($id, $name, $title, $content) {
		parent::__construct($id);
		$this->name = $name;
		$this->title = $title;
		$this->content = $content;
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