<?php
class Settings {
	// General
	const name = 'Infected Crew';
	const description = 'Infected er et av Akershus største datatreff (LAN-party), og holder til i kulturhuset i Asker kommune.';
	const keywords = 'infected, lan, party, asker, kulturhus, ungdom, gaming';
	const authors = array('halvors', 'petterroea');
	
	// Default page.
	const defaultPage = 'home';

	// Database.
	const db_host = 'localhost';

	const db_name_infected = 'infecrjn_infected';
	const db_name_crew = 'infecrjn_crew';

	const db_prefixList = array('');

	const db_table_events = 'events';
	const db_table_pages = 'pages';
	const db_table_games = 'games';
	const db_table_gameapplications = 'gameapplications';
	const db_table_crews = 'crews';
	const db_table_users = 'users';
	const db_table_permissions = 'permissions';
	const db_table_agendas = 'agendas';
	const db_table_slides = 'slides';
	const db_table_teams = 'teams';
	const db_table_articles = 'articles';
	/*
	const host = 'localhost';
	const databaseList = array('infecrjn_infected', 'infecrjn_crew');
	const prefixList = array('');
	const tableList = array('events', 
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

	//Here is the table array for v7
	// - Liam
	
	/*
		public $tables = array('events', 
					'users', 
					'roles', 
					'pages', 
					'articles', 
					'games', 
					'gameapplications', 
					'agendas', 
					'slides');
	*/
	
	// Create tables
	const tablesSql = array();
	const tablesDataSql = array();
}
?>