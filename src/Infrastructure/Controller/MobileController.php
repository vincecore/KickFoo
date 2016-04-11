<?php

namespace KickFoo\Infrastructure\Controller;

use DateTime;
use KickFoo\Domain\Entity\Game;
use KickFoo\Domain\Entity\Goal;
use KickFoo\Domain\Entity\Player;
use KickFoo\Infrastructure\Form\PlayerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MobileController extends Controller
{
    /**
     * @Route("/", name="mobile")
     * @Template()
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('dashboard_index'));
    }

    /**
     * @Route("/mobile/", name="mobile_index")
     * @Template()
     */
    public function mobileAction(Request $request)
    {
        $playerRepository = $this->get('kickfoo.repository.player');
        $gameRepository = $this->get('kickfoo.repository.game');

        $player = new Player;
        $form = $this->createForm(new PlayerType(), $player);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $player->setActive(true);
                $em = $this->getDoctrine()->getManager();
                $em->persist($player);
                $em->flush();
                copy(dirname($this->get('kernel')->getRootDir()) . '/web/img/logo.png', dirname($this->get('kernel')->getRootDir()) . '/web/avatars/' . $player->getId() . '.jpg');
                return $this->redirect($this->generateUrl('mobile_index', array('players' => $player->getId())));
            }
        }

        return $this->render(
            ':Mobile:index.html.twig',
            array(
                'players'       => $playerRepository->findAllActiveKingFooPlayers(),
                'guests'        => $playerRepository->findAllActiveGuestPlayers(),
                'gamesToResume' => $gameRepository->findOpenGames(),
                'form'          => $form->createView(),
            )
        );
    }

    /**
     * @Route("/mobile/game", name="mobile_game")
     * @Route("/mobile/game/players/{players}", name="mobile_game_resume")
     * @Template()
     */
    public function gameAction(Request $request, $players = null)
    {
        $playerRepository = $this->get('kickfoo.repository.player');
        $players = $playerRepository->findAllActivePlayers();

        // Resume functionality
        $gameId = $request->get('resume');
        $game = false;
        if ($gameId) {
            $game = $this->getGame($gameId);
        }

        $randomPlayers = $this->getRequest()->get('players');
        if ($randomPlayers) {
            $randomPlayers = explode(',', $randomPlayers);
            shuffle($randomPlayers);
        }

        return $this->render(
            ':Mobile:game.html.twig',
            array(
                'players'       => $players,
                'randomPlayers' => $randomPlayers,
                'resumeData'    => $game
            )
        );
    }

    /**
     * Start the game and return the id
     *
     * @Route("/mobile/game/start", name="mobile_game_start")
     * @Method({"POST"})
     */
    public function startAction()
    {
        $gameManager = $this->get('kickfoo.model.game_manager');
        $teamManager = $this->get('kickfoo.model.team_manager');

        $playerIds = $this->getRequest()->get('positions');
        $players = $this->convertPlayerIdsToEntities($playerIds);
        $teamOne = $teamManager->formTeam($players[0], $players[1]);
        $teamTwo = $teamManager->formTeam($players[2], $players[3]);

        $game = $gameManager->start(new Game(), $teamOne, $teamTwo, new DateTime());
        $previousGamesStats = $this->get('kickfoo.service.stat_calculator')->previousGamesStats($game->getTeamOne(), $game->getTeamTwo());

        $response = array(
            'gameId' => $game->getId(),
            'previousGamesStats' => $previousGamesStats,
        );

        return new JsonResponse($response);
    }

    /**
     * End the game
     * @Route("/mobile/game/end", name="mobile_game_end")
     * @Method({"POST"})
     */
    public function endAction()
    {
        $game = $this->getGame($this->getRequest()->get('gameId'));

        $gameManager = $this->get('kickfoo.model.game_manager');
        $gameManager->end($game);

        return new JsonResponse($game->getId());
    }

    /**
     * Add a goal
     * @Route("/mobile/game/goal/add", name="mobile_game_goal_add")
     * @Method({"POST"})
     */
    public function addGoalAction()
    {
        $game = $this->getGame($this->getRequest()->get('gameId'));
        $position = $this->getRequest()->get('position');

        $playerId = $this->getRequest()->get('playerId');
        $playerRepository = $this->get('kickfoo.repository.player');
        $player = $playerRepository->find($playerId);

        $playerIds = $this->getRequest()->get('players');
        $players = $this->convertPlayerIdsToEntities($playerIds);

        $oldScore = explode(' - ', $this->getRequest()->get('score'));
        $type = $this->getRequest()->get('type');

        $goal = new Goal();
        $gameFacade = $this->get('kickfoo.model.game_facade');
        $result = $gameFacade->addGoal($goal, $game, $player, $position, $type, $oldScore, $players);

        return new JsonResponse($result);
    }

    /**
     * Convert an array of player ids into an array of player entities
     * @param  array  $playerIds
     * @return Player[]
     */
    protected function convertPlayerIdsToEntities(array $playerIds)
    {
        $playerRepository = $this->get('kickfoo.repository.player');
        $players = array();
        foreach ($playerIds as $playerId) {
            $players[] = $playerRepository->find($playerId);
        }
        return $players;
    }

    /**
     * Delete a goal
     * @Route("/mobile/game/goal/delete", name="mobile_game_goal_delete")
     * @Method({"POST"})
     */
    public function deleteGoalAction()
    {
        $game = $this->getGame($this->getRequest()->get('gameId'));
        $gameFacade = $this->get('kickfoo.model.game_facade');
        $game = $gameFacade->deleteLastGoal($game);

        return new JsonResponse($game);
    }

    /**
     * Get the game by it's ID
     *
     * @return Game
     */
    protected function getGame($gameId)
    {
        $gameRepository = $this->get('kickfoo.repository.game');
        return $gameRepository->find($gameId);
    }
}
