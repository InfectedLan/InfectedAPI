<?php
require_once 'phpqrcode/qrlib.php';
require_once 'settings.php';

class QR {
	public static function getCode($content) {
		$fileName = md5($content) . '.png';
		//$filePath = Settings::qr_path . $fileName;
		$filePath = 'content/seatmapBackground/' . $fileName;
    
		if (!file_exists($filePath)) {
			QRcode::png($content, $filePath);
		}
		
		return $fileName;
	}
}
?>