<?php
class Settings {
	// Information
	const name = 'Infected';
	const description = 'Infected er et av Akershus største datatreff (LAN-party), og holder til i kulturhuset i Asker kommune.';
	const keywords = 'infected, lan, party, asker, kulturhus, ungdom, gaming';
	public static $authors = array('halvors', 'petterroea');
	
	const api_path = '/home/test.infected.no/public_html/api/';
	const qr_path = 'content/qrcache/';
	
	const emailName = 'Infected';
	const email = 'no-reply@infected.no';

	const storeSessionTime = 3600; //You have an hour to pay on paypal
	
	// Database information
	const db_host = 'localhost';
	const db_name_infected = 'test_infected_no';
	const db_name_infected_main = 'test_infected_no_main';
	const db_name_infected_crew = 'test_infected_no_crew';
	const db_name_infected_tickets = 'test_infected_no_tickets';

	// Infected
	const db_table_infected_emergencycontacts = 'emergencycontacts';
	const db_table_infected_events = 'events';
	const db_table_infected_locations = 'locations';
	const db_table_infected_passwordresetcodes = 'passwordresetcodes';
	const db_table_infected_permissions = 'permissions';
	const db_table_infected_postalcodes = 'postalcodes';
	const db_table_infected_registrationcodes = 'registrationcodes';
	const db_table_infected_userpermissions = 'userpermissions';
	const db_table_infected_users = 'users';
	
	// InfectedMain
	const db_table_infected_main_agenda = 'agenda';
	const db_table_infected_main_gameapplications = 'gameapplications';
	const db_table_infected_main_games = 'games';
	const db_table_infected_main_pages = 'pages';
	const db_table_infected_main_slides = 'slides';
	
	// InfectedCrew
	const db_table_infected_crew_applications = 'applications';
	const db_table_infected_crew_avatars = 'avatars';
	const db_table_infected_crew_groups = 'groups';
	const db_table_infected_crew_memberof = 'memberof';
	const db_table_infected_crew_pages = 'pages';
	const db_table_infected_crew_teams = 'teams';
	
	// InfectedTickets
	const db_table_infected_tickets_entrances = 'entrances';
	const db_table_infected_tickets_paymentlog = 'paymentlog';
	const db_table_infected_tickets_rows = 'rows';
	const db_table_infected_tickets_seatmaps = 'seatmaps';
	const db_table_infected_tickets_seats = 'seats';
	const db_table_infected_tickets_storesessions = 'storesessions';
	const db_table_infected_tickets_tickets = 'tickets';
	const db_table_infected_tickets_tickettypes = 'tickettypes';
}
?>
