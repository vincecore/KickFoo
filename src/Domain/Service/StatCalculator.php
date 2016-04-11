<?php

namespace KickFoo\Domain\Service;

use KickFoo\Domain\Entity\Game;
use KickFoo\Domain\Entity\Player;
use KickFoo\Domain\Entity\Team;
use KickFoo\Domain\Repository\GameRepositoryInterface;
use KickFoo\Domain\Repository\GoalRepositoryInterface;
use KickFoo\Domain\Entity\PlayerStat;
use KickFoo\Domain\Entity\TeamStat;

class StatCalculator
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
     * @param GameRepositoryInterface $gameRepository
     * @param GoalRepositoryInterface $goalRepository
     */
    public function __construct(GameRepositoryInterface $gameRepository, GoalRepositoryInterface $goalRepository)
    {
        $this->gameRepository = $gameRepository;
        $this->goalRepository = $goalRepository;
    }

    /**
     * Get the topscores stats for a player
     *
     * @param  Player $player
     * @param  PlayerStat $playerStat
     * @param  \DateTime $startDate
     * @param  \DateTime $endDate
     *
     * @return PlayerStat
     */
    public function getTopScore(Player $player, PlayerStat $playerStat, \DateTime $startDate = null, \DateTime $endDate = null)
    {
        $totalGamesPlayed = $this->gameRepository->getTotalNumberOfGamesForPlayer($player, $startDate, $endDate);
        $backPositionGoalCount = $this->goalRepository->getBackGoalsForPlayerCount($player, $startDate, $endDate);
        $forwardPositionGoalCount = $this->goalRepository->getForwardGoalsForPlayerCount($player, $startDate, $endDate);
        $owngoalCount = $this->goalRepository->getOwnGoalsForPlayerCount($player, $startDate, $endDate);

        $playerStat->setPlayer($player);
        $playerStat->setTotalGameCount($totalGamesPlayed);
        $playerStat->setBackPositionGoalCount($backPositionGoalCount);
        $playerStat->setForwardPositionGoalCount($forwardPositionGoalCount);
        $playerStat->setOwngoalCount($owngoalCount);

        return $playerStat;
    }

    /**
     * Get the individual player stats
     *
     * @param  Player $player
     * @param  PlayerStat $playerStat
     * @param  \DateTime $startDate
     * @param  \DateTime $endDate
     *
     * @return PlayerStat
     */
    public function getIndividualGameStats(Player $player, PlayerStat $playerStat, \DateTime $startDate = null, \DateTime $endDate = null)
    {
        $gamesCount = $this->gameRepository->getTotalNumberOfGamesForPlayer($player, $startDate, $endDate);
        $wins = $this->gameRepository->getTotalNumberOfWonGamesForPlayer($player, $startDate, $endDate);

        $playerStat->setPlayer($player);
        $playerStat->setTotalGameCount($gamesCount);
        $playerStat->setTotalWinsCount($wins);
        $playerStat->setTotalLosesCount($gamesCount - $wins);

        return $playerStat;
    }

    /**
     * Get Team Stats
     *
     * @param  Team $team
     * @param  TeamStat $teamStat
     * @param  \DateTime $startDate
     * @param  \DateTime $endDate
     *
     * @return TeamStat
     */
    public function getTeamGameStats(Team $team, TeamStat $teamStat, \DateTime $startDate = null, \DateTime $endDate = null)
    {
        $gamesCount = $this->gameRepository->getTotalNumberOfGamesForTeam($team, $startDate, $endDate);
        $wins = $this->gameRepository->getTotalNumberOfWonGamesForTeam($team, $startDate, $endDate);

        $teamStat->setTeam($team);
        $teamStat->setTotalGameCount($gamesCount);
        $teamStat->setTotalWinsCount($wins);
        $teamStat->setTotalLosesCount($gamesCount - $wins);

        return $teamStat;
    }

    /**
     * @param Team $teamOne
     * @param Team $teamTwo
     * @param Game[] $gamesBetweenTheseTeams
     *
     * @return array
     */
    public function previousGamesStats(Team $teamOne, Team $teamTwo, array $gamesBetweenTheseTeams = null)
    {
        if ($gamesBetweenTheseTeams == null) {
            $gamesBetweenTheseTeams = $this->gameRepository->findAllGamesForTeams($teamOne, $teamTwo);
        }
        
        // Get the wins per team
        $winsTeamOne = 0;
        $winsTeamTwo = 0;
        foreach ($gamesBetweenTheseTeams as $game) {
            if ($game->getWinningTeam() == $teamOne) {
                $winsTeamOne++;
            } else {
                $winsTeamTwo++;
            }
        }

        // Get team with most wins
        if ($winsTeamOne > $winsTeamTwo) {
            $teamWithMostWins = $teamOne;
            $mostWinsCount = $winsTeamOne;
        } else {
            $teamWithMostWins = $teamTwo;
            $mostWinsCount = $winsTeamTwo;
        }

        $totalGames = count($gamesBetweenTheseTeams);
        if ($totalGames > 0) {
            $winPercentage = round(($mostWinsCount/$totalGames)*100);
        } else {
            $winPercentage = 0;
        }

        return array (
            'totalGames' => $totalGames,
            'winsTeamOne' => $winsTeamOne,
            'winsTeamTwo' => $winsTeamTwo,
            'teamWithMostWins' => $teamWithMostWins,
            'winPercentage' => $winPercentage,
            'mostWinsCount' => $mostWinsCount,
        );
    }
}
