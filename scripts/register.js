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
	$('.register').on('submit', function(event) {
		event.preventDefault();
		if($("#tos").checked()) {
            register(this);
        } else {
			alert("Du må akseptere vilkår for bruk for å kunne registrere.");
		}
	});
});

function register(form) {
	$.post('../api/json/user/addUser.php', $(form).serialize(), function(data) {
		if (data.result) {
			info(data.message, function() {
				location.reload();
			});
		} else {
			error(data.message);
		}
	}, 'json');
}
