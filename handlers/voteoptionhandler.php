<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/voteoption.php';
require_once 'objects/compo.php';
require_once 'objects/match.php';

class VoteOptionHandler {
    /*
     * Get a vote option by the internal id.
     */
    public static function getVoteOption($id) {
        $database = Database::open(Settings::db_name_infected_compo);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_voteoptions . '` 
		                            WHERE `id` = \'' . $id . '\';');
        
        $database->close();
		
		return $result->fetch_object('VoteOption');
    }

    /*
     * Get a vote option for a specified compo.
     */
    public static function getVoteOptionsByCompo(Compo $compo) {
        $database = Database::open(Settings::db_name_infected_compo);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_voteoptions . '` 
                                    WHERE `compoId` = '. $compo->getId() . ';');
        
        $database->close();

        $voteOptionList = array();

        while ($object = $result->fetch_object('VoteOption')) {
            array_push($voteOptionList, $object);
        }

        return $voteOptionList;
    }

    /*
     * Returns true if specified vote option is voted for the specified match.
     */
    public static function isVoted(VoteOption $voteOption, Match $match) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_votes . '` 
                                    WHERE `voteOptionId` = '. $voteOption->getId() . '
								    AND `consumerId` = ' . $match->getId() . ';');
        
        $database->close();                         
        
        return $result->num_rows > 0;
    }
}
?>