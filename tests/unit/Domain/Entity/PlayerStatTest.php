<?php

namespace KickFoo\Bundle\KickFooBundle\Tests;

use KickFoo\Domain\Entity\Player;
use KickFoo\Domain\Entity\PlayerStat;

class PlayerStatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function setPlayer()
    {
        $player = new Player;
        $playerStat = new PlayerStat;
        $playerStat->setPlayer($player);

        $this->assertEquals($player, $playerStat->getPlayer());
    }

    /**
     * @test
     */
    public function setTotalGameCount()
    {
        $playerStat = new PlayerStat;
        $playerStat->setTotalGameCount(99);

        $this->assertEquals(99, $playerStat->getTotalGameCount());
    }

    /**
     * @test
     */
    public function setForwardPositionGoalCount()
    {
        $playerStat = new PlayerStat;
        $playerStat->setForwardPositionGoalCount(99);

        $this->assertEquals(99, $playerStat->getForwardPositionGoalCount());
    }

    /**
     * @test
     */
    public function setBackPositionGoalCount()
    {
        $playerStat = new PlayerStat;
        $playerStat->setBackPositionGoalCount(99);

        $this->assertEquals(99, $playerStat->getBackPositionGoalCount());
    }

    /**
     * @test
     */
    public function setOwngoalCount()
    {
        $playerStat = new PlayerStat;
        $playerStat->setOwngoalCount(99);

        $this->assertEquals(99, $playerStat->getOwngoalCount());
    }

    /**
     * @test
     */
    public function getAverageGoalsPerGame()
    {
        $playerStat = new PlayerStat;
        $playerStat->setTotalGameCount(3);
        $playerStat->setBackPositionGoalCount(1);
        $playerStat->setForwardPositionGoalCount(2);

        $this->assertEquals(3, $playerStat->getTotalGameCount());
        $this->assertEquals(1, $playerStat->getAverageGoalsPerGame());
    }

    /**
     * @test
     */
    public function getAverageGoalsPerGameIsZero()
    {
        $playerStat = new PlayerStat;
        $playerStat->setTotalGameCount(0);

        $this->assertEquals(0, $playerStat->getTotalGameCount());
        $this->assertEquals(0, $playerStat->getAverageGoalsPerGame());
    }

    /**
     * @test
     */
    public function getWinPercentage()
    {
        $playerStat = new PlayerStat;
        $playerStat->setTotalGameCount(10);
        $playerStat->setTotalWinsCount(5);

        $this->assertEquals(50, $playerStat->getWinPercentage());
    }

    /**
     * @test
     */
    public function getWinPercentageGoalsPerGameIsZero()
    {
        $playerStat = new PlayerStat;
        $playerStat->setTotalGameCount(0);

        $this->assertEquals(0, $playerStat->getWinPercentage());
    }
}
