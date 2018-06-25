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
	$('.request-reset-password').submit(function(e) {
		e.preventDefault();
		$.getJSON('../api/json/user/resetUserPassword.php' + '?' + $(this).serialize(), function(data){
			if (data.result) {
				$(location).attr('href', '.');
			} else {
				error(data.message); 
			}
		});
	});
	
	$('.reset-password').submit(function(e) {
		e.preventDefault();
		$.getJSON('../api/json/user/resetUserPassword.php' + '?' + $(this).serialize(), function(data){
			if (data.result) {
				$(location).attr('href', '.');
			} else {
				error(data.message); 
			}
		});
	});
});
