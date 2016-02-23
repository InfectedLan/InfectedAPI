/**
 * This file is part of InfectedCrew.
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

function loadData() {
	var ticketId = $("#ticketId").val();

	$.getJSON('../api/json/ticket/getTicketData.php?id=' + encodeURIComponent(ticketId), function(data) {
		if (data.result) {
			// Remove old entries.
			$("#ticketDetails").empty();
			var user = data.userData;

			$("#ticketDetails").append('<table>' +
									   	   '<tr>' +
										       '<td>Navn:</td>' +
										       '<td>' + user.firstname + ' ' + user.lastname +'</td>' +
										   '</tr>' +
										   '<tr>' +
										   	   '<td>Adresse:</td>' +
										   	   '<td>' + user.address + '</td>' +
										   '</tr>' +
										   '<tr>' +
										   	   '<td></td>' +
										   	   '<td>' + user.city + '</td>' +
										   '</tr>' +
										   '<tr>' +
										   	   '<td>Kjønn:</td>' +
										   	   '<td>' + user.gender + '</td>' +
										   '</tr>' +
										   '<tr>' +
										   	   '<td>Født:</td>' +
										       '<td>' + user.birthdate + '</td>' +
										   '</tr>' +
										   '<tr>' +
										       '<td>Alder:</td>' +
										   	   '<td>' + user.age + ' År</td>' +
										   '</tr>' +
										   '<tr>' +
										   	   '<td>Brukernavn:</td>' +
										   	   '<td>' + user.username + '</td>' +
										   '</tr>' +
										   '<tr>' +
										   	   '<td>E-post:</td>' +
										   	   '<td>' + user.email + '</td>' +
										   '</tr>' +
										   '<tr>' +
										   	   '<td>Phone:</td>' +
										   	   '<td>' + user.phone + '</td>' +
										   '</tr>' +

									   '</table>' +
									   '<input type="button" value="Godkjenn" onClick="acceptTicket(' + ticketId + ')">');
		} else {
			error(data.message);
		}
	});
}

function acceptTicket(id) {
	$.getJSON('../api/json/ticket/checkInTicket.php?id=' + encodeURIComponent(id), function(data) {
		if (data.result) {
			// Remove the user information.
			$("#ticketDetails").empty();

			// Display confirmation message to the user.
			info(data.message);
		} else {
			error(data.message);
		}
	});
}
