<?php
class Utils {
	public static function getDayFromInt($day) {
		$dayList = array('Søndag',
						 'Mandag', 
						 'Tirsdag', 
						 'Onsdag', 
						 'Torsdag', 
						 'Fredag', 
						 'Lørdag');
		
		return $dayList[$day];
	}
	
	public static function getMonthFromInt($month) {
		$monthList = array('Januar', 
						   'Februar', 
						   'Mars', 
						   'April', 
						   'Mai', 
						   'Juni', 
						   'Juli', 
						   'August', 
						   'September', 
						   'Oktober', 
						   'November', 
						   'Desember');
		
		return $monthList[$month - 1];
	}
}
?>