<?php
require_once 'phpqrcode/qrlib.php';

class QR {
	public function getCode($content) {
		$fileName = md5($this->getHumanName()) . '.png';
		$directory = '/api/images/qrcache/' . $fileName;
		$filePath = $_SERVER['DOCUMENT_ROOT'] . $directory;
    
		if (!file_exists($filePath)) {
			QRcode::png($content, $filePath);
		}
		
		return $directory;
	}
}
?>