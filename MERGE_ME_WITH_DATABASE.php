<?php
//Main page database
require_once 'mysql.php';

require_once 'event.php';
require_once 'user.php';
require_once 'role.php';
require_once 'page.php';
require_once 'article.php';
require_once 'game.php';
require_once 'gameApplication.php';
require_once 'agenda.php';
require_once 'slide.php';

class Database {
	private $mysql;
	private $settings;
	
	public function Database() {
		$this->mysql = new MySQL();
        $this->settings = new Settings();
    }
	
	// Get event.
	public function getEvent($id) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tables[0] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Event($row['id'], $row['theme'], $row['participants'], $row['price'], $row['start'], $row['end']);
		}
		
		$this->mysql->close($con);
	}
	
	// Get a list of all events.
	public function getEvents() {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[0]);
		
		$eventList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($eventList, $this->getEvent($row['id']));
		}
		
		return $eventList;
		
		$this->mysql->close($con);
	}
	
	// Get a role by id.
	public function getRole($id) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tables[2] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Role($row['id'], $row['name']);
		}
		
		$this->mysql->close($con);
	}
	
	// Get a list of all roles.
	public function getRoles() {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[2]);
		
		$roleList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($roleList, $this->getRole($row['id']));
		}
		
		return $roleList;
		
		$this->mysql->close($con);
	}
	
	// Compatibility with petterroea's work.
	public function getPermission($username, $value) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT value FROM permissions WHERE username=\'' . $username . '\' AND value=\'' . $value . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return true;
		}
		
		$this->mysql->close($con);
		
		return false;
	}
	
	// Get page.
	public function getPage($id) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tables[3] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Page($row['id'], $row['name'], $row['title'], $row['content']);
		}
		
		$this->mysql->close($con);
	}
	
	// Get page.
	public function getPageByName($name) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[3] . ' WHERE name=\'' . $name . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return $this->getPage($row['id']);
		}
		
		$this->mysql->close($con);
	}
	
	// Get a list of all pages
	public function getPages() {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[3]);
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, $this->getPage($row['id']));
		}
		
		return $pageList;
		
		$this->mysql->close($con);
	}
	
	// Get article.
	public function getArticle($id) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tables[4] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Article($row['id'], $row['name'], $row['title'], $row['content'], $row['image'], $row['author'], $row['datetime']);
		}
		
		$this->mysql->close($con);
	}
	
	// Get article by name article.
	public function getArticleByName($name) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[4] . ' WHERE name=\'' . $name . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return $this->getArticle($row['id']);
		}
		
		$this->mysql->close($con);
	}
	
	// Get a list of all articles.
	public function getArticles() {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[4]);
		$articleList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($articleList, $this->getArticle($row['id']));
		}
		
		return $articleList;
		
		$this->mysql->close($con);
	}
	
	public function getGame($id) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tables[5] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Game($row['id'], $row['name'], $row['title'], $row['price'], $row['mode'], $row['description'], $row['deadline'], $row['published']);
		}
		
		$this->mysql->close($con);
	}
	
	public function getGames() {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[5]);
		$gameList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($gameList, $this->getGame($row['id']));
		}
		
		return $gameList;
		
		$this->mysql->close($con);
	}
	
	public function getPublishedGames() {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[5] . ' WHERE published=\'1\'');
		$gameList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($gameList, $this->getGame($row['id']));
		}
		
		return $gameList;
		
		$this->mysql->close($con);
	}
	
	public function getGameApplication($id) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tables[6] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new GameApplication($row['id'], $row['game'], $row['name'], $row['tag'], $row['contactname'], $row['contactnick'], $row['phone'], $row['email']);
		}
		
		$this->mysql->close($con);
	}
	
	public function getGameApplications($game) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[6] . ' WHERE game=\'' . $game . '\'');
		$gameApplicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($gameApplicationList, $this->getGameApplication($row['id']));
		}
		
		return $gameApplicationList;
		
		$this->mysql->close($con);
	}
	
	public function createGameApplication($game, $name, $tag, $contactname, $contactnick, $phone, $email) {
		$con = $this->mysql->open();
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tables[6] . ' (game, name, tag, contactname, contactnick, phone, email) VALUES (\'' . $game . '\', \'' . $name . '\', \'' . $tag . '\', \'' . $contactname . '\', \'' . $contactnick . '\', \'' . $phone . '\', \'' . $email . '\')');
		
		$this->mysql->close($con);
	}
	
	public function getAgenda($id) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tables[7] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Agenda($row['id'], $row['datetime'], $row['name'], $row['description']);
		}
		
		$this->mysql->close($con);
	}
	
	public function getAgendas() {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[7] . ' ORDER BY datetime');
		$agendaList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($agendaList, $this->getAgenda($row['id']));
		}
		
		return $agendaList;
		
		$this->mysql->close($con);
	}
	
	public function getAgendasBetween($first, $last) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[7] . ' ORDER BY datetime');
		$agendaList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			$agenda = $this->getAgenda($row['id']);
			$now = date('U');
			
			if ($agenda->getDatetime() >= $now - $first * 60 * 60 ||
				$agenda->getDatetime() + (60*90) >= $now + $last * 60 * 60) {
				array_push($agendaList, $agenda);
			}
		}
		
		return $agendaList;
		
		$this->mysql->close($con);
	}
	
	public function getSlide($id) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tables[8] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Slide($row['id'], $row['start'], $row['end'], $row['title'], $row['content'], $row['published']);
		}
		
		$this->mysql->close($con);
	}
	
	public function getSlides() {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[8] . ' ORDER BY start');
		$slideList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			$slide = $this->getSlide($row['id']);
			$now = date('U');
			
			if ($slide->getStart() >= $now - $first * 60 * 60 ||
				$slide->getEnd() >= $now + $last * 60 * 60) {
				array_push($slideList, $slide);
			}
		}
		
		return $slideList;
		
		$this->mysql->close($con);
	}
}
?>