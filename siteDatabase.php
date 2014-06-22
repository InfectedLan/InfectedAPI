<?php
require_once 'settings.php';
require_once 'mysql.php';

require_once 'event.php';
require_once 'page.php';
require_once 'game.php';
require_once 'gameApplication.php';
require_once 'agenda.php';
require_once 'slide.php';

class SiteDatabase {
	private $settings;
	private $mysql;
	
	public function SiteDatabase() {
        $this->settings = new Settings();
		$this->mysql = new MySQL();
    }
	
	// Get event.
	public function getEvent($id) {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[0][1] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Event($row['id'], 
							$row['theme'], 
							$row['participants'], 
							$row['price'], 
							$row['start'], 
							$row['end']);
		}
		
		$this->mysql->close($con);
	}
	
	// Get a list of all events.
	public function getEvents() {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[0][1]);
		
		$eventList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($eventList, $this->getEvent($row['id']));
		}
		
		return $eventList;
		
		$this->mysql->close($con);
	}
	
	public function createEvent($theme, $participants, $price, $start, $end) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tableList[0][1] . ' (theme, participants, price, start, end) 
							VALUES (\'' . $theme . '\', 
									\'' . $participants . '\', 
									\'' . $price . '\', 
									\'' . $start . '\', 
									\'' . $end . '\')');
		
		$this->mysql->close($con);
	}
	
	public function removeEvent($id) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'DELETE FROM ' . $this->settings->tableList[0][1] . ' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	public function updateEvent($id, $theme, $participants, $price, $start, $end) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'UPDATE ' . $this->settings->tableList[0][1] . ' 
							SET theme=\'' . $theme . '\', 
								participants=\'' . $participants . '\', 
								price=\'' . $price . '\', 
								start=\'' . $start . '\', 
								end=\'' . $end . '\' 
							WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	public function getPage($id) {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[0][4] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Page($row['id'], 
							$row['name'], 
							$row['title'], 
							$row['content'], 
							null, 
							null);
		}
		
		$this->mysql->close($con);
	}
	
	public function getPageByName($name) {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[0][4] . ' WHERE name=\'' . $name . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return $this->getPage($row['id']);
		}
		
		$this->mysql->close($con);
	}
	
	// Get a list of all pages
	public function getPages() {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[0][4]);
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, $this->getPage($row['id']));
		}
		
		return $pageList;
		
		$this->mysql->close($con);
	}
	
	public function createPage($name, $title, $content) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tableList[0][4] . ' (name, title, content) 
							VALUES (\'' . $name . '\', 
									\'' . $title . '\', 
									\'' . $content . '\')');
		
		$this->mysql->close($con);
	}
	
	public function removePage($id) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'DELETE FROM ' . $this->settings->tableList[0][4] . ' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	public function updatePage($id, $title, $content) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'UPDATE ' . $this->settings->tableList[0][4] . ' 
							SET title=\'' . $title . '\', 
								content=\'' . $content . '\' 
							WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	public function getGame($id) {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[0][3] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Game($row['id'], 
							$row['name'], 
							$row['title'], 
							$row['price'], 
							$row['mode'], 
							$row['description'], 
							$row['deadline'], 
							$row['published']);
		}
		
		$this->mysql->close($con);
	}
	
	public function getGames() {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[0][3]);
		$gameList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($gameList, $this->getGame($row['id']));
		}
		
		return $gameList;
		
		$this->mysql->close($con);
	}
	
	public function createGame($name, $title, $price, $mode, $description, $deadline, $published) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tableList[0][3] . ' (name, title, price, mode, description, deadline, published) 
							VALUES (\'' . $name . '\', 
									\'' . $title . '\', 
									\'' . $price . '\', 
									\'' . $mode . '\', 
									\'' . $description . '\', 
									\'' . $deadline . '\', 
									\'' . $published . '\')');
		
		$this->mysql->close($con);
	}
	
	public function removeGame($id) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'DELETE FROM ' . $this->settings->tableList[0][3] . ' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	public function updateGame($id, $name, $title, $price, $mode, $description, $deadline, $published) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'UPDATE ' . $this->settings->tableList[0][3] . ' 
							SET name=\'' . $name . '\', 
								title=\'' . $title . '\', 
								price=\'' . $price . '\', 
								mode=\'' . $mode . '\', 
								description=\'' . $description . '\', 
								deadline=\'' . $deadline . '\', 
								published=\'' . $published . '\' 
							WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	public function getGameApplication($id) {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[0][2] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new GameApplication($row['id'], 
									$row['game'], 
									$row['name'], 
									$row['tag'], 
									$row['contactname'], 
									$row['contactnick'], 
									$row['phone'], 
									$row['email']);
		}
		
		$this->mysql->close($con);
	}
	
	public function getGameApplications($game) {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[0][2] . ' WHERE game=\'' . $game . '\'');
		$gameApplicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($gameApplicationList, $this->getGameApplication($row['id']));
		}
		
		return $gameApplicationList;
		
		$this->mysql->close($con);
	}
	
	public function createGameApplication($game, $name, $tag, $contactname, $contactnick, $phone, $email) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tableList[0][2] . ' (game, name, tag, contactname, contactnick, phone, email) 
							VALUES (\'' . $game . '\', 
									\'' . $name . '\', 
									\'' . $tag . '\', 
									\'' . $contactname . '\', 
									\'' . $contactnick . '\', 
									\'' . $phone . '\', 
									\'' . $email . '\')');
		
		$this->mysql->close($con);
	}
	
	public function removeGameApplication($id) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'DELETE FROM ' . $this->settings->tableList[0][2] . ' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	public function getAgenda($id) {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[0][0] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Agenda($row['id'], 
							$row['datetime'], 
							$row['name'], 
							$row['description']);
		}
		
		$this->mysql->close($con);
	}
	
	public function getAgendas() {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[0][0] . ' ORDER BY datetime');
		$agendaList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($agendaList, $this->getAgenda($row['id']));
		}
		
		return $agendaList;
		
		$this->mysql->close($con);
	}
	
	public function createAgenda($datetime, $name, $description) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tableList[0][0] . ' (datetime, name, description) 
							VALUES (\'' . $datetime . '\', 
									\'' . $name . '\', 
									\'' . $description . '\')');
		
		$this->mysql->close($con);
	}
	
	public function removeAgenda($id) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'DELETE FROM ' . $this->settings->tableList[0][0] . ' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	public function updateAgenda($id, $datetime, $name, $description) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'UPDATE ' . $this->settings->tableList[0][0] . ' 
							SET datetime=\'' . $datetime . '\', 
								name=\'' . $name . '\', 
								description=\'' . $description . '\' 
							WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	public function getSlide($id) {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[0][8] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Slide($row['id'], 
							$row['start'], 
							$row['end'], 
							$row['title'], 
							$row['content'], 
							$row['published']);
		}
		
		$this->mysql->close($con);
	}
	
	public function getSlides() {
		$con = $this->mysql->open(0);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[0][8]);
		$slideList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($slideList, $this->getSlide($row['id']));
		}
		
		return $slideList;
		
		$this->mysql->close($con);
	}
	
	public function createSlide($start, $end, $title, $content, $published) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tableList[0][8] . ' (start, end, title, content, published) 
							VALUES (\'' . $start . '\', 
									\'' . $end . '\', 
									\'' . $title . '\', 
									\'' . $content . '\', 
									\'' . $published . '\')');
		
		$this->mysql->close($con);
	}
	
	public function removeSlide($id) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'DELETE FROM ' . $this->settings->tableList[0][8] . ' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	public function updateSlide($id, $start, $end, $title, $content, $published) {
		$con = $this->mysql->open(0);
		
		mysqli_query($con, 'UPDATE ' . $this->settings->tableList[0][8] . ' 
							SET start=\'' . $start . '\', 
								end=\'' . $end . '\', 
								title=\'' . $title . '\', 
								content=\'' . $content . '\', 
								published=\'' . $published . '\' 
							WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
}
?>