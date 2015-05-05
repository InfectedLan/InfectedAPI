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

require_once 'settings.php';
require_once 'database.php';
require_once 'objects/event.php';
require_once 'utils/databaseutils.php';

class EventMigrationHandler {
		/*
	 * Copies all information from the given event to the new one.
	 */
    public static function copyTableByEvent(Event $fromEvent, Event $toEvent, $databaseName, $tableName) {
		if (!$fromEvent->equals($toEvent)) {
			DatabaseUtils::copyTableSelection($databaseName, $tableName, 'eventId', $fromEvent->getId(), $toEvent->getId());
		}
    }

	/*
	 * Copies all information that is relevant from the old event to the new one, overwriting is forbidden so no entries for the new event can already exist.
	 */
    public static function copy(Event $fromEvent, Event $toEvent) {
    	// Infected
    	self::copyPermissions($fromEvent, $toEvent);

    	// InfectedCompo
    	self::copyCompos($fromEvent, $toEvent);
    	//self::copyInvites($fromEvent, $toEvent); // This is commented out because this may be unwanted behavior.
    	
    	// InfectedCrew
    	self::copyGroups($fromEvent, $toEvent);
    	self::copyTeams($fromEvent, $toEvent);
    	self::copyMembers($fromEvent, $toEvent);
    	self::copyRestrictedPages($fromEvent, $toEvent);

    	// InfectedInfo
    	self::copySlides($fromEvent, $toEvent);

    	// InfectedMain
    	self::copyAgenda($fromEvent, $toEvent);

    	// InfectedTickets
    	//self::copyTickets($fromEvent, $toEvent); // This is commented out because this may be unwanted behavior.
    }

    /* Infected */

    /*
	 * Copies permissions from the given event to the new one, overwriting is forbidden so no entries for the new event can already exist.
	 */
    public static function copyPermissions(Event $fromEvent, Event $toEvent) {
	    self::copyTableByEvent($fromEvent, $toEvent, Settings::db_name_infected, Settings::db_table_infected_userpermissions);
    }

    /* InfectedCompo */

 	/*
	 * Copies compos from given event to the new one, overwriting is forbidden so no entries for the new event can already exist.
	 */
    public static function copyCompos($fromEvent, $toEvent) {
	    self::copyTableByEvent($fromEvent, $toEvent, Settings::db_name_infected_compo, Settings::db_table_infected_compo_compos);
    }

    /*
	 * Copies invites from given event to the new one, overwriting is forbidden so no entries for the new event can already exist.
	 */
    public static function copyInvites($fromEvent, $toEvent) {
	    self::copyTableByEvent($fromEvent, $toEvent, Settings::db_name_infected_compo, Settings::db_table_infected_compo_invites);
    }

    /* InfectedCrew */

	/*
	 * Copies groups from given event to the new one, overwriting is forbidden so no entries for the new event can already exist.
	 */
    public static function copyGroups(Event $fromEvent, Event $toEvent) {
    	self::copyTableByEvent($fromEvent, $toEvent, Settings::db_name_infected_crew, Settings::db_table_infected_crew_groups);
    }

    /*
	 * Copies teams from given event to the new one, overwriting is forbidden so no entries for the new event can already exist.
	 */
    public static function copyTeams(Event $fromEvent, Event $toEvent) {
    	self::copyTableByEvent($fromEvent, $toEvent, Settings::db_name_infected_crew, Settings::db_table_infected_crew_teams);
    }

	/*
	 * Copies members from given event to the new one, overwriting is forbidden so no entries for the new event can already exist.
	 */
    public static function copyMembers(Event $fromEvent, Event $toEvent) {
    	self::copyTableByEvent($fromEvent, $toEvent, Settings::db_name_infected_crew, Settings::db_table_infected_crew_memberof);
    }

    /*
	 * Copies restricted pages from given event to the new one, overwriting is forbidden so no entries for the new event can already exist.
	 */
    public static function copyRestrictedPages(Event $fromEvent, Event $toEvent) {
	    self::copyTableByEvent($fromEvent, $toEvent, Settings::db_name_infected_crew, Settings::db_table_infected_crew_pages);
    }

    /* InfectedInfo */

    /*
	 * Copies slides from given event to the new one, overwriting is forbidden so no entries for the new event can already exist.
	 */
    public static function copySlides($fromEvent, $toEvent) {
	    self::copyTableByEvent($fromEvent, $toEvent, Settings::db_name_infected_info, Settings::db_table_infected_info_slides);
    }

    /* InfectedMain */

    /*
	 * Copies agenda entries from given event to the new one, overwriting is forbidden so no entries for the new event can already exist.
	 */
    public static function copyAgenda($fromEvent, $toEvent) {
	    self::copyTableByEvent($fromEvent, $toEvent, Settings::db_name_infected_main, Settings::db_table_infected_main_agenda);
    }

    /* InfectedTickets */

    /*
	 * Copies tickets from given event to the new one, overwriting is forbidden so no entries for the new event can already exist.
	 */
    public static function copyTickets($fromEvent, $toEvent) {
	    self::copyTableByEvent($fromEvent, $toEvent, Settings::db_name_infected_tickets, Settings::db_table_infected_tickets_tickets);
    }
}
?>