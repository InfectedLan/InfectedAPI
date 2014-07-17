<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/Utils.php';

/*
 * SitePage.php
 * Page used by main page on infected.no
 * 
 * While merging, i found that there are two different pages for the crewpage and the main page.
 * This is the main page. The crew page is refactored to CrewPage
*/
class MainPage {
	private $id;
	private $name;
	private $title;
	private $content;
	
	public function MainPage($id, $name, $title, $content) {
		$this->id = $id;
		$this->name = $name;
		$this->title = $title;
		$this->content = $content;
	}
	
	public function display() {
		// Format the page with HTML.
		echo '<div class="contentTitleBox">';
			echo '<h1>';
				/*
					if (Utils::isAuthenticated()) {
						if ($this->utils->hasPermission('admin') || $this->utils->hasPermission('site-admin')) {
							echo '<form name="input" action="index.php?viewPage=edit-page&pageId=' . $this->getId() . '" method="post">';
								echo $this->getTitle();
								echo '<input style="float: right;" type="submit" value="Endre">';
							echo '</form>';
						}
					} else {
						echo $this->getTitle();
					}
				*/
				echo $this->getTitle();
			echo '</h1>';
		echo '</div>';
		
		echo $this->getContent();
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