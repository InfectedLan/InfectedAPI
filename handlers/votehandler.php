<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/vote.php';
require_once 'objects/voteoption.php';
require_once 'objects/user.php';

class VoteHandler {
    public static function getVote($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_votes . '` 
                                 WHERE `id` = \'$id\';');
        
        $mysql->close();
		
		return $result->fetch_object('Vote');
    }
    
    public static function getNumBanned(User $consumer) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_votes . '` 
                                 WHERE `consumerId` = ' . $mysql->real_escape_string($consumer->getId()) . ';');
        
        $mysql->close();

        return $result->num_rows;
    }
    
    public static function getCurrentBanner($numBanned) {
        $turnArray = array(0, 1, 0, 1, 1, 0, 2);
        
        return $turnArray[$numBanned];
    }
    
    public static function banMap(VoteOption $voteOption, User $consumer) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_votes . '` (`consumerId`, `voteOptionId`) 
                                 VALUES (\'' . $mysql->real_escape_string($consumer->getId()) . '\', 
                                         \'' . $mysql->real_escape_string($voteOption->getId()) . '\');');
        
        $mysql->close();
    }
}
?>