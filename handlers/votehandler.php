<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/vote.php';

class VoteHandler {
    public static function getVote($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_votes . '` 
                                 WHERE `id` = \'$id\';');
        
        $mysql->close();
		
		return $result->fetch_object('Vote');
    }
    
    public static function getNumBanned($mysqlsumerId) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_votes . '` 
                                 WHERE `consumerId` = ' . $mysql->real_escape_string($mysqlsumerId) . ';');
        
        $mysql->close();

        return $result->num_rows;
    }
    
    public static function getCurrentBanner($numBanned) {
        $turnArray = array(0, 1, 0, 1, 1, 0, 2);
        
        return $turnArray[$numBanned];
    }
    
    public static function banMap($voteOption, $mysqlsumerId) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_votes . '` (`consumerId`, `voteOptionId`) 
                                 VALUES (\'' . $mysql->real_escape_string($mysqlsumerId) . '\', 
                                         \'' . $mysql->real_escape_string($voteOption->getId()) . '\');');
    
        $mysql->close();
    }
}
?>