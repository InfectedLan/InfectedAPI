<?php
class Settings {
	// Information
	const name = 'Infected';
	const description = 'Infected er et av Akershus største datatreff (LAN-party), og holder til i kulturhuset i Asker kommune.';
	const keywords = 'infected, lan, party, asker, kulturhus, ungdom, gaming';
	public static $authors = array('halvors', 'petterroea');

	const emailName = 'kontakt@infected.no';
	const email = 'kontakt@infected.no';
	
	const path = 'http://api.infectedlan.tk/';

	const qr_path = '/home/infectedlan.tk/public_html/api/images/qrcache/';
	
	// Database information
	const db_host = 'localhost';
	const db_name_infected = 'infectedlan_tk';
	const db_name_infected_main = 'infectedlan_tk_main';
	const db_name_infected_crew = 'infectedlan_tk_crew';
	const db_name_infected_tickets = 'infectedlan_tk_tickets';

	// Infected
	const db_table_infected_events = 'events';
	const db_table_infected_locations = 'locations';
	const db_table_infected_permissions = 'permissions';
	const db_table_infected_postalcodes = 'postalcodes';
	const db_table_infected_resetcodes = 'resetcodes';
	const db_table_infected_users = 'users';
	
	// InfectedMain
	const db_table_infected_main_agenda = 'agenda';
	const db_table_infected_main_gameapplications = 'gameapplications';
	const db_table_infected_main_games = 'games';
	const db_table_infected_main_pages = 'pages';
	
	// InfectedCrew
	const db_table_infected_crew_applications = 'applications';
	const db_table_infected_crew_avatars = 'avatars';
	const db_table_infected_crew_groups = 'groups';
	const db_table_infected_crew_memberof = 'memberof';
	const db_table_infected_crew_pages = 'pages';
	const db_table_infected_crew_teams = 'teams';
	
	// InfectedTickets
	const db_table_infected_tickets_rows = 'rows';
	const db_table_infected_tickets_seats = 'seats';
	const db_table_infected_tickets_tickets = 'tickets';
	const db_table_infected_tickets_ticketTypes = 'ticketTypes';
}
?>
