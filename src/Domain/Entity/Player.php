<?php
namespace KickFoo\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * KickFoo\Domain\Entity\Player
 *
 * @ORM\Table(name="kickfoo_players")
 * @ORM\Entity(repositoryClass="KickFoo\Domain\Repository\PlayerRepository")
 */
class Player implements JsonSerializable
{
    CONST GROUP_KINGFOO = 'kingfoo';
    CONST GROUP_GUEST = 'guest';

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $firstname
     *
     * @ORM\Column(name="firstname", type="string", length=100, nullable=true)
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @var string $lastname
     *
     * @ORM\Column(name="lastname", type="string", length=100, nullable=true)
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @var string $group
     *
     * @ORM\Column(name="group", type="string", length=20, nullable=true)
     * @Assert\NotBlank()
     */
    private $group;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active;

    /**
     * @ORM\ManyToMany(targetEntity="Team", mappedBy="players")
     * @ORM\JoinTable(name="kickfoo_player_teams")
     **/
    private $teams;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
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
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Is active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Get the full name of the player
     *
     * @return string
     */
    public function getName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function addTeam(Team $team)
    {
        $team->addPlayer($this);
        $this->teams[] = $team;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Remove teams
     *
     * @param Team $teams
     */
    public function removeTeam(Team $teams)
    {
        $this->teams->removeElement($teams);
    }

    /**
     * Get teams
     *
     * @return Collection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup($group)
    {
        $this->group = $group;
    }
}
