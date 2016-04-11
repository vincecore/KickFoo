<?php

namespace KickFoo\Domain\Entity;

/**
 */
class PlayerStat
{
    /**
     * @var Player
     */
    private $player;

    /**
     * @var int
     */
    private $forwardPositionGoalCount = 0;

    /**
     * @var int
     */
    private $backPositionGoalCount = 0;

    /**
     * @var int
     */
    private $owngoalCount = 0;

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

    /**
     * @return \KickFoo\Domain\Entity\Player $player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param \KickFoo\Domain\Entity\Player $player
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;
    }

    public function getTotalGameCount()
    {
        return $this->totalGameCount;
    }

    public function setTotalGameCount($totalGameCount)
    {
        $this->totalGameCount = $totalGameCount;
    }

    public function getForwardPositionGoalCount()
    {
        return $this->forwardPositionGoalCount;
    }

    public function setForwardPositionGoalCount($forwardPositionGoalCount)
    {
        $this->forwardPositionGoalCount = $forwardPositionGoalCount;
    }

    public function getBackPositionGoalCount()
    {
        return $this->backPositionGoalCount;
    }

    public function setBackPositionGoalCount($backPositionGoalCount)
    {
        $this->backPositionGoalCount = $backPositionGoalCount;
    }

    public function setOwngoalCount($owngoalCount)
    {
        $this->owngoalCount = $owngoalCount;
    }

    public function getOwngoalCount()
    {
        return $this->owngoalCount;
    }

    public function getTotalGoalCount()
    {
        return $this->backPositionGoalCount + $this->forwardPositionGoalCount;
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

    public function getAverageGoalsPerGame()
    {
        if ($this->totalGameCount == 0) {
            return 0;
        }

        return round($this->getTotalGoalCount() / $this->totalGameCount, 2);
    }

    public function getWinPercentage()
    {
        if ($this->totalGameCount == 0) {
            return 0;
        }

        return round(($this->getTotalWinsCount() / $this->totalGameCount) * 100, 2);
    }
}
