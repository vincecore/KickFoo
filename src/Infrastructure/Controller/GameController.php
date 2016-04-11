<?php

namespace KickFoo\Infrastructure\Controller;

use KickFoo\Domain\Entity\Game;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GameController extends Controller
{
    /**
     * @Route("/game/{id}", name="game_detail")
     * @Template()
     */
    public function detailAction(Game $game)
    {
        $gameRepository = $this->get('kickfoo.repository.game');
        $statCalculator = $this->get('kickfoo.service.stat_calculator');

        $gamesBetweenTheseTeams = $gameRepository->findAllGamesForTeams($game->getTeamOne(), $game->getTeamTwo());
        $previousGamesStats = $statCalculator->previousGamesStats($game->getTeamOne(), $game->getTeamTwo(), $gamesBetweenTheseTeams);
        return $this->render(
            ':Game:detail.html.twig',
            array(
                'game' => $game,
                'previousGamesStats' => $previousGamesStats,
                'gamesBetweenTheseTeams' => $gamesBetweenTheseTeams,
            )
        );
    }

    /**
     * @Route("/live", name="game_live")
     * @Template()
     */
    public function liveAction()
    {
        $gameRepository = $this->get('kickfoo.repository.game');
        $game = $gameRepository->getLastOpenGame();
        return $this->render(
            ':Game:live.html.twig',
            array(
                'table' => 'kingfoo',
                'game' => $game
            )
        );
    }
}
