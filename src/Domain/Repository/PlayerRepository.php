<?php

namespace KickFoo\Domain\Repository;

use Doctrine\ORM\EntityRepository;
use KickFoo\Domain\Entity\Player;

class PlayerRepository extends EntityRepository implements PlayerRepositoryInterface
{
    /**
     * Retrieve all the active players from the database, sorted by firstname
     *
     * @param null $group
     * @return array
     */
    public function findAllActivePlayers($group = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p')
           ->from('KickFoo\Domain\Entity\Player', 'p')
           ->andWhere('p.active = 1')
           ->addOrderBy('p.firstname', 'ASC');

        if (!is_null($group)) {
            $qb->andWhere('p.group = :group')
                ->setParameter('group', $group);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Retrieve all the active players in the king foo group
     *
     * @return array
     */
    public function findAllActiveKingFooPlayers()
    {
        return $this->findAllActivePlayers(Player::GROUP_KINGFOO);
    }

    /**
     * Retrieve all the active players in the guest group
     *
     * @return array
     */
    public function findAllActiveGuestPlayers()
    {
        return $this->findAllActivePlayers(Player::GROUP_GUEST);
    }
}
