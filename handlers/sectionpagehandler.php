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

require_once 'settings.php';
require_once 'database.php';
require_once 'objects/sectionpage.php';

class SectionPageHandler {
	/*
	 * Return the section page by the internal id.
	 */
	public static function getSectionPage(int $id): SectionPage {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_sectionpages . '`
																WHERE id = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('SectionPage');
	}

	/*
	 * Return the section page by name.
	 */
	public static function getSectionPageByName(string $name): SectionPage {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_sectionpages . '`
																WHERE `name` = \'' . $database->real_escape_string($name) . '\';');

		return $result->fetch_object('SectionPage');
	}

	/*
	 * Returns a list of all pages.
	 */
	public static function getSectionPages(): array {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_sectionpages . '`;');

		$pageList = [];

		while ($object = $result->fetch_object('SectionPage')) {
			$pageList[] = $object;
		}

		return $pageList;
	}

	/*
	 * Create a new section page.
	 */
	public static function createSectionPage(string $name, string $title, string $content): SectionPage {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$database->query('INSERT INTO `' . Settings::db_table_infected_main_sectionpages . '` (`name`, `title`, `content`)
										  VALUES (\'' . $database->real_escape_string($name) . '\',
														  \'' . $database->real_escape_string($title) . '\',
														  \'' . $database->real_escape_string($content) . '\')');

		return self::getSectionPage($database->insert_id);
	}

	/*
	 * Update a section page.
	 */
	public static function updatePage(SectionPage $sectionPage, string $title, string $content) {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$database->query('UPDATE `' . Settings::db_table_infected_main_sectionpages . '`
										  SET `title` = \'' . $database->real_escape_string($title) . '\',
											  	`content` = \'' . $database->real_escape_string($content) . '\'
										  WHERE `id` = \'' . $sectionPage->getId() . '\';');
	}

	/*
	 * Remove a section page.
	 */
	public static function removeSectionPage(SectionPage $sectionPage) {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$database->query('DELETE FROM `' . Settings::db_table_infected_main_sectionpages . '`
						  				WHERE `id` = \'' . $page->getId() . '\';');
	}
}
?>
