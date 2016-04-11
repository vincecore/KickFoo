<?php

namespace KickFoo\Infrastructure\Subscriber;

use Buzz\Browser;
use KickFoo\Domain\Entity\Game;
use KickFoo\Domain\Entity\Goal;
use KickFoo\Domain\Event\GameEndedEvent;
use KickFoo\Domain\Event\GameEvents;
use KickFoo\Domain\Event\GameStartedEvent;
use KickFoo\Domain\Event\GoalDeletedEvent;
use KickFoo\Domain\Event\GoalScoredEvent;
use KickFoo\Domain\Service\StatCalculator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

class SlackSubscriber implements EventSubscriberInterface
{
    /**
     * @var Browser
     */
    protected $httpClient;

    /**
     * @var StatCalculator
     */
    protected $statCalculator;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $channel;

    /**
     * SlackSubscriber constructor.
     * @param Browser $httpClient
     * @param StatCalculator $statCalculator
     * @param RouterInterface $router
     * @param string $url
     * @param string $channel
     */
    public function __construct(Browser $httpClient, StatCalculator $statCalculator, RouterInterface $router, $url, $channel)
    {
        $this->httpClient = $httpClient;
        $this->statCalculator = $statCalculator;
        $this->router = $router;
        $this->url = $url;
        $this->channel = $channel;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            GameEvents::ON_ADD_TEAM_GOAL => array('onAddTeamGoal', 0),
            GameEvents::ON_DELETE_GOAL => array('onDeleteGoal', 0),
            GameEvents::ON_GAME_START => array('onStartGame', 0),
            GameEvents::ON_GAME_END => array('onEndGame', 0),
        );
    }

    /**
     * @param GoalScoredEvent $event
     */
    public function onAddTeamGoal(GoalScoredEvent $event)
    {
        $text = $this->getSpecialEventText($event->getGame(), $event->getGoal());

        if (null !== $text) {
            $this->callStack($text);
        }
    }

    /**
     * @param GoalDeletedEvent $event
     */
    public function onDeleteGoal(GoalDeletedEvent $event)
    {
        //$scoreText = $this->getScoreText($event->getGame());
        //$text = sprintf(':confused: Oops, wrong button! %s', $scoreText);
        //$this->callStack($text);
    }

    public function onStartGame(GameStartedEvent $event)
    {
        $previousGameStats = $this->statCalculator->previousGamesStats($event->getGame()->getTeamOne(), $event->getGame()->getTeamTwo());

        $game = $event->getGame();

        $teamOne = $game->getTeamOne()->getPlayerOne()->getFirstname() . ' & ' . $game->getTeamOne()->getPlayerTwo()->getFirstname();
        $teamTwo = $game->getTeamTwo()->getPlayerOne()->getFirstname() . ' & ' . $game->getTeamTwo()->getPlayerTwo()->getFirstname();

        $teamMostWins = $previousGameStats['teamWithMostWins']->getPlayerOne()->getFirstname().' & '.$previousGameStats['teamWithMostWins']->getPlayerTwo()->getFirstname();

        $liveUrl = $this->router->generate('game_live', array(), RouterInterface::ABSOLUTE_URL);

        $text = sprintf('Game started: *%s* vs *%s*. Follow it live @ %s. ', $teamOne, $teamTwo, $liveUrl);

        if ($previousGameStats['totalGames'] > 0) {
            if ($previousGameStats['winsTeamOne'] === $previousGameStats['winsTeamOne']) {
                $text .= sprintf('%s won %s of %s games (%s%%).', $teamMostWins, $previousGameStats['mostWinsCount'], $previousGameStats['totalGames'], $previousGameStats['winPercentage']);
            } else {
                $text .= sprintf('They played %s game(s) and both won %s games :sweat:.', $previousGameStats['totalGames'], $previousGameStats['mostWinsCount']);
            }
        }


        $this->callStack($text);
    }

    /**
     * @param GameEndedEvent $event
     */
    public function onEndGame(GameEndedEvent $event)
    {
        $game = $event->getGame();

        $scoreTeamOne = $game->getGoalsTeamOne();
        $scoreTeamTwo = $game->getGoalsTeamTwo();

        $winningTeam = $event->getGame()->getWinningTeam();

        $playerOne = $winningTeam->getPlayerOne();
        $playerTwo = $winningTeam->getPlayerTwo();

        $text = sprintf(':checkered_flag: Game ended, *%s* won! score: %s-%s', $playerOne->getFirstName().' & '.$playerTwo->getFirstName(), $scoreTeamOne, $scoreTeamTwo);
        $this->callStack($text, ':checkered_flag:');
    }

    /**
     * @param Game $game
     * @return string
     */
    private function getScoreText(Game $game)
    {
        $teamOne = $game->getTeamOne()->getPlayerOne()->getFirstname() . ' & ' . $game->getTeamOne()->getPlayerTwo()->getFirstname();
        $teamTwo = $game->getTeamTwo()->getPlayerOne()->getFirstname() . ' & ' . $game->getTeamTwo()->getPlayerTwo()->getFirstname();


        return sprintf('%s (%s-%s) %s', $teamOne, $game->getGoalsTeamOne(), $game->getGoalsTeamTwo(), $teamTwo);
    }

    private function callStack($text, $icon = ':soccer:')
    {
        $data = array(
            'text' => $text,
            'channel' => $this->channel,
            'username' => 'Kick Foo',
            'icon_emoji' => $icon,
        );
        $this->httpClient->post($this->url, array(), json_encode($data));
    }

    /**
     * @todo: Refactor to seperate classes? :)
     *
     * @param Game $game
     * @param Goal $goal
     * @return null|string
     */
    private function getSpecialEventText(Game $game, Goal $goal)
    {
        if ($game->getGoalsTeamOne() == 10 && $game->getGoalsTeamTwo() == 10) {
            return '10-10, Overtime! '.$this->getScoreText($game);
        }

        if (
            $game->getGoalsTeamOne() == 11 && $game->getGoalsTeamTwo() == 0
            ||
            $game->getGoalsTeamOne() == 0 && $game->getGoalsTeamTwo() == 11
        ) {
            return sprintf(':dizzy_face: Game added to the Wall of Shame (%s) :dizzy_face:! ', $this->router->generate('wallofshame', array(), RouterInterface::ABSOLUTE_URL)).$this->getScoreText($game);
        }
    }
}
