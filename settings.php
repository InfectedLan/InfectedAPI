<?php
class Settings {
	/* Metadata */
	const name = 'Infected';
	const description = 'Infected er et av Akershus største datatreff (LAN-party), og holder til i kulturhuset i Asker kommune.';
	const keywords = 'infected, lan, party, asker, kulturhus, ungdom, gaming';
	public static $authors = array('halvors', 'petterroea');
	
	/* Database */
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
	
	/* Permissions */
    public static $permissions = array('*' 				    	   => 'Gir tilgang til absolutt alt, du er nå en udødelig administrator.',
									   'functions.search-users'    => 'Søk etter brukere i databasen.',
									   'functions.my-crew' 		   => 'Endre sider under "My Crew".',
									   'functions.info'			   => 'Post informasjon på infoskjermen som vil vises under arrangementet.',
									   'functions.site-list-games' => 'Administrer spill, settes opp compo i nye spill og moderer påmeldinger.',
									   'functions.site-list-pages' => 'Endre innholdet på nettsiden.',
									   'chief.home' 			   => 'Endre "home" siden."',
									   'chief.groups' 			   => 'Administrer crewene, medlemmene i dem og hvem som er ledere.',
									   'chief.teams' 			   => 'Administrer lagene for ditt crew, hvem som er medlem av hvilke lag og lederene i lagene.',
									   'chief.avatars'			   => 'Administrer profilbilder, alle nye profilbilder vil dukke opp her og du kan godjkenne dem eller avise.',
									   'chief.applications'		   => 'Godta eller avvis crew-søknader.',
									   'admin.events' 			   => 'Administrer arrangementer, informasjon er blir automatisk endret på hovedsiden og ticketsiden.',
									   'admin.permissions' 		   => 'Velg hvilke bruker som skal ha tilgang til hva, dette er et tilgangsystem, men husk at brukere har standard tilganger utenom dette, ettersom de er medlem av crewet eller ikke.',
									   'admin.change-user' 		   => 'Lar deg logge inn som hvilken som helst annen bruker, kun beregnet for bruk ved feilsøking.',
									   'admin.seatmap' 			   => 'Endre seatmappet, her kan du flytte, legg til, og fjerne seter og rader.');
	
	/* Configuration */
	// Full path to the API location.
	const api_path = '/home/test.infected.no/public_html/api/';
	
	// Email information
	const emailName = 'Infected';
	const email = 'no-reply@infected.no';
	
	// Tells where QR images should be stored under API folder.
	const qr_path = 'content/qrcache/';
	const avatar_path = 'content/avatars/';

	// Defines how long a ticket should be stored on your account before payment is successful.
	const storeSessionTime = 3600; // You have an hour to pay on paypal
}
?>
