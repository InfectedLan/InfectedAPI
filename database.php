<?php
//Crewpage database
require_once 'settings.php';
require_once 'mysql.php';
require_once 'utils.php';

require_once 'user.php';
require_once 'crewPage.php';
require_once 'sitePage.php';

require_once 'application.php';
require_once 'avatar.php';
require_once 'group.php';
require_once 'team.php';

class Database {
	private $settings;
	private $mysql;
	private $utils;
	
	public function __construct() {
		$this->settings = new Settings();
		$this->mysql = new MySQL();
		$this->utils = new Utils();
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

	/*
	 * Slides
	 */

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

	/*
	 * Agenda
	 */

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

	/*
	 * Game applications
	 * This is the backend used on the main site where participants can sign up for game compos.
	 */
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

	/*
	 * Games
	 * This is the different game titles used by games application. Like COD, BF4, Minecraft...
	 */
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

	/*
	 * Article
	 */

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

	/*
	 * Main site page system
	 */

	// Get page.
	public function getSitePage($id) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tables[3] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Page($row['id'], $row['name'], $row['title'], $row['content']);
		}
		
		$this->mysql->close($con);
	}
	
	// Get page.
	public function getSitePageByName($name) {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[3] . ' WHERE name=\'' . $name . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return $this->getPage($row['id']);
		}
		
		$this->mysql->close($con);
	}
	
	// Get a list of all pages
	public function getSitePages() {
		$con = $this->mysql->open();
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tables[3]);
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, $this->getPage($row['id']));
		}
		
		return $pageList;
		
		$this->mysql->close($con);
	}

    /*
     * Role
     */

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

    /*
	 * Event
	 */

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
	
	/*
	 *	User
	 */
	
	/* Get user by id */
	public function getUser($id) {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->db_table_users . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new User($row['id'], 
							$row['firstname'], 
							$row['lastname'], 
							$row['username'], 
							$row['password'], 
							$row['email'], 
							$row['birthDate'], 
							$row['gender'], 
							$row['phone'], 
							$row['address'], 
							$row['postalCode'], 
							$row['nickname']);
		}
		
		$this->mysql->close($con);
	}
	
	/* Get a list of all users */
	public function getUsers() {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->db_table_users);
		
		$userList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($userList, new User($row['id'], 
										   $row['firstname'], 
										   $row['lastname'], 
										   $row['username'], 
										   $row['password'], 
										   $row['email'], 
										   $row['birthDate'], 
										   $row['gender'], 
										   $row['phone'], 
										   $row['address'], 
										   $row['postalCode'], 
										   $row['nickname']));
		}
		
		return $userList;
		
		$this->mysql->close($con);
	}
	
	/* Check if user already exists */
	public function userExists($username) {
		$con = $this->mysql->open($this->settings->db_name_infected);

		$result = mysqli_query($con, 'SELECT EXISTS (SELECT * FROM ' . $this->settings->db_table_users . ' WHERE username=\'' . $username . '\' OR email=\'' . $username . '\')');
		$row = mysqli_fetch_array($result);
		
		$this->mysql->close($con);
		
		return $row ? true : false;
	}
	
	/* Get user by name */
	public function getUserByName($username) {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->db_table_users . ' WHERE username=\'' . $username . '\' OR email=\'' . $username . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return $this->getUser($row['id']);
		}
		
		$this->mysql->close($con);
	}
	
	/* Create new user */
	public function createUser($firstname, $lastname, $username, $password, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->db_table_users . ' (firstname, lastname, username, password, email, birthDate, gender, phone, address, postalCode, nickname) 
							VALUES (\'' . $firstname . '\', 
									\'' . $lastname . '\', 
									\'' . $username . '\', 
									\'' . $password . '\', 
									\'' . $email . '\', 
									\'' . $birthDate . '\', 
									\'' . $gender . '\', 
									\'' . $phone . '\', 
									\'' . $address. '\', 
									\'' . $postalCode . '\', 
									\'' . $nickname . '\')');
									
		$this->mysql->close($con);
	}
	
	/* Remove user */
	public function removeUser($id) {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		mysqli_query($con, 'DELETE FROM ' . $this->settings->db_table_users . ' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	/* Update user */
	public function updateUser($firstname, $lastname, $username, $password, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		mysqli_query($con, 'UPDATE ' . $this->settings->db_table_users . ' 
							SET firstname=\'' . $firstname . '\', 
								lastname=\'' . $lastname . '\', 
								username=\'' . $username . '\', 
								password=\'' . $password . '\', 
								email=\'' . $email . '\', 
								birthDate=\'' . $birthDate . '\', 
								gender=\'' . $gender . '\', 
								phone=\'' . $phone . '\', 
								address=\'' . $address . '\', 
								postalCode=\'' . $postalCode . '\', 
								nickname=\'' . $nickname . '\' 
							WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	/* Get a list of all users which is member in a group */
	public function getMemberUsers() {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->db_table_users . '
									  LEFT JOIN infecrjn_crew.' . $this->settings->tableList[1][3] . ' ON ' . $this->settings->db_table_users . '.id = userId
									  WHERE groupId IS NOT NULL 
									  ORDER BY firstname ASC');
		
		$userList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($userList, new User($row['id'], 
										   $row['firstname'], 
										   $row['lastname'], 
										   $row['username'], 
									       $row['password'], 
									       $row['email'], 
										   $row['birthDate'], 
										   $row['gender'], 
										   $row['phone'], 
										   $row['address'], 
										   $row['postalCode'], 
										   $row['nickname']));
		}
		
		$this->mysql->close($con);
		
		return $userList;
	}
	
	/* Get a list of all users which is not member in a group */
	public function getNonMemberUsers() {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->db_table_users . ' 
									  LEFT JOIN infecrjn_crew.' . $this->settings->tableList[1][3] . ' ON ' . $this->settings->db_table_users . '.id = userId 
									  WHERE groupId IS NULL 
									  ORDER BY firstname ASC');
		
		$userList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($userList, new User($row['id'], 
										   $row['firstname'], 
										   $row['lastname'], 
										   $row['username'], 
									       $row['password'], 
									       $row['email'], 
										   $row['birthDate'], 
										   $row['gender'], 
										   $row['phone'], 
										   $row['address'], 
										   $row['postalCode'], 
										   $row['nickname']));
		}
		
		$this->mysql->close($con);
		
		return $userList;
	}
	
	/*
	 *	Permission
	 */
	
	/* Returns true if user has the givern permission, otherwise false */
	public function hasPermission($userId, $permission) {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		$result = mysqli_query($con, 'SELECT value FROM ' . $this->settings->tableList[0][5] . ' WHERE userId=\'' . $userId . '\' AND value=\'' . $permission . '\'');
		$row = mysqli_fetch_array($result);
		
		$this->mysql->close($con);
		
		return $row ? true : false;
	}
	
	/*
	 *	Page
	 */
	
	/* Get page by id */
	public function getPage($id) {
		if ($this->utils->isAuthenticated()) {
			$user = $this->utils->getUser();
			
			if ($user->isGroupMember()) {
				$con = $this->mysql->open($this->settings->db_name_crew);
				
				if ($user->isTeamMember()) {
					$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->db_table_pages . ' WHERE id=\'' . $id . '\' AND (groupId=\'0\' OR groupId=\'' . $user->getGroup()->getId() . '\') AND (teamId=\'0\' OR teamId=\'' . $user->getTeam()->getId() . '\')');
				} else {
					$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->db_table_pages . ' WHERE id=\'' . $id . '\' AND (groupId=\'0\' OR groupId=\'' . $user->getGroup()->getId() . '\') AND teamId=\'0\'');
				}
				
				$row = mysqli_fetch_array($result);
				
				if ($row) {
					return new Page($row['id'], 
									$row['name'], 
									$row['title'], 
									$row['content'], 
									$row['groupId'], 
									$row['teamId']);
				}
				
				$this->mysql->close($con);
			}
		}
	}
	
	/* Get page by name */
	public function getPageByName($name) {
		if ($this->utils->isAuthenticated()) {
			$user = $this->utils->getUser();
			$con = $this->mysql->open($this->settings->db_name_crew);
			
			$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->db_table_pages . ' WHERE name=\'' . $name . '\'');
			
			$row = mysqli_fetch_array($result);
			
			if ($row) {
				return $this->getPage($row['id']);
			}
			
			$this->mysql->close($con);
		}
	}
	
	/* Get a list of all pages */
	public function getPages() {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->db_table_pages);
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, $this->getPage($row['id']));
		}
		
		return $pageList;
		
		$this->mysql->close($con);
	}
	
	/* Get a list of pages for specified group */
	public function getPagesForGroup($groupId) {
		return $this->getPagesForTeam($groupId, 0);
	}
	
	/* Get a list of pages for specified team */
	public function getPagesForTeam($groupId, $teamId) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->db_table_pages . ' WHERE groupId=\'' . $groupId . '\' AND teamId=\'' . $teamId . '\'');
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, $this->getPage($row['id']));
		}
		
		return $pageList;
		
		$this->mysql->close($con);
	}
	
	/* Create a new page */
	public function createPage($name, $title, $content, $groupId, $teamId) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->db_table_pages . ' (name, title, content, groupId, teamId) 
							VALUES (\'' . $name . '\', 
									\'' . $title . '\', 
									\'' . $content . '\', 
									\'' . $groupId . '\', 
									\'' . $teamId . '\')');
		
		$this->mysql->close($con);
	}
	
	/* Remove a page */
	public function removePage($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'DELETE FROM ' . $this->settings->db_table_pages . ' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	/* Update a page */
	public function updatePage($id, $title, $content) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'UPDATE ' . $this->settings->db_table_pages . ' SET title=\'' . $title . '\', content=\'' . $content . '\' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	/*
	 *	Group
	 */
	
	/* Get a group by id */
	public function getGroup($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[1][2] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Group($row['id'], 
							$row['name'], 
							$row['title'], 
							$row['description'], 
							$row['chief']);
		}
		
		$this->mysql->close($con);
	}
	
	/* Get an array of all groups */
	public function getGroups() {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][2] . ' ORDER BY name');
		
		$groupList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($groupList, $this->getGroup($row['id']));
		}
		
		$this->mysql->close($con);
		
		return $groupList;
	}
	
	/* Create a new group */
	public function createGroup($name, $title, $description, $chief) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tableList[1][2] . ' (name, title, description, chief) 
							VALUES (\'' . $name . '\', 
									\'' . $title . '\', 
									\'' . $description . '\', 
									\'' . $chief . '\')');
		
		$this->mysql->close($con);
	}
	
	/* Remove a page */
	public function removeGroup($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'DELETE FROM ' . $this->settings->tableList[1][2] . ' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	/* Update a page */
	public function updateGroup($id, $name, $title, $description, $chief) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'UPDATE ' . $this->settings->tableList[1][2] . ' SET name=\'' . $name . '\', title=\'' . $title . '\', description=\'' . $description . '\', chief=\'' . $chief . '\' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	/*
	 *	Team
	 */
	
	/* Get a team by id */
	public function getTeam($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[1][6] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Team($row['id'], 
							$row['groupId'], 
							$row['name'], 
							$row['title'], 
							$row['description'], 
							$row['chief']);
		}
		
		$this->mysql->close($con);
	}
	
	/* Get an array of all teams */
	public function getTeams() {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][6]);
		
		$teamList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($teamList, $this->getTeam($row['id']));
		}
		
		$this->mysql->close($con);
		
		return $teamList;
	}
	
	/* Create a new team */
	public function createTeam($groupId, $name, $title, $description, $chief) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tableList[1][6] . ' (groupId, name, title, description, chief) 
							VALUES (\'' . $groupId . '\', 
									\'' . $name . '\', 
									\'' . $title . '\', 
									\'' . $description . '\', 
									\'' . $chief . '\')');
		
		$this->mysql->close($con);
	}
	
	/* Remove a team */
	public function removeTeam($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'DELETE FROM ' . $this->settings->tableList[1][6] . ' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	/* Update a team */
	public function updateTeam($id, $groupId, $name, $title, $description, $chief) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		mysqli_query($con, 'UPDATE ' . $this->settings->tableList[1][6] . ' SET groupId=\'' . $groupId . '\', name=\'' . $name . '\', title=\'' . $title . '\', description=\'' . $description . '\', chief=\'' . $chief . '\' WHERE id=\'' . $id . '\'');
		
		$this->mysql->close($con);
	}
	
	/*
	 *	Avatar
	 */
	
	/* Get a avatar by id */
	public function getAvatar($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[1][1] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Avatar($row['id'], 
							$row['userId'], 
							$row['relativeUrl'], 
							$row['state']);
		}
		
		$this->mysql->close($con);
	}
	
	public function getAvatars() {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][1]);
		
		$avatarList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($groupList, $this->getAvatar($row['id']));
		}
		
		$this->mysql->close($con);
		
		return $avatarList;
	}
	
	public function getPendingAvatars() {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][1] . ' WHERE state=\'1\'');
		
		$avatarList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($groupList, $this->getAvatar($row['id']));
		}
		
		$this->mysql->close($con);
		
		return $avatarList;
	}
	
	public function getAvatarForUser($userId) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][1] . ' WHERE userId=\'' . $userId . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return $this->getAvatar($row['id']);
		}
		
		$this->mysql->close($con);
	}
	
	/*
	 *	Application
	 */
	
	/* Get a application by id */
	public function getApplication($id) {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . $this->settings->tableList[1][0] . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new Application($row['id'], 
								$row['userId'], 
								$row['groupId'], 
								$row['content'], 
								$row['state'], 
								$row['datetime'], 
								$row['reason']);
		}
		
		$this->mysql->close($con);
	}
	
	/* Get a list of all applications */
	public function getApplications() {
		$con = $this->mysql->open($this->settings->db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . $this->settings->tableList[1][0]);
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, $this->getApplication($row['id']));
		}
		
		$this->mysql->close($con);
		
		return $applicationList;
	}
	
	/*
	 *	Password resets
	 */
	
	/* Get a password reset code by userId */
	public function getResetCode($userId) {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		$result = mysqli_query($con, 'SELECT code FROM ' . $this->settings->tableList[0][10] . ' WHERE userId=\'' . $userId . '\'');
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return $row['code'];
		}
		
		$this->mysql->close($con);
	}
	
	/* Set a password reset code for user */
	public function setResetCode($userId, $code) {
		$con = $this->mysql->open($this->settings->db_name_infected);
		
		mysqli_query($con, 'INSERT INTO ' . $this->settings->tableList[0][10] . ' (userId, code) 
							VALUES (\'' . $userId . '\', 
									\'' . $code . '\')');
		
		$this->mysql->close($con);
	}
}
?>