<?php
require_once 'objects/object.php';

class Page extends Object {
	protected $name;
	protected $title;
	protected $content;
	
	/*
	 * Returns the name of this page.
	 */
	public function getName() {
		return $this->name;
	}
	
	/*
	 * Returns the title of this page.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/*
	 * Returns the content of this page.
	 */
	public function getContent() {
		return $this->content;
	}
}
?>