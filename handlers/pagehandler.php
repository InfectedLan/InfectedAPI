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
require_once 'objects/page.php';

class PageHandler {
	/*
	 * Return the page bu the internal id.
	 */
	public static function getPage($id) {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_pages . '`
																WHERE id = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('Page');
	}

	/*
	 * Return the page by name.
	 */
	public static function getPageByName($name) {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_pages . '`
																WHERE `name` = \'' . $database->real_escape_string($name) . '\';');

		return $result->fetch_object('Page');
	}

	/*
	 * Returns a list of all pages.
	 */
	public static function getPages() {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_pages . '`;');

		$pageList = [];

		while ($object = $result->fetch_object('Page')) {
			$pageList[] = $object;
		}

		return $pageList;
	}

	/*
	 * Create a new page.
	 */
	public static function createPage($name, $title, $content) {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$database->query('INSERT INTO `' . Settings::db_table_infected_main_pages . '` (`name`, `title`, `content`)
										  VALUES (\'' . $database->real_escape_string($name) . '\',
														  \'' . $database->real_escape_string($title) . '\',
														  \'' . $database->real_escape_string($content) . '\')');

		return self::getPage($database->insert_id);
	}

	/*
	 * Update a page.
	 */
	public static function updatePage(Page $page, $title, $content) {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$database->query('UPDATE `' . Settings::db_table_infected_main_pages . '`
										  SET `title` = \'' . $database->real_escape_string($title) . '\',
											  	`content` = \'' . $database->real_escape_string($content) . '\'
										  WHERE `id` = \'' . $page->getId() . '\';');
	}

	/*
	 * Remove a page.
	 */
	public static function removePage(Page $page) {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$database->query('DELETE FROM `' . Settings::db_table_infected_main_pages . '`
						  				WHERE `id` = \'' . $page->getId() . '\';');
	}
}
?>
