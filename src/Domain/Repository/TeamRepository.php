<?php

namespace KickFoo\Domain\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use KickFoo\Domain\Entity\Player;
use KickFoo\Domain\Entity\Team;

class TeamRepository extends EntityRepository implements TeamRepositoryInterface
{
    /**
     * Find the team for the given players
     * 
     * @param Player $playerOne
     * @param Player $playerTwo
     * 
     * @return Team
     */
    public function findTeamForPlayers(Player $playerOne, Player $playerTwo)
    {
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('KickFoo\Domain\Entity\Team', 't');
        $rsm->addFieldResult('t', 'id', 'id');
        $rsm->addFieldResult('t', 'name', 'name');

        $sql = "SELECT t . *
FROM `kickfoo_teams` t
JOIN `kickfoo_player_teams` pt ON pt.team_id = t.id
WHERE pt.`player_id` = :playerOne
AND pt.`team_id`
IN (

SELECT pt2.`team_id`
FROM `kickfoo_player_teams` pt2
WHERE pt2.`player_id` = :playerTwo
)";
        
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('playerOne', $playerOne);
        $query->setParameter('playerTwo', $playerTwo);
       
        return $query->getOneOrNullResult();
    }
}
