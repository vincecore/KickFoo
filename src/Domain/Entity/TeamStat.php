<?php

namespace KickFoo\Domain\Entity;

/**
 */
class TeamStat
{
    /**
     * @var Team
     */
    private $team;

    /**
     * @var int
     */
    private $totalGameCount = 0;

    /**
     * @var int
     */
    private $totalWinsCount = 0;

    /**
     * @var int
     */
    private $totalLosesCount = 0;

    public function __construct()
    {
    }

    /**
     * @return \KickFoo\Domain\Entity\Team $team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param \KickFoo\Domain\Entity\Team $team
     */
    public function setTeam(Team $team)
    {
        $this->team = $team;
    }

    /**
     */
    public function getTotalGameCount()
    {
        return $this->totalGameCount;
    }

    /**
     */
    public function setTotalGameCount($totalGameCount)
    {
        $this->totalGameCount = $totalGameCount;
    }

    public function getTotalWinsCount()
    {
        return $this->totalWinsCount;
    }

    public function setTotalWinsCount($totalWinsCount)
    {
        $this->totalWinsCount = $totalWinsCount;
    }

    public function getTotalLosesCount()
    {
        return $this->totalLosesCount;
    }

    public function setTotalLosesCount($totalLosesCount)
    {
        $this->totalLosesCount = $totalLosesCount;
    }

    public function getWinPercentage()
    {
        if ($this->totalGameCount == 0) {
            return 0;
        }

        return round(($this->getTotalWinsCount() / $this->totalGameCount) * 100, 2);
    }
}
