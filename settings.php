<?php
class Settings {
	// General
	const name = 'Infected Crew';
	const description = 'Infected er et av Akershus største datatreff (LAN-party), og holder til i kulturhuset i Asker kommune.';
	const keywords = 'infected, lan, party, asker, kulturhus, ungdom, gaming';
	public static $authors = array('halvors', 'petterroea');
	
	// Default page.
	const defaultPage = 'home';

	// Database.
	const db_host = 'localhost';

	const db_name_infected = 'infecrjn_infected';
	const db_name_crew = 'infecrjn_crew';
	const db_name_tickets = 'infecrjn_tickets';

	// Fix this, does not support constant arrays... const db_prefixList = array('');

	const db_table_events = 'events';
	const db_table_site_pages = 'pages';
	const db_table_crew_pages = 'pages';
	const db_table_games = 'games';
	const db_table_gameapplications = 'gameapplications';
	const db_table_crews = 'crews';
	const db_table_users = 'users';
	const db_table_permissions = 'permissions';
	const db_table_agendas = 'agendas';
	const db_table_slides = 'slides';
	const db_table_teams = 'teams';
	const db_table_articles = 'articles';
	const db_table_roles = 'roles';
	const db_table_memberof = 'memberof'; //The well known tableList[1][3] from issue #1
	const db_table_groups = 'groups';
	const db_table_avatars = 'avatars';
	const db_table_applications = 'applications';
	const db_table_passresets = 'passresets';
	const db_table_tickets = 'tickets';
	const db_table_seats = 'seats';
	const db_table_sections = 'sections';
	const db_table_ticketTypes = 'ticketTypes';

	const emailName = 'kontakt@infected.no';
	const email = 'kontakt@infected.no';
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
	public $tablesSql = array();
	public $tablesDataSql = array();
}
?>