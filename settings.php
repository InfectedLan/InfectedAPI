<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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
	const authors = 'halvors og petterroa';

	/* Configuration */
	const domain = 'test.infected.no';

	// Email information.
	const enableEmail = true;
	const emailName = self::name;
	const email = 'no-reply@' . self::domain;

	// Full path to the API location.
	const api_path = '/home/' . self::domain . '/public_html/api/';

	// Permissions file.
	const file_json_permissions = self::api_path . 'json/permissions.json';
	const file_json_postalcodes = self::api_path . 'json/postalcodes.json';

	// Tells where images should be stored.
	const qr_path = '../api/content/qrcache/';
	const avatar_path = '../api/content/avatars/';
	const api_relative_avatar_path = 'content/avatars/';

	/* PHP */
	const php_version = '7.2.0';

	/* Database */
	const db_host = 'localhost';
	
	const db_name_infected = 'test_infected_no';
	const db_name_infected_compo = 'test_infected_no_compo';
	const db_name_infected_crew = 'test_infected_no_crew';
	const db_name_infected_info = 'test_infected_no_info';
	const db_name_infected_main = 'test_infected_no_main';
	const db_name_infected_tech = 'test_infected_no_tech';
	const db_name_infected_tickets = 'test_infected_no_tickets';

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

	const prioritySeatingReq = 5;

	const curfewLimit = 14;
}
