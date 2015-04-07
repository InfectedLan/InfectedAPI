<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';

if (Session::isAuthenticated()) {
	echo '<h3>Endre passord</h3>';
	
	echo '<script src="../api/scripts/edit-password.js"></script>';
	echo '<form class="edit-password" method="post">';
		echo '<table>';
			echo '<tr>';
				echo '<td>Gammelt passord:</td>';
				echo '<td><input type="password" name="oldPassword" required autofocus></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Nytt passord:</td>';
				echo '<td><input type="password" name="newPassword" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Gjenta nytt passord:</td>';
				echo '<td><input type="password" name="confirmNewPassword" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><input type="submit" value="Lagre"></td>';
			echo '</tr>';
		echo '</table>';
	echo '</form>';
} else {
	echo '<p>Du er ikke logget inn!</p>';
}
?>