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

require_once 'session.php';
require_once 'localization.php';

if (!Session::isAuthenticated()) {
	echo '<script src="../api/scripts/reset-password.js"></script>';

	if (!isset($_GET['code'])) {
		echo '<h2>' . Localization::getLocale('forgot_password') . '?</h2>';
		echo '<form class="request-reset-password" method="post">';
			echo '<p>' . Localization::getLocale('enter_your_username_or_e-mail_in_order_to_reset_your_password') . ': <input type="text" name="identifier" placeholder="Brukernavn, e-post eller telefon" required autofocus></p>';
			echo '<input type="submit" value="' . Localization::getLocale('reset_password') . '">';
		echo '</form>';
	} else {
		echo '<h2>' . Localization::getLocale('reset_password') . '</h2>';
		echo '<p>' . Localization::getLocale('enter_a_new_password') . '</p>';

		echo '<form class="reset-password" method="post">';
			echo '<input type="hidden" name="code" value="' . $_GET['code'] . '">';
			echo '<table>';
				echo '<tr>';
					echo '<td>' . Localization::getLocale('new_password') . ':</td>';
					echo '<td><input type="password" name="password"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td>' . Localization::getLocale('repeat_password') . ':</td>';
					echo '<td><input type="password" name="confirmpassword"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><input type="submit" value="' . Localization::getLocale('change') . '"></td>';
				echo '</tr>';
			echo '</table>';
		echo '</form>';
	}
} else {
	echo Localization::getLocale('since_you_are_already_logged_in_you_it_seems_like_you_remember_your_password_after_all');
}
?>
