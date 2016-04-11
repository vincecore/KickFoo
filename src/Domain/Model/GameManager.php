<?php

namespace KickFoo\Domain\Model;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use KickFoo\Domain\Event\GameEndedEvent;
use KickFoo\Domain\Event\GameEvents;
use KickFoo\Domain\Event\GameStartedEvent;
use KickFoo\Domain\Event\GoalDeletedEvent;
use KickFoo\Domain\Event\GoalScoredEvent;
use KickFoo\Domain\Exception\NoGoalToRemoveException;
use KickFoo\Domain\Entity\Game;
use KickFoo\Domain\Entity\Goal;
use KickFoo\Domain\Entity\Player;
use KickFoo\Domain\Entity\Team;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GameManager
{
    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructor
     *
     * @param ObjectManager $em
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ObjectManager $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Start a game
     *
     * @param Game $game
     * @param Team $teamOne
     * @param Team $teamTwo
     * @param DateTime $startTime
     *
     * @return Game
     */
    public function start(Game $game, Team $teamOne, Team $teamTwo, DateTime $startTime)
    {
        $game->setTeamOne($teamOne);
        $game->setTeamTwo($teamTwo);
        $game->setStart($startTime);
        $game->setEnd(null);
        $game->setGoalsTeamOne(0);
        $game->setGoalsTeamTwo(0);

        $playersTeamOne = $teamOne->getPlayers();
        $game->setPlayerOneTeamOne($playersTeamOne[0]);
        $game->setPlayerTwoTeamOne($playersTeamOne[1]);
        $playersTeamTwo = $teamTwo->getPlayers();
        $game->setPlayerOneTeamTwo($playersTeamTwo[0]);
        $game->setPlayerTwoTeamTwo($playersTeamTwo[1]);

        $this->em->persist($game);
        $this->em->flush();

        $event = new GameStartedEvent($game);
        $this->eventDispatcher->dispatch(GameEvents::ON_GAME_START, $event);

        return $game;
    }

    /**
     * End the game
     *
     * @param Game   $game
     *
     * @return Game
     */
    public function end(Game $game)
    {
        $game->setEnd(new DateTime());

        $this->em->persist($game);
        $this->em->flush();

        $event = new GameEndedEvent($game);
        $this->eventDispatcher->dispatch(GameEvents::ON_GAME_END, $event);

        return $game;
    }


    /**
     * Add a goal
     *
     * @param Goal   $goal
     * @param Game   $game
     * @param Player $player
     * @param string $position
     * @param Team   $team
     * @param string $type
     * @param array  $players
     * @return Goal
     */
    public function addGoal(Goal $goal, Game $game, Player $player, $position, Team $team, $type, array $players)
    {
        $goal->setPosition($position);
        $goal->setGoalsTeamOne($game->getGoalsTeamOne());
        $goal->setGoalsTeamTwo($game->getGoalsTeamTwo());
        $goal->setType($type);
        $goal->setTime(new DateTime());
        $goal->setPlayer($player);

        $goal->setTeamOneBack($players[0]);
        $goal->setTeamOneForward($players[1]);
        $goal->setTeamTwoBack($players[2]);
        $goal->setTeamTwoForward($players[3]);

        $goal->setGame($game);
        $goal->setTeam($team);

        $this->em->persist($goal);

        $event = new GoalScoredEvent($game, $goal);
        $this->eventDispatcher->dispatch(GameEvents::ON_ADD_TEAM_GOAL, $event);

        return $goal;
    }

    /**
     * This function checks if a game has to be ended automatically
     * due to the score
     *
     * Game will be ended if score is 11 or more and the goal difference is 2 or more
     *
     * @param Game $game
     * @return boolean
     */
    public function checkAutoEnd(Game $game)
    {
        if (($game->getGoalsTeamOne() >= 11 && $game->getGoalsTeamOne() - $game->getGoalsTeamTwo() >= 2)
            || ($game->getGoalsTeamTwo() >= 11 && $game->getGoalsTeamTwo() - $game->getGoalsTeamOne() >= 2)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete the last goal from the goals
     *
     * @param Game $game
     *
     * @return Game
     */
    public function deleteLastGoal(Game $game)
    {
        $lastGoal = $this->getLastGoalFromGame($game);
        $this->em->remove($lastGoal);
        $game->removeGoal($lastGoal);
        $this->em->persist($game);

        return $game;
    }

    /**
     * Delete the last goal from the game by adjusting the score
     *
     * @param Game $game
     *
     * @return Game
     */
    public function deleteLastGoalFromGame(Game $game)
    {
        try {
            $lastGoal = $this->getLastGoalFromGame($game);
            $game->setGoalsTeamOne($lastGoal->getGoalsTeamOne());
            $game->setGoalsTeamTwo($lastGoal->getGoalsTeamTwo());
        } catch (NoGoalToRemoveException $e) {
            $game->setGoalsTeamOne(0);
            $game->setGoalsTeamTwo(0);
        }

        $this->em->persist($game);

        $event = new GoalDeletedEvent($game);
        $this->eventDispatcher->dispatch(GameEvents::ON_DELETE_GOAL, $event);

        return $game;
    }

    /**
     * Get the last goal from the game,
     * returns false if there isn't one
     *
     * @param Game $game
     * @return Goal
     * @throws NoGoalToRemoveException
     */
    protected function getLastGoalFromGame(Game $game)
    {
        $goals = $game->getGoals();
        if (!$goals instanceof Collection || $goals->isEmpty()) {
            throw new NoGoalToRemoveException();
        }
        return $goals->last();
    }
}
