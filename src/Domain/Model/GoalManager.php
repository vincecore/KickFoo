<?php

namespace KickFoo\Domain\Model;

use Doctrine\Common\Persistence\ObjectManager;
use KickFoo\Domain\Entity\Game;
use KickFoo\Domain\Entity\Goal;
use KickFoo\Domain\Repository\GoalRepositoryInterface;

class GoalManager
{
    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var GoalRepositoryInterface
     */
    protected $goalRepository;

    /**
     * Constructor
     *
     * @param ObjectManager $em
     * @param GoalRepositoryInterface $goalRepository
     */
    public function __construct(ObjectManager $em, GoalRepositoryInterface $goalRepository)
    {
        $this->em = $em;
        $this->goalRepository = $goalRepository;
    }

    /**
     * Check if the goal already has been added in the goal table
     *
     * @param Game  $game
     * @param array $newScore
     *
     * @return bool
     */
    public function isGoalAlreadyAdded(Game $game, array $newScore)
    {
        $criteria = array(
            'game'          => $game->getId(),
            'goalsTeamOne'  => trim($newScore[0]),
            'goalsTeamTwo'  => trim($newScore[1])
        );
        $goal = $this->goalRepository->findOneBy($criteria);
        if ($goal instanceof Goal) {
            return true;
        } else {
            return false;
        }
    }
}
