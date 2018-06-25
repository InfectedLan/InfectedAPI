/*
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

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function() {
	$('.edit-password').on('submit', function(event) {
		event.preventDefault();
		editPassword(this);
	});
});

function editPassword(form) {
	$.post('../api/json/user/editUserPassword.php', $(form).serialize(), function(data) {
		if (data.result) {
			$(location).attr('href', 'index.php?page=user-profile');
		} else {
			error(data.message);
		}
	}, 'json');
}
