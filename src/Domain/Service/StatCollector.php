<?php

namespace KickFoo\Domain\Service;

use KickFoo\Domain\Entity\PlayerStat;
use KickFoo\Domain\Repository\GameRepositoryInterface;
use KickFoo\Domain\Repository\GoalRepositoryInterface;
use KickFoo\Domain\Repository\PlayerRepositoryInterface;

class StatCollector
{
    /**
     * @var GameRepositoryInterface
     */
    protected $gameRepository;

    /**
     * @var GoalRepositoryInterface
     */
    protected $goalRepository;

    /**
     * @var PlayerRepositoryInterface
     */
    protected $playerRepository;

    /**
     * @var StatCalculator
     */
    protected $statCalculatorService;

    protected $topscorers = null;
    protected $gameStats = null;
    
    public function __construct(GameRepositoryInterface $gameRepository, GoalRepositoryInterface $goalRepository, PlayerRepositoryInterface $playerRepository, StatCalculator $statCalculatorService)
    {
        $this->gameRepository = $gameRepository;
        $this->goalRepository = $goalRepository;
        $this->playerRepository = $playerRepository;
        $this->statCalculatorService = $statCalculatorService;
    }

    public function getTotalGamesCount()
    {
        return $this->gameRepository->getTotalGamesCount();
    }

    public function getTotalGoalsCount()
    {
        return $this->goalRepository->getTotalGoalsCount();
    }

    public function findMostGoalsPerPlayer()
    {
        return $this->goalRepository->findMostGoalsPerPlayer();
    }

    public function getTopScorers()
    {
        if (null !== $this->topscorers) {
            return $this->topscorers;
        }

        $players = $this->playerRepository->findAllActiveKingFooPlayers();
        
        foreach ($players as $player) {
            $this->topscorers[] = $this->statCalculatorService->getTopScore($player, new PlayerStat);
        }

        return $this->topscorers;
    }

    public function getGameStats()
    {
        if (null !== $this->gameStats) {
            return $this->gameStats;
        }

        $players = $this->playerRepository->findAllActiveKingFooPlayers();

        foreach ($players as $player) {
            $this->gameStats[] = $this->statCalculatorService->getIndividualGameStats($player, new PlayerStat);
        }

        return $this->gameStats;
    }

    public function findPlayerWithHighestAvgScore()
    {
        $topscorers = $this->getTopScorers();

        if (count($topscorers) === 0) {
            $topscorers = array();
        }

        $highestAvg = -1;
        $player = null;
        foreach ($topscorers as $topscorer) {
            if ($topscorer->getAverageGoalsPerGame() > $highestAvg) {
                $highestAvg = $topscorer->getAverageGoalsPerGame();
                $player = $topscorer->getPlayer();
            }
        }

        return array (
            'avg' => $highestAvg,
            'player' => $player,
        );
    }

    public function findPlayerWithHighestWinPercentage()
    {
        $gameStats = $this->getGameStats();

        if (count($gameStats) === 0) {
            $gameStats = array();
        }

        $highestWinPercentage = -1;
        $player = null;
        foreach ($gameStats as $gameStat) {
            if ($gameStat->getWinPercentage() > $highestWinPercentage) {
                $highestWinPercentage = $gameStat->getWinPercentage();
                $player = $gameStat->getPlayer();
            }
        }

        return array (
            'winPercentage' => $highestWinPercentage,
            'player' => $player,
        );
    }

    public function findMostGamesPerPlayer()
    {
        $players = $this->playerRepository->findAllActiveKingFooPlayers();

        $highestGamesCount = -1;
        $selectedPlayer = null;
        foreach ($players as $player) {
            $totalGamesPlayed = $this->gameRepository->getTotalNumberOfGamesForPlayer($player);
            if ($totalGamesPlayed > $highestGamesCount) {
                $highestGamesCount = $totalGamesPlayed;
                $selectedPlayer = $player;
            }
        }

        return array (
            'gameCount' => $highestGamesCount,
            'player' => $selectedPlayer,
        );
    }

    /**
     * @return array | array with keys days, hours, minutes, seconds
     */
    public function getTotalGameTime()
    {
        return $this->secondsToTime($this->getTotalGameTimeInSeconds());
    }

    /**
     * @return array | array with keys days, hours, minutes, seconds
     */
    public function getAverageGameTime()
    {
        return $this->secondsToTime($this->getAverageGameTimeInSeconds());
    }

    public function getTotalGameTimeInSeconds()
    {
        return $this->gameRepository->getTotalGameTime();
    }

    public function getAverageGameTimeInSeconds()
    {
        return $this->gameRepository->getAverageGameTime();
    }

    protected function secondsToTime($inputSeconds)
    {
        $secondsInAMinute = 60;
        $secondsInAnHour  = 60 * $secondsInAMinute;
        $secondsInADay    = 24 * $secondsInAnHour;

        // extract days
        $days = floor($inputSeconds / $secondsInADay);

        // extract hours
        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        // return the final array
        $obj = array(
            'days' => (int) $days,
            'hours' => (int) $hours,
            'minutes' => (int) $minutes,
            'seconds' => (int) $seconds,
        );
        return $obj;
    }
}
