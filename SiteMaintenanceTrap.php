<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <https://infected.no/>.
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
require_once('maintenance.php');
require_once('settings.php');

Maintenance::loadMaintenanceState();

//This will kill the site before it attempts to do anything, while the page is in maintenance mode
if(Maintenance::isMaintenance()) {
    http_response_code(503);
    readfile(Settings::api_path . "pages/maintenance.html"); //IMPORTANT: This is served from the root of a site.
    die();
}
?>
