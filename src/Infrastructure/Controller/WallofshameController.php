<?php

namespace KickFoo\Infrastructure\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WallofshameController extends Controller
{
    /**
     * @Route("/wallofshame", name="wallofshame")
     * @Template(":Wallofshame:overview.html.twig")
     */
    public function overviewAction()
    {
        $gameRepository = $this->get('kickfoo.repository.game');
        $games = $gameRepository->findGamesWithScore(0, 11);

        return array (
            'games' => $games,
        );
    }
}
