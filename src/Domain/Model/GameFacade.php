<?php
namespace KickFoo\Domain\Model;

use Doctrine\Common\Persistence\ObjectManager;
use KickFoo\Domain\Exception\GoalAlreadyAddedException;
use KickFoo\Domain\Entity\Game;
use KickFoo\Domain\Entity\Goal;
use KickFoo\Domain\Entity\Player;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GameFacade
{
    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var GameManager
     */
    protected $gameManager;

    /**
     * @var TeamManager
     */
    protected $teamManager;

    /**
     * @var GoalManager
     */
    protected $goalManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructs the Facade object, injecting the GameManager
     *
     * @param ObjectManager $em
     * @param GameManager $gameManager
     * @param TeamManager $teamManager
     * @param GoalManager $goalManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ObjectManager $em, GameManager $gameManager, TeamManager $teamManager, GoalManager $goalManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->gameManager = $gameManager;
        $this->teamManager = $teamManager;
        $this->goalManager = $goalManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Unified interface to delete the last goal from a game
     *
     * @param Game $game
     * @return Game
     */
    public function deleteLastGoal(Game $game)
    {
        $game = $this->gameManager->deleteLastGoal($game);
        $game = $this->gameManager->deleteLastGoalFromGame($game);

        $this->em->flush();

        $result = array();
        $result['game'] = $game->toArray();

        return $game;
    }

    /**
     * Add a goal
     *
     * @param Goal $goal
     * @param Game $game
     * @param Player $player
     * @param string $position
     * @param string $type
     * @param array $oldScore
     * @param array $players
     * @return array
     * @throws GoalAlreadyAddedException
     */
    public function addGoal(Goal $goal, Game $game, Player $player, $position, $type, array $oldScore, array $players)
    {
        $goal->setType($type);

        $teamOne = $this->teamManager->formTeam($players[0], $players[1]);
        $teamTwo = $this->teamManager->formTeam($players[2], $players[3]);

        $newScore = $oldScore;
        if (($goal->isOwnGoal() && $teamOne->contains($player))
            || (!$goal->isOwnGoal() && $teamTwo->contains($player))) {
            $newScore[1] = $oldScore[1] + 1;
            $game->addGoalTeamTwo();
        } else {
            $newScore[0] = $oldScore[0] + 1;
            $game->addGoalTeamOne();
        }

        if ($this->goalManager->isGoalAlreadyAdded($game, $newScore)) {
            throw new GoalAlreadyAddedException();
        }

        if ($teamOne->contains($player)) {
            $team = $teamOne;
        } else {
            $team = $teamTwo;
        }

        $goal = $this->gameManager->addGoal($goal, $game, $player, $position, $team, $type, $players);

        if ($this->gameManager->checkAutoEnd($game)) {
            $game = $this->gameManager->end($game);
        }

        $this->em->persist($game);
        $this->em->flush();

        $result = array();
        $result['goal']   = $goal;
        $result['game']   = $game;
        $result['player'] = $player;

        return $result;
    }
}
