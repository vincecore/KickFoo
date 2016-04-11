<?php

namespace KickFoo\Domain\Model;

use Doctrine\Common\Persistence\ObjectManager;
use KickFoo\Domain\Entity\Player;
use KickFoo\Domain\Entity\Team;
use KickFoo\Domain\Repository\PlayerRepository;
use KickFoo\Domain\Repository\PlayerRepositoryInterface;
use KickFoo\Domain\Repository\TeamRepository;
use KickFoo\Domain\Repository\TeamRepositoryInterface;

class TeamManager
{
    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * @var PlayerRepository
     */
    protected $playerRepository;

    /**
     * @param ObjectManager $em
     * @param TeamRepositoryInterface $teamRepository
     * @param PlayerRepositoryInterface $playerRepository
     */
    public function __construct(ObjectManager $em, TeamRepositoryInterface $teamRepository, PlayerRepositoryInterface $playerRepository)
    {
        $this->em = $em;
        $this->teamRepository = $teamRepository;
        $this->playerRepository = $playerRepository;
    }

    /**
     * Form a team from the given playerIds, if none is found a new team is created
     *
     * @param Player $playerOne
     * @param Player $playerTwo
     *
     * @return Team
     */
    public function formTeam(Player $playerOne, Player $playerTwo)
    {
        $team = $this->teamRepository->findTeamForPlayers($playerOne, $playerTwo);
        if (!$team instanceof Team) {
            // If no team is found, create a new team
            $team = $this->createTeam($playerOne, $playerTwo);
        }

        return $team;
    }

    /**
     * Create a team
     *
     * @param Player $playerOne
     * @param Player $playerTwo
     * @return Team
     */
    public function createTeam(Player $playerOne, Player $playerTwo)
    {
        $team = new Team();
        $namePlayerOne = $playerOne->getFirstname().' '.$playerOne->getLastname();
        $namePlayerTwo = $playerTwo->getFirstname().' '.$playerTwo->getLastname();
        $team->setName($namePlayerOne . ' & ' . $namePlayerTwo);
        $team->addPlayer($playerOne);
        $team->addPlayer($playerTwo);
        $this->em->persist($team);
        $this->em->flush();

        return $team;
    }
}
