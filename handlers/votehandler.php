<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/vote.php';
require_once 'objects/voteoption.php';
require_once 'objects/user.php';

class VoteHandler {
    public static function getVote($id) {
        $database = Database::open(Settings::db_name_infected_compo);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_votes . '` 
                                    WHERE `id` = \'$id\';');
        
        $database->close();
		
		return $result->fetch_object('Vote');
    }
    
    public static function getNumBanned(User $consumer) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_votes . '` 
                                    WHERE `consumerId` = ' . $consumer->getId() . ';');
        
        $database->close();

        return $result->num_rows;
    }
    
    public static function getCurrentBanner($numBanned) {
        $turnArray = array(0, 1, 0, 1, 1, 0, 2);
        
        return $turnArray[$numBanned];
    }
    
    public static function banMap(VoteOption $voteOption, User $consumer) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('INSERT INTO `' . Settings::db_table_infected_compo_votes . '` (`consumerId`, `voteOptionId`) 
                                    VALUES (\'' . $consumer->getId() . '\', 
                                            \'' . $voteOption->getId() . '\');');
        
        $database->close();
    }
}
?>