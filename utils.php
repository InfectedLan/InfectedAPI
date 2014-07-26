<?php
class Utils {
	public static function getDayFromInt($day) {
		$dayList = array('Mandag', 
						 'Tirsdag', 
						 'Onsdag', 
						 'Torsdag', 
						 'Fredag', 
						 'Lørdag',
						 'Søndag');
		
		return $dayList[$day - 1];
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