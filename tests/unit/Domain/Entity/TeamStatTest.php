<?php

namespace KickFoo\Bundle\KickFooBundle\Tests;

use KickFoo\Domain\Entity\TeamStat;

class TeamStatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function setTotalGameCount()
    {
        $teamStat = new TeamStat;
        $teamStat->setTotalGameCount(99);

        $this->assertEquals(99, $teamStat->getTotalGameCount());
    }
    
    /**
     * @test
     */
    public function setTotalWinsCount()
    {
        $teamStat = new TeamStat;
        $teamStat->setTotalWinsCount(99);

        $this->assertEquals(99, $teamStat->getTotalWinsCount());
    }

    /**
     * @test
     */
    public function setTotalLosesCount()
    {
        $teamStat = new TeamStat;
        $teamStat->setTotalLosesCount(99);

        $this->assertEquals(99, $teamStat->getTotalLosesCount());
    }

    /**
     * @test
     */
    public function getWinPercentage()
    {
        $teamStat = new TeamStat;
        $teamStat->setTotalGameCount(10);
        $teamStat->setTotalWinsCount(5);

        $this->assertEquals(50, $teamStat->getWinPercentage());
    }

    /**
     * @test
     */
    public function getWinPercentageGameCountIsZero()
    {
        $teamStat = new TeamStat;
        $teamStat->setTotalGameCount(0);

        $this->assertEquals(0, $teamStat->getWinPercentage());
    }
}
