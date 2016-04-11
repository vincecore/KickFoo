<?php

namespace KickFoo\Domain\Event;

use KickFoo\Domain\Entity\Game;
use KickFoo\Domain\Entity\Goal;
use Symfony\Component\EventDispatcher\Event;

class GoalScoredEvent extends Event
{
    /**
     * @var Game
     */
    private $game;

    /**
     * @var Goal
     */
    private $goal;

    /**
     * GoalScoredEvent constructor
     * @param Game $game
     * @param Goal $goal
     */
    public function __construct(Game $game, Goal $goal)
    {
        $this->game = $game;
        $this->goal = $goal;
    }

    /**
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @return Goal
     */
    public function getGoal()
    {
        return $this->goal;
    }
}
