<?php

namespace KickFoo\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * KickFoo\Domain\Entity\Team
 *
 * @ORM\Table(name="kickfoo_teams")
 * @ORM\Entity(repositoryClass="KickFoo\Domain\Repository\TeamRepository")
 */
class Team implements \ArrayAccess, \JsonSerializable
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Player", inversedBy="teams")
     * @ORM\JoinTable(name="kickfoo_player_teams")
     **/
    private $players;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function addPlayer(Player $player)
    {
        $this->players[] = $player;
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
     * Set name
     *
     * @param  string $name
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Remove players
     *
     * @param Player $players
     */
    public function removePlayer(Player $players)
    {
        $this->players->removeElement($players);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @return Player
     */
    public function getPlayerOne()
    {
        return $this->players[0];
    }

    /**
     * @return Player
     */
    public function getPlayerTwo()
    {
        return $this->players[1];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->players[] = $value;
        } else {
            $this->players[$offset] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->players[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->players[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->players[$offset]) ? $this->players[$offset] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function contains(Player $player)
    {
        return $this->players->contains($player);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
