<?php

namespace KickFoo\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * KickFoo\Domain\Entity\Goal
 *
 * @ORM\Table(name="kickfoo_goals")
 * @ORM\Entity(repositoryClass="KickFoo\Domain\Repository\GoalRepository")
 */
class Goal implements \JsonSerializable
{
    const POSITION_BACK = 'back';
    const POSITION_FORWARD = 'forward';

    const TYPE_OWNGOAL = 'owngoal';
    const TYPE_REGULAR = 'regular';

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $position
     *
     * @ORM\Column(name="position", type="string", length=20, nullable=true)
     */
    private $position;

    /**
     * @var integer $goalsteamone
     *
     * @ORM\Column(name="goalsTeamOne", type="integer", nullable=false)
     */
    private $goalsTeamOne;

    /**
     * @var integer $goalsteamtwo
     *
     * @ORM\Column(name="goalsTeamTwo", type="integer", nullable=false)
     */
    private $goalsTeamTwo;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=20, nullable=true)
     */
    private $type;

    /**
     * @var \DateTime $time
     *
     * @ORM\Column(name="time", type="datetime", nullable=false)
     */
    private $time;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="playerId", referencedColumnName="id", nullable=true)
     * })
     */
    private $player;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team", referencedColumnName="id", nullable=false)
     * })
     */
    private $team;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="teamOneBack", referencedColumnName="id", nullable=false)
     * })
     */
    private $teamOneBack;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="teamOneForward", referencedColumnName="id", nullable=false)
     * })
     */
    private $teamOneForward;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="teamTwoBack", referencedColumnName="id", nullable=false)
     * })
     */
    private $teamTwoBack;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="teamTwoForward", referencedColumnName="id", nullable=false)
     * })
     */
    private $teamTwoForward;

    /**
     * @var Game
     *
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="goals")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gameId", referencedColumnName="id", nullable=false)
     * })
     */
    private $game;

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
     * Set position
     *
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set goalsTeamOne
     *
     * @param integer $goalsTeamOne
     */
    public function setGoalsTeamOne($goalsTeamOne)
    {
        $this->goalsTeamOne = $goalsTeamOne;
    }

    /**
     * Get goalsTeamOne
     *
     * @return integer
     */
    public function getGoalsTeamOne()
    {
        return $this->goalsTeamOne;
    }

    /**
     * Set goalsTeamTwo
     *
     * @param integer $goalsTeamTwo
     */
    public function setGoalsTeamTwo($goalsTeamTwo)
    {
        $this->goalsTeamTwo = $goalsTeamTwo;
    }

    /**
     * Get goalsTeamTwo
     *
     * @return integer
     */
    public function getGoalsTeamTwo()
    {
        return $this->goalsTeamTwo;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set player
     *
     * @param Player $player
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set teamOneBack
     *
     * @param Player $teamOneBack
     */
    public function setTeamOneBack(Player $teamOneBack)
    {
        $this->teamOneBack = $teamOneBack;
    }

    /**
     * Get teamOneBack
     *
     * @return Player
     */
    public function getTeamOneBack()
    {
        return $this->teamOneBack;
    }

    /**
     * Set teamOneForward
     *
     * @param Player $teamOneForward
     */
    public function setTeamOneForward(Player $teamOneForward)
    {
        $this->teamOneForward = $teamOneForward;
    }

    /**
     * Get teamOneForward
     *
     * @return Player
     */
    public function getTeamOneForward()
    {
        return $this->teamOneForward;
    }

    /**
     * Set teamTwoBack
     *
     * @param Player $teamTwoBack
     */
    public function setTeamTwoBack(Player $teamTwoBack)
    {
        $this->teamTwoBack = $teamTwoBack;
    }

    /**
     * Get teamTwoBack
     *
     * @return Player
     */
    public function getTeamTwoBack()
    {
        return $this->teamTwoBack;
    }

    /**
     * Set teamTwoForward
     *
     * @param Player $teamTwoForward
     */
    public function setTeamTwoForward(Player $teamTwoForward)
    {
        $this->teamTwoForward = $teamTwoForward;
    }

    /**
     * Get teamTwoForward
     *
     * @return Player
     */
    public function getTeamTwoForward()
    {
        return $this->teamTwoForward;
    }

    /**
     * Set game
     *
     * @param Game $game
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set team
     *
     * @param Team $team
     */
    public function setTeam(Team $team)
    {
        $this->team = $team;
    }

    /**
     * Get team
     *
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @return boolean
     */
    public function isOwnGoal()
    {
        return (bool) ($this->type === self::TYPE_OWNGOAL);
    }

    /**
     * @return boolean
     */
    public function isGoalBack()
    {
        return (bool) ($this->type === self::TYPE_REGULAR && $this->position === self::POSITION_BACK);
    }

    /**
     * @return boolean
     */
    public function isGoalForward()
    {
        return (bool) ($this->type === self::TYPE_REGULAR && $this->position === self::POSITION_FORWARD);
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
