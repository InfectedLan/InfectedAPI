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

class Transaction {
	/*
	 * Creates a payment of the given amount of the specified valuta.
	 */
	public static function pay($user, $valuta, $amount) {
		$transactionId = /* Generated transaction id here */;

		if (/* Given amount is successfully paid. */) {
			/* Log the transaction to database */

			return $transactionId;
		}

		return false;
	}

	/*
	 * Refunds the amount of the given transaction to the originating users account.
	 */
	public static function refund($transactionId) {
		if (/* Transaction is refunded completly. */) {
			return true;
		}

		return false;
	}
}
?>