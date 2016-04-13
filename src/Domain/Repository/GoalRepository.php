<?php

namespace KickFoo\Domain\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use KickFoo\Domain\Entity\Game;
use KickFoo\Domain\Entity\Goal;
use KickFoo\Domain\Entity\Player;

class GoalRepository extends EntityRepository implements GoalRepositoryInterface
{
    /**
     * Find the unclaimed goal for the given game
     *
     * @param  Game $game
     * @return Goal
     */
    public function findUnclaimedGoal(Game $game)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('g')
           ->from('KickFoo\Domain\Entity\Goal', 'g')
           ->andWhere('g.game = :game')
           ->andWhere('g.player IS NULL')
           ->addOrderBy('g.time', 'ASC');
        $query = $qb->getQuery();
        $query->setParameter('game', $game);
        $query->setMaxResults(1);

        return $query->getOneOrNullResult();
    }

    /**
     * Find the last goal
     * @param  Game $game
     * @return Goal
     */
    public function findLastGoal(Game $game)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('g')
           ->from('KickFoo\Domain\Entity\Goal', 'g')
           ->andWhere('g.game = :game')
           ->addOrderBy('g.time', 'DESC');
        $query = $qb->getQuery();
        $query->setParameter('game', $game);
        $query->setMaxResults(1);

        return $query->getOneOrNullResult();
    }

    /**
     * Find the last goal
     *
     * @return Goal
     */
    public function findLastGoalOfEndedGame()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('g')
           ->from('KickFoo\Domain\Entity\Goal', 'g')
           ->addOrderBy('g.time', 'DESC');
        $query = $qb->getQuery();
        $query->setMaxResults(1);

        return $query->getOneOrNullResult();
    }

    /**
     * Find claimed goals
     *
     * @return array
     */
    public function findAllClaimedGoals($startDate = null, $endDate = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('g')
            ->from('KickFoo\Domain\Entity\Goal', 'g')
            ->join('g.player', 'p')
           ->andWhere('g.player IS NOT NULL')
           ->andWhere('p.active = 1');

        if ($startDate && $endDate) {
            $qb->andWhere('g.time >= :startDate');
            $qb->setParameter('startDate', $startDate);
            $qb->andWhere('g.time <= :endDate');
            $qb->setParameter('endDate', $endDate);
        }
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Find claimed goals for a player
     *
     * @return array
     */
    public function findAllClaimedGoalsForPlayer(Player $player, DateTime $startDate = null, DateTime $endDate = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('g')
            ->from('KickFoo\Domain\Entity\Goal', 'g')
            ->join('g.player', 'p')
           ->andWhere('g.player = :p')
           ->andWhere('p.active = 1');

        $qb->setParameter('p', $player);

        if ($startDate && $endDate) {
            $qb->andWhere('g.time >= :startDate');
            $qb->setParameter('startDate', $startDate);
            $qb->andWhere('g.time <= :endDate');
            $qb->setParameter('endDate', $endDate);
        }
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Find claimed goals for a player
     *
     * @param Player $player
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getBackGoalsForPlayerCount(Player $player, DateTime $startDate = null, DateTime $endDate = null)
    {
        return $this->getGoalsForPlayerAndPositionCount($player, Goal::POSITION_BACK, $startDate, $endDate);
    }

    /**
     * Find claimed goals for a player
     *
     * @param Player $player
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getForwardGoalsForPlayerCount(Player $player, DateTime $startDate = null, DateTime $endDate = null)
    {
        return $this->getGoalsForPlayerAndPositionCount($player, Goal::POSITION_FORWARD, $startDate, $endDate);
    }

    /**
     * Find claimed goals for a player
     *
     * @param Player $player
     * @param $position
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    protected function getGoalsForPlayerAndPositionCount(Player $player, $position, DateTime $startDate = null, DateTime $endDate = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(g.id)')
            ->from('KickFoo\Domain\Entity\Goal', 'g')
            ->join('g.player', 'p')
           ->andWhere('g.player = :p')
           ->andWhere('g.position = :position')
           ->andWhere('g.type = :type')
           ->andWhere('p.active = 1');

        $qb->setParameter('p', $player);
        $qb->setParameter('position', $position);
        $qb->setParameter('type', Goal::TYPE_REGULAR);

        if ($startDate && $endDate) {
            $qb->andWhere('g.time >= :startDate');
            $qb->setParameter('startDate', $startDate);
            $qb->andWhere('g.time <= :endDate');
            $qb->setParameter('endDate', $endDate);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Find claimed goals for a player
     *
     * @param Player $player
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getOwnGoalsForPlayerCount(Player $player, DateTime $startDate = null, DateTime $endDate = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(g.id)')
            ->from('KickFoo\Domain\Entity\Goal', 'g')
            ->join('g.player', 'p')
           ->andWhere('g.player = :p')
           ->andWhere('g.type = :type')
           ->andWhere('p.active = 1');

        $qb->setParameter('p', $player);
        $qb->setParameter('type', Goal::TYPE_OWNGOAL);

        if ($startDate && $endDate) {
            $qb->andWhere('g.time >= :startDate');
            $qb->setParameter('startDate', $startDate);
            $qb->andWhere('g.time <= :endDate');
            $qb->setParameter('endDate', $endDate);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getTotalGoalsCount()
    {
        $query = $this->_em->createQuery('SELECT COUNT(g.id) FROM KickFoo\Domain\Entity\Goal g');
        $count = $query->getSingleScalarResult();
        return $count;
    }

    public function findMostGoalsPerPlayer($limit = 1)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(g.id) as goalCount, g as goal, p')
            ->from('KickFoo\Domain\Entity\Goal', 'g')
            ->join('g.player', 'p')
            ->groupBy('g.player')
            ->andWhere('g.type = :type')
            ->andWhere('p.active = 1')
            ->setMaxResults($limit)
            ->orderBy('goalCount', 'DESC');

        $qb->setParameter('type', Goal::TYPE_REGULAR);

        if ($limit == 1) {
            $qb->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
            $result = $qb->getQuery()->getOneOrNullResult();
        } else {
            $result = $qb->getQuery()->getResult();
        }
        return $result;
    }
}
