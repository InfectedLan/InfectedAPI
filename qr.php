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

require_once 'libraries/phpqrcode/qrlib.php';
require_once 'settings.php';

class QR {
	public static function getCode(string $content): ?string {
		$fileName = md5($content) . '.png';
		$filePath = Settings::api_path . Settings::qr_path . $fileName;

		if (!file_exists($filePath)) {
			QRcode::png($content, $filePath, QR_ECLEVEL_Q, 400, 6);
		}

		return $fileName;
	}
}
?>
