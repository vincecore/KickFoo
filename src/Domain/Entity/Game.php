<?php

namespace KickFoo\Domain\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use KickFoo\Domain\Exception\GameAlreadyEndedException;

/**
 * KickFoo\Domain\Entity\Game
 *
 * @ORM\Table(name="kickfoo_games")
 * @ORM\Entity(repositoryClass="KickFoo\Domain\Repository\GameRepository")
 */

class Game implements \JsonSerializable
{
    const TABLE_KINGFOO = 'kingfoo';
    const TABLE_COMBELL = 'combell';

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var datetime $start
     *
     * @ORM\Column(name="start", type="datetime", nullable=false)
     */
    private $start;

    /**
     * @var datetime $end
     *
     * @ORM\Column(name="end", type="datetime", nullable=true)
     */
    private $end;

    /**
     * @var smallint $goalsteamone
     *
     * @ORM\Column(name="goalsTeamOne", type="smallint", nullable=true)
     */
    private $goalsTeamOne;

    /**
     * @var smallint $goalsteamtwo
     *
     * @ORM\Column(name="goalsTeamTwo", type="smallint", nullable=true)
     */
    private $goalsTeamTwo;

     /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="playerOneTeamOne", referencedColumnName="id", nullable=false)
     * })
     */
    private $playerOneTeamOne;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="playerTwoTeamOne", referencedColumnName="id", nullable=false)
     * })
     */
    private $playerTwoTeamOne;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="playerOneTeamTwo", referencedColumnName="id", nullable=false)
     * })
     */
    private $playerOneTeamTwo;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="playerTwoTeamTwo", referencedColumnName="id", nullable=false)
     * })
     */
    private $playerTwoTeamTwo;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="teamOne", referencedColumnName="id", nullable=false)
     * })
     */
    private $teamOne;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="teamTwo", referencedColumnName="id", nullable=false)
     * })
     */
    private $teamTwo;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Goal", mappedBy="game")
     */
    private $goals;

    public function __construct()
    {
        $this->goals = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     */
    public function setStart(\DateTime $start = null)
    {
        $this->start = $start;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     */
    public function setEnd(\DateTime $end = null)
    {
        $this->end = $end;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set goalsTeamOne
     *
     * @param int $goalsTeamOne
     */
    public function setGoalsTeamOne($goalsTeamOne)
    {
        $this->goalsTeamOne = trim($goalsTeamOne);
    }

    /**
     * Get goalsTeamOne
     *
     * @return int
     */
    public function getGoalsTeamOne()
    {
        return $this->goalsTeamOne;
    }

    /**
     * Set goalsTeamTwo
     *
     * @param int $goalsTeamTwo
     */
    public function setGoalsTeamTwo($goalsTeamTwo)
    {
        $this->goalsTeamTwo = trim($goalsTeamTwo);
    }

    /**
     * Get goalsTeamTwo
     *
     * @return int
     */
    public function getGoalsTeamTwo()
    {
        return $this->goalsTeamTwo;
    }

    /**
     * Set playerOneTeamOne
     *
     * @param Player $playerOneTeamOne
     */
    public function setPlayerOneTeamOne(Player $playerOneTeamOne)
    {
        $this->playerOneTeamOne = $playerOneTeamOne;
    }

    /**
     * Get playerOneTeamOne
     *
     * @return Player
     */
    public function getPlayerOneTeamOne()
    {
        return $this->playerOneTeamOne;
    }

    /**
     * Set playerTwoTeamOne
     *
     * @param Player $playerTwoTeamOne
     */
    public function setPlayerTwoTeamOne(Player $playerTwoTeamOne)
    {
        $this->playerTwoTeamOne = $playerTwoTeamOne;
    }

    /**
     * Get playerTwoTeamOne
     *
     * @return Player
     */
    public function getPlayerTwoTeamOne()
    {
        return $this->playerTwoTeamOne;
    }

    /**
     * Set playerOneTeamTwo
     *
     * @param Player $playerOneTeamTwo
     */
    public function setPlayerOneTeamTwo(Player $playerOneTeamTwo)
    {
        $this->playerOneTeamTwo = $playerOneTeamTwo;
    }

    /**
     * Get playerOneTeamTwo
     *
     * @return Player
     */
    public function getPlayerOneTeamTwo()
    {
        return $this->playerOneTeamTwo;
    }

    /**
     * Set playerTwoTeamTwo
     *
     * @param Player $playerTwoTeamTwo
     */
    public function setPlayerTwoTeamTwo(Player $playerTwoTeamTwo)
    {
        $this->playerTwoTeamTwo = $playerTwoTeamTwo;
    }

    /**
     * Get playerTwoTeamTwo
     *
     * @return Player
     */
    public function getPlayerTwoTeamTwo()
    {
        return $this->playerTwoTeamTwo;
    }

    /**
     * Set teamOne
     *
     * @param Team $teamOne
     */
    public function setTeamOne(Team $teamOne)
    {
        $this->teamOne = $teamOne;
    }

    /**
     * Get teamOne
     *
     * @return Team
     */
    public function getTeamOne()
    {
        return $this->teamOne;
    }

    /**
     * Set teamTwo
     *
     * @param Team $teamTwo
     */
    public function setTeamTwo(Team $teamTwo)
    {
        $this->teamTwo = $teamTwo;
    }

    /**
     * Get teamTwo
     *
     * @return Team
     */
    public function getTeamTwo()
    {
        return $this->teamTwo;
    }

    /**
     * Add goals
     *
     * @param Goal $goals
     */
    public function addGoal(Goal $goals)
    {
        $this->goals[] = $goals;
    }

    /**
     * Get goals
     *
     * @return Collection
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * Get all the players of team one
     * @return array
     */
    public function getPlayersTeamOne()
    {
        return array(
            $this->getPlayerOneTeamOne(),
            $this->getPlayerTwoTeamOne(),
        );
    }

    /**
     * Get all the players of team two
     * @return array
     */
    public function getPlayersTeamTwo()
    {
        return array(
            $this->getPlayerOneTeamTwo(),
            $this->getPlayerTwoTeamTwo(),
        );
    }

    /**
     * @return boolean
     */
    public function hasTeamOneWon()
    {
        return (bool) ($this->getGoalsTeamOne() > $this->getGoalsTeamTwo());
    }

    /**
     * @return boolean
     */
    public function hasTeamTwoWon()
    {
        return (bool) ($this->getGoalsTeamTwo() > $this->getGoalsTeamOne());
    }

    /**
     * @return Team
     */
    public function getWinningTeam()
    {
        if ($this->getGoalsTeamOne() > $this->getGoalsTeamTwo()) {
            return $this->getTeamOne();
        } else {
            return $this->getTeamTwo();
        }
    }

    /**
     * Remove goals
     *
     * @param Goal $goals
     */
    public function removeGoal(Goal $goals)
    {
        $this->goals->removeElement($goals);
    }

    /**
     * Check if the game has been ended
     * @return boolean
     */
    public function hasEnded()
    {
        return (bool) (!is_null($this->getEnd()));
    }

    public function addGoalTeamOne()
    {
        if ($this->hasEnded()) {
            throw new GameAlreadyEndedException();
        }
        $this->setGoalsTeamOne($this->getGoalsTeamOne() + 1);
    }

    public function addGoalTeamTwo()
    {
        if ($this->hasEnded()) {
            throw new GameAlreadyEndedException();
        }
        $this->setGoalsTeamTwo($this->getGoalsTeamTwo() + 1);
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
