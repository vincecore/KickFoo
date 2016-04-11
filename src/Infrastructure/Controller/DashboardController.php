<?php

namespace KickFoo\Infrastructure\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard_index")
     * @Template()
     */
    public function indexAction()
    {
        $gameRepository = $this->get('kickfoo.repository.game');

        /*
        $goalRepository = $this->get('kickfoo.repository.goal');
        $lastGoal = $goalRepository->findLastGoalOfEndedGame();
        $lastGoalDate = $lastGoal->getTime();
        $response = new Response();
        $response->setLastModified($lastGoalDate);
        $response->setPublic();

        // Check that the Response is not modified for the given Request
        if ($response->isNotModified($this->getRequest())) {
            // return the 304 Response immediately
            return $response;
        }*/

        // Not cached, get data
        $statCollector = $this->get('kickfoo.service.stat_collector');
        $totalGames = $statCollector->getTotalGamesCount();
        $totalGoals = $statCollector->getTotalGoalsCount();
        $mostGoalsPlayer = $statCollector->findMostGoalsPerPlayer();
        $mostGamesPlayer = $statCollector->findMostGamesPerPlayer();
        $highestAvgScore = $statCollector->findPlayerWithHighestAvgScore();
        $highestWinPercentagePlayer = $statCollector->findPlayerWithHighestWinPercentage();
        $totalGameTime = $statCollector->getTotalGameTime();
        $averageGameTime = $statCollector->getAverageGameTime();

        if ($this->getRequest()->get('show', null)) {
            $limit = null;
        } else {
            $limit = 10;
        }

        $games = $gameRepository->findBy(array(), array('start' => 'desc'), $limit);

        return $this->render(
            ':Dashboard:index.html.twig',
            array(
                'games' => $games,
                'limit' => $limit,
                'totalGames' => $totalGames,
                'totalGoals' => $totalGoals,
                'mostGoalsPlayer' => $mostGoalsPlayer,
                'mostGamesPlayer' => $mostGamesPlayer,
                'highestAvgScore' => $highestAvgScore,
                'highestWinPercentagePlayer' => $highestWinPercentagePlayer,
                'totalGameTime' => $totalGameTime,
                'averageGameTime' => $averageGameTime,
            )
            //,$response
        );
    }
}
