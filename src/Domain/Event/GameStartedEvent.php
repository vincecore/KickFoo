<?php

namespace KickFoo\Domain\Event;

use KickFoo\Domain\Entity\Game;
use Symfony\Component\EventDispatcher\Event;

class GameStartedEvent extends Event
{
    /**
     * @var Game
     */
    private $game;

    /**
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }
}
