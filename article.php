<?php
require_once 'database.php';

class Article {	
	private $database;
	
	private $id;
	private $name;
	private $title;
	private $content;
	private $image;
	private $author;
	private $datetime;
	
	public function Article($id, $name, $title, $content, $image, $author, $datetime) {
		$this->id = $id;
		$this->name = $name;
		$this->title = $title;
		$this->content = $content;
		$this->image = $image;
		$this->author = $author;
		$this->datetime = $datetime;
		
		$this->database = new Database();
	}
	
	public function display() {
		$author = $this->database->getUser($this->getAuthor());
		
		if ($author != null) {
			// Format the page with HTML.		
			if (!empty($this->image)) {
				echo '<article>';
					echo '<img class="contentLeftImageBox" src="' . $this->getImage() . '" alt="' . $this->getTitle() . '">';
				echo '</article>';
			}
			
			$position = !empty($this->image) ? 'contentRightBox' : 'contentRightBox';
				
			echo '<article class="' . $position . '">';
				echo '<a href="index.php?viewArticle=' . $this->getName() . '"><h1>' . $this->getTitle() . '</h1></a>';
				echo $this->getContent();
			echo '</article>';
			echo '<article class="contentBox">';
				echo '<p>Skrevet av: <i>' . $author->getFirstname() . ' ' . $author->getLastname() . '</i> on <time>' . $this->getDatetime() . '</time>.<p>';
			echo '</article>';
		}
	}
	
	public function getid() {
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
	
	public function getImage() {
		return $this->image;
	}
	
	public function getAuthor() {
		return $this->author;
	}
	
	public function getDatetime() {
		return $this->datetime;
	}
}
?>