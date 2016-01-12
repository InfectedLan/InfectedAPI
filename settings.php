<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

class Settings {
	/* Metadata */
	const name = 'Infected';
	const description = 'Infected er et av Akershus største datatreff (LAN-party), og holder til i kulturhuset i Asker kommune.';
	const keywords = 'infected, lan, party, asker, kulturhus, ungdom, gaming';

	/* Configuration */
	const domain = 'test.infected.no';

	// Email information.
	const emailName = self::name;
	const email = 'no-reply@' . self::domain;

	// Full path to the API location.
	const api_path = '/home/' . self::domain . '/public_html/api/';

	// Tells where images should be stored.
	const qr_path = '../api/content/qrcache/';
	const avatar_path = '../api/content/avatars/';

	/* Database */
	const db_host = 'localhost';
	const db_name_infected = 'test_infected_no';
	const db_name_infected_compo = 'test_infected_no_compo';
	const db_name_infected_crew = 'test_infected_no_crew';
	const db_name_infected_info = 'test_infected_no_info';
	const db_name_infected_main = 'test_infected_no_main';
	const db_name_infected_tickets = 'test_infected_no_tickets';

	// Infected
	const db_table_infected_emergencycontacts = 'emergencycontacts';
	const db_table_infected_events = 'events';
	const db_table_infected_locations = 'locations';
	const db_table_infected_passwordresetcodes = 'passwordresetcodes';
	const db_table_infected_permissions = 'permissions';
	const db_table_infected_postalcodes = 'postalcodes';
	const db_table_infected_registrationcodes = 'registrationcodes';
	const db_table_infected_tasks = 'tasks';
	const db_table_infected_userfriends = 'userfriends';
	const db_table_infected_usernotes = 'usernotes';
	const db_table_infected_useroptions = 'useroptions';
	const db_table_infected_userpermissions = 'userpermissions';
	const db_table_infected_users = 'users';
	const db_table_infected_syslogs = 'syslogs';

	// InfectedCompo
	const db_table_infected_compo_matchrelationships = 'matchrelationships';
	const db_table_infected_compo_chatmessages =  'chatmessages';
	const db_table_infected_compo_chats =  'chats';
	const db_table_infected_compo_clans = 'clans';
	const db_table_infected_compo_compos = 'compos';
	const db_table_infected_compo_invites = 'invites';
	const db_table_infected_compo_memberof = 'memberof';
	const db_table_infected_compo_memberofchat =  'memberofchat';
	const db_table_infected_compo_participantof = 'participantof';
	const db_table_infected_compo_voteoptions = 'voteoptions';
	const db_table_infected_compo_participantOfMatch = 'participantofmatch';
	const db_table_infected_compo_matches = 'matches';
	const db_table_infected_compo_readyusers = 'readyusers';
	const db_table_infected_compo_votes =  'votes';
  const db_table_infected_compo_qualificationQueue = 'qualificationQueue';
  const db_table_infected_compo_matchmetadata = 'matchmetadata';

	// InfectedCrew
	const db_table_infected_crew_applicationqueue = 'applicationqueue';
	const db_table_infected_crew_applications = 'applications';
	const db_table_infected_crew_avatars = 'avatars';
	const db_table_infected_crew_groups = 'groups';
	const db_table_infected_crew_memberof = 'memberof';
	const db_table_infected_crew_notes = 'notes';
	const db_table_infected_crew_notewatches = 'notewatches';
	const db_table_infected_crew_pages = 'pages';
	const db_table_infected_crew_teams = 'teams';

	// InfectedInfo
	const db_table_infected_info_slides = 'slides';

	// InfectedMain
	const db_table_infected_main_agenda = 'agenda';
	const db_table_infected_main_gameapplications = 'gameapplications';
	const db_table_infected_main_games = 'games';
	const db_table_infected_main_pages = 'pages';

	// InfectedTickets
	const db_table_infected_tickets_checkedintickets = 'checkedintickets';
	const db_table_infected_tickets_entrances = 'entrances';
	const db_table_infected_tickets_payments = 'payments';
	const db_table_infected_tickets_rows = 'rows';
	const db_table_infected_tickets_seatmaps = 'seatmaps';
	const db_table_infected_tickets_seats = 'seats';
	const db_table_infected_tickets_storesessions = 'storesessions';
	const db_table_infected_tickets_tickets = 'tickets';
	const db_table_infected_tickets_tickettransfers = 'tickettransfers';
	const db_table_infected_tickets_tickettypes = 'tickettypes';

	/* Compo */
	// Match participant of state.
	const compo_match_participant_type_clan = 0;
	const compo_match_participant_type_match_winner = 1;
	const compo_match_participant_type_match_looser = 2;
  const compo_match_participant_type_match_walkover = 3;

	/* Crew */
	// Avatar sizes.
	const avatar_thumb_w = 150;
	const avatar_thumb_h = 133;

	const avatar_sd_w = 800;
	const avatar_sd_h = 600;

	const avatar_hd_w = 1200;
	const avatar_hd_h = 900;

	const thumbnail_compression_rate = 100;
	const sd_compression_rate = 100;
	const hd_compression_rate = 100;

	const avatar_minimum_width = 600;
	const avatar_minimum_height = 450;

	/* Tickets */
	// How long time before the tickets event should allow it to be refunded?
	const refundBeforeEventTime = 1209600; // 14 days (14 * 24 * 60 * 60)

	// How long a time should the ticket be stored on your account before payment is successful?
	const storeSessionTime = 3600; // 1 Hour (60 * 60)

	// How long time after ticket is transfered should we allow the former owner to revert the transaction?
	const ticketFee = 20; // Radar membership ticket fee.
	const ticketTransferTime = 86400; // 1 day (24 * 60 * 60)
}
?>
