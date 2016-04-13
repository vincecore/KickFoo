<?php

namespace KickFoo\Infrastructure\Controller;

use DateTime;
use Exception;
use KickFoo\Domain\Entity\Goal;
use KickFoo\Domain\Entity\PlayerStat;
use KickFoo\Domain\Entity\TeamStat;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class StatController extends Controller
{
    /**
     * @Route("/stats/topscorers", name="stats_topscorers")
     * @Template()
     */
    public function topscorersAction()
    {
        $response = $this->getCacheResponse();

        // Check that the Response is not modified for the given Request
        if ($response->isNotModified($this->getRequest())) {
            // return the 304 Response immediately
            return $response;
        }

        $dates = $this->getDatesForFilter();
        $playerRepository = $this->get('kickfoo.repository.player');
        $players = $playerRepository->findAllActiveKingFooPlayers();
        $topscorers = array();

        $statCalculator = $this->get('kickfoo.service.stat_calculator');
        foreach ($players as $player) {
            $topscorers[] = $statCalculator->getTopScore($player, new PlayerStat, $dates['start'], $dates['end']);
        }

        return $this->render(
            ':Stat:topscorers.html.twig',
            array(
                'topscorers'    => $topscorers,
                'dates'         => $dates,
                'currentRoute'  => $this->generateUrl('stats_topscorers')
            ),
            $response
        );
    }

    /**
     * @Route("/stats/games", name="stats_games")
     * @Template()
     */
    public function gamesAction()
    {
        $response = $this->getCacheResponse();

        // Check that the Response is not modified for the given Request
        if ($response->isNotModified($this->getRequest())) {
            // return the 304 Response immediately
            return $response;
        }
        
        $dates = $this->getDatesForFilter();
        $playerRepository = $this->get('kickfoo.repository.player');
        $players = $playerRepository->findAllActiveKingFooPlayers();
        $gameStats = array();

        $statCalculator = $this->get('kickfoo.service.stat_calculator');
        foreach ($players as $player) {
            $gameStats[] = $statCalculator->getIndividualGameStats($player, new PlayerStat, $dates['start'], $dates['end']);
        }

        return $this->render(
            'KickFooKickFooBundle:Stat:games.html.twig',
            array(
                'gamestats'    => $gameStats,
                'dates'        => $dates,
                'currentRoute' => $this->generateUrl('stats_games')
            ),
            $response
        );
    }

    /**
     * @Route("/stats/teams", name="stats_teams")
     * @Template()
     */
    public function teamsAction()
    {
        $response = $this->getCacheResponse();

        // Check that the Response is not modified for the given Request
        if ($response->isNotModified($this->getRequest())) {
            // return the 304 Response immediately
            return $response;
        }

        $dates = $this->getDatesForFilter();
        $teamRepository = $this->get('kickfoo.repository.team');
        $teams = $teamRepository->findAll();

        $statCalculator = $this->get('kickfoo.service.stat_calculator');
        $teamsStats = array();
        foreach ($teams as $team) {
            $teamsStats[] = $statCalculator->getTeamGameStats($team, new TeamStat, $dates['start'], $dates['end']);
        }

        return $this->render(
            'KickFooKickFooBundle:Stat:teams.html.twig',
            array(
                'teamstats'     => $teamsStats,
                'dates'         => $dates,
                'currentRoute'  => $this->generateUrl('stats_teams')
            ),
            $response
        );
    }

    /**
     * Get dates to filter the stats from request or from session
     * @return array
     */
    private function getDatesForFilter()
    {
        $start = $this->getRequest()->get('start', null);
        $end = $this->getRequest()->get('end', null);
        $range = $this->getRequest()->get('range', null);
        if ($start && $end) {
            try {
                $start = new DateTime($start);
                $start->setTime(0, 0, 0);
                $end = new DateTime($end);
                $end->setTime(23, 59, 59);
            } catch (Exception $e) {
                $start = null;
                $end = null;
            }
        }

        $dates = array();
        $session = $this->getRequest()->getSession();
        if ($start && $end) {
            $dates['start'] = $start;
            $dates['end'] = $end;
            $dates['range'] = $range;

            $session->set('stat-time-filter', $dates);
        } else {
            $dates = $session->get('stat-time-filter');
            if (!$dates) {
                $dates['start'] = null;
                $dates['end'] = null;
                $dates['range'] = null;
            }
        }

        if ($dates['range'] == 'Custom Range') {
            $dates['range'] = null;
        }
        if ($dates['range'] == 'From The Start') {
            $dates['start'] = null;
            $dates['end'] = null;
            $dates['range'] = null;
        }

        return $dates;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function getCacheResponse()
    {
        $response = new Response();
        $datesFilter = $this->getDatesForFilter();

        // No cache when filters are active
        // @todo, always put filters in the url
        if ($datesFilter['start'] == null && $datesFilter['end'] == null) {
            $goalRepository = $this->get('kickfoo.repository.goal');
            $lastGoal = $goalRepository->findLastGoalOfEndedGame();

            if ($lastGoal instanceof Goal) {
                $lastGoalDate = $lastGoal->getTime();

                $response->setLastModified($lastGoalDate);
                $response->setPublic();
            }
        }

        return $response;
    }
}
