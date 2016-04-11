<?php

namespace KickFoo\Component\KickFoo\Tests\Service;

use KickFoo\Domain\Entity\Game;
use KickFoo\Domain\Entity\Player;
use KickFoo\Domain\Entity\PlayerStat;
use KickFoo\Domain\Entity\Team;
use KickFoo\Domain\Entity\TeamStat;
use KickFoo\Domain\Repository\GameRepository;
use KickFoo\Domain\Repository\GoalRepository;
use KickFoo\Domain\Service\StatCalculator;
use PHPUnit_Framework_TestCase;

class StatCalculatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getTopScores()
    {
        $player = new Player;
        $playerStat = new PlayerStat;
        
        $gameRepo = $this->getMockBuilder(GameRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $gameRepo->expects($this->once())
                        ->method('getTotalNumberOfGamesForPlayer')
                        ->with($this->equalTo($player))
                        ->will($this->returnValue(3));

        $goalRepo = $this->getMockBuilder(GoalRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $goalRepo->expects($this->once())
                        ->method('getBackGoalsForPlayerCount')
                        ->with($this->equalTo($player))
                        ->will($this->returnValue(5));

        $goalRepo->expects($this->once())
                        ->method('getForwardGoalsForPlayerCount')
                        ->with($this->equalTo($player))
                        ->will($this->returnValue(7));

        $goalRepo->expects($this->once())
                        ->method('getOwnGoalsForPlayerCount')
                        ->with($this->equalTo($player))
                        ->will($this->returnValue(2));

        $service = new StatCalculator($gameRepo, $goalRepo);
        $topscore = $service->getTopScore($player, $playerStat);

        $this->assertInstanceOf(PlayerStat::class, $topscore);

        $this->assertEquals($player, $topscore->getPlayer());
        $this->assertEquals(3, $topscore->getTotalGameCount());
        $this->assertEquals(5, $topscore->getBackPositionGoalCount());
        $this->assertEquals(7, $topscore->getForwardPositionGoalCount());
        $this->assertEquals(2, $topscore->getOwngoalCount());
        $this->assertEquals(((5+7) / 3), $topscore->getAverageGoalsPerGame());
        $this->assertEquals(12, $topscore->getTotalGoalCount());

        $this->assertEquals(0, $topscore->getTotalLosesCount());
        $this->assertEquals(0, $topscore->getTotalWinsCount());
        $this->assertEquals(0, $topscore->getWinPercentage());
    }

    /**
     * @test
     */
    public function getIndividualGameStats()
    {
        $player = new Player;
        $playerStat = new PlayerStat;
        
        $gameRepo = $this->getMockBuilder(GameRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $gameRepo->expects($this->once())
                        ->method('getTotalNumberOfGamesForPlayer')
                        ->with($this->equalTo($player))
                        ->will($this->returnValue(3));

        $gameRepo->expects($this->once())
                        ->method('getTotalNumberOfWonGamesForPlayer')
                        ->with($this->equalTo($player))
                        ->will($this->returnValue(2));

        $goalRepo = $this->getMockBuilder(GoalRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $service = new StatCalculator($gameRepo, $goalRepo);
        $stat = $service->getIndividualGameStats($player, $playerStat);

        $this->assertInstanceOf(PlayerStat::class, $stat);
        $this->assertEquals($player, $stat->getPlayer());
        $this->assertEquals(3, $stat->getTotalGameCount());
        $this->assertEquals(1, $stat->getTotalLosesCount());
        $this->assertEquals(2, $stat->getTotalWinsCount());
        $this->assertEquals(round((2/3)*100, 2), $stat->getWinPercentage());

        $this->assertEquals(0, $stat->getTotalGoalCount());
        $this->assertEquals(0, $stat->getBackPositionGoalCount());
        $this->assertEquals(0, $stat->getForwardPositionGoalCount());
        $this->assertEquals(0, $stat->getOwngoalCount());
        $this->assertEquals(0, $stat->getAverageGoalsPerGame());
    }

    /**
     * @test
     */
    public function getTeamGameStats()
    {
        $gameRepo = $this->getMockBuilder(GameRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $goalRepo = $this->getMockBuilder(GoalRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $player1 = new Player;
        $player2 = new Player;
        $team = new Team;
        $team->addPlayer($player1);
        $team->addPlayer($player2);
        $teamStat = new TeamStat;

        $service = new StatCalculator($gameRepo, $goalRepo);
        $service->getTeamGameStats($team, $teamStat);
    }

    /**
     * @test
     */
    public function previousGamesStatsTeamTwo100Percentage()
    {
        $gameRepo = $this->getMockBuilder(GameRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $goalRepo = $this->getMockBuilder(GoalRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $teamOne = new Team;
        $teamTwo = new Team;

        $game1 = new Game;
        $game1->setGoalsTeamOne(0);
        $game1->setGoalsTeamTwo(11);
        $game2 = new Game;
        $game2->setGoalsTeamOne(0);
        $game2->setGoalsTeamTwo(11);

        $games = array(
            $game1,
            $game2,
        );

        $service = new StatCalculator($gameRepo, $goalRepo);
        $result = $service->previousGamesStats($teamOne, $teamTwo, $games);

        $exptectedResult = array(
            'totalGames' => 2,
            'winsTeamOne' => 0,
            'winsTeamTwo' => 2,
            'teamWithMostWins' => $teamTwo,
            'winPercentage' => 100,
            'mostWinsCount' => 2,
        );
        $this->assertEquals($exptectedResult, $result);
    }

    /**
     * @test
     */
    public function previousGamesStatsGetTeamWithMostWins()
    {
        $gameRepo = $this->getMockBuilder(GameRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $goalRepo = $this->getMockBuilder(GoalRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        
        $teamA = new Team;
        $teamB = new Team;

        $game1 = new Game;
        $game1->setGoalsTeamOne(11);
        $game1->setGoalsTeamTwo(0);
        $game1->setTeamOne($teamA);
        $game1->setTeamTwo($teamB);

        $game2 = new Game;
        $game2->setGoalsTeamOne(11);
        $game2->setGoalsTeamTwo(0);
        $game2->setTeamOne($teamA);
        $game2->setTeamTwo($teamB);

        $game3 = new Game;
        $game3->setGoalsTeamOne(11);
        $game3->setGoalsTeamTwo(0);
        $game3->setTeamOne($teamA);
        $game3->setTeamTwo($teamB);

        $game4 = new Game;
        $game4->setGoalsTeamOne(11);
        $game4->setGoalsTeamTwo(0);
        $game4->setTeamOne($teamA);
        $game4->setTeamTwo($teamB);

        $game5 = new Game;
        $game5->setGoalsTeamOne(0);
        $game5->setGoalsTeamTwo(11);
        $game5->setTeamOne($teamA);
        $game5->setTeamTwo($teamB);

        $games = array(
            $game1,
            $game2,
            $game3,
            $game4,
            $game5,
        );

        $service = new StatCalculator($gameRepo, $goalRepo);
        $result = $service->previousGamesStats($teamA, $teamB, $games);

        $exptectedResult = array(
            'totalGames' => 5,
            'winsTeamOne' => 5,
            'winsTeamTwo' => 0,
            'teamWithMostWins' => $teamA,
            'winPercentage' => 100,
            'mostWinsCount' => 5,
        );
        $this->assertEquals($exptectedResult, $result);
    }
}
