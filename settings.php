<?php
class Settings {
	// General
	public $name = 'Infected Crew';
	public $description = 'Infected er et av Akershus største datatreff (LAN-party), og holder til i kulturhuset i Asker kommune.';
	public $keywords = 'infected, lan, party, asker, kulturhus, ungdom, gaming';
	public $authors = array('halvors', 'petterroea');
	
	// Default page.
	public $defaultPage = 'home';

	// Database.
	public $db_host = 'localhost';

	public $db_name_infected = 'infecrjn_infected';
	public $db_name_crew = 'infecrjn_crew';

	public $db_prefixList = array('');

	public $db_table_events = 'events';
	public $db_table_pages = 'pages';
	public $db_table_games = 'games';
	public $db_table_gameapplications = 'gameapplications';
	public $db_table_crews = 'crews';
	public $db_table_users = 'users';
	public $db_table_permissions = 'permissions';
	public $db_table_agendas = 'agendas';
	public $db_table_slides = 'slides';
	public $db_table_teams = 'teams';
	/*
	public $host = 'localhost';
	public $databaseList = array('infecrjn_infected', 'infecrjn_crew');
	public $prefixList = array('');
	public $tableList = array('events', 
						'pages', 
						'games', 
						'gameapplications', 
						'crews', 
						'users', 
						'permissions', 
						'agendas', 
						'slides', 
						'teams');
	*/
	
	// Create tables
	public $tablesSql = array();
	public $tablesDataSql = array();
}
?>