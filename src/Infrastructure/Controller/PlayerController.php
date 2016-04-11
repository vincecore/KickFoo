<?php

namespace KickFoo\Infrastructure\Controller;

use KickFoo\Domain\Entity\Player;
use KickFoo\Domain\Entity\PlayerStat;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PlayerController extends Controller
{
    /**
     * @Route("/player/{id}", name="player_detail")
     */
    public function detailAction(Request $request, Player $player)
    {
        $gameRepository = $this->get('kickfoo.repository.game');
        $qb = $gameRepository->getAllGamesForPlayerQB($player);
        $qb->orderBy('g.start', 'DESC');
        $adapter = new DoctrineORMAdapter($qb);
        $games = new Pagerfanta($adapter);
        $games->setMaxPerPage(20);
        $games->setCurrentPage($request->query->get('page', 1));

        $statCalculator = $this->get('kickfoo.service.stat_calculator');
        $playerStat = new PlayerStat();
        $statCalculator->getIndividualGameStats($player, $playerStat);
        $statCalculator->getTopScore($player, $playerStat);

        return $this->render(
            ':Player:detail.html.twig',
            array(
                'player' => $player,
                'playerStat' => $playerStat,
                'games' => $games,
            )
        );
    }
}
