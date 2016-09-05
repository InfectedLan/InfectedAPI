<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2013-2016 Infected <http://infected.no/>.
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
require_once 'objects/sectionpage.php';

class SectionPageHandler {
	/*
	 * Return the section page by the internal id.
	 */
	public static function getSectionPage($id) {
		$database = Database::open(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_sectionpages . '`
																WHERE id = \'' . $database->real_escape_string($id) . '\';');

		$database->close();

		return $result->fetch_object('SectionPage');
	}

	/*
	 * Return the section page by name.
	 */
	public static function getSectionPageByName($name) {
		$database = Database::open(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_sectionpages . '`
																WHERE `name` = \'' . $database->real_escape_string($name) . '\';');

		$database->close();

		return $result->fetch_object('SectionPage');
	}

	/*
	 * Returns a list of all pages.
	 */
	public static function getSectionPages() {
		$database = Database::open(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_sectionpages . '`;');

		$database->close();

		$pageList = [];

		while ($object = $result->fetch_object('SectionPage')) {
			$pageList[] = $object;
		}

		return $pageList;
	}

	/*
	 * Create a new section page.
	 */
	public static function createSectionPage($name, $title, $content) {
		$database = Database::open(Settings::db_name_infected_main);

		$database->query('INSERT INTO `' . Settings::db_table_infected_main_sectionpages . '` (`name`, `title`, `content`)
										  VALUES (\'' . $database->real_escape_string($name) . '\',
														  \'' . $database->real_escape_string($title) . '\',
														  \'' . $database->real_escape_string($content) . '\')');

		$page = self::getSectionPage($database->insert_id);

		$database->close();

		return $page;
	}

	/*
	 * Update a section page.
	 */
	public static function updatePage(SectionPage $sectionPage, $title, $content) {
		$database = Database::open(Settings::db_name_infected_main);

		$database->query('UPDATE `' . Settings::db_table_infected_main_sectionpages . '`
										  SET `title` = \'' . $database->real_escape_string($title) . '\',
											  	`content` = \'' . $database->real_escape_string($content) . '\'
										  WHERE `id` = \'' . $sectionPage->getId() . '\';');

		$database->close();
	}

	/*
	 * Remove a section page.
	 */
	public static function removeSectionPage(SectionPage $sectionPage) {
		$database = Database::open(Settings::db_name_infected_main);

		$database->query('DELETE FROM `' . Settings::db_table_infected_main_sectionpages . '`
						  				WHERE `id` = \'' . $page->getId() . '\';');

		$database->close();
	}
}
?>
