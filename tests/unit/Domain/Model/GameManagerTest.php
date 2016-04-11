<?php

namespace KickFoo\Component\KickFoo\Tests\Model;

use Doctrine\ORM\EntityManager;
use KickFoo\Domain\Entity\Game;
use KickFoo\Domain\Entity\Goal;
use KickFoo\Domain\Entity\Player;
use KickFoo\Domain\Entity\Team;
use KickFoo\Domain\Model\GameManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GameManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test to see if the start function returns the correct object
     *
     * @return void
     */
    public function testStartGameReturnsTheCorrectObject()
    {
        $startTime = new \DateTime();

        // Setup team one
        $teamOne = new Team();
        $playerOne = new Player();
        $teamOne->addPlayer($playerOne);
        $playerTwo = new Player();
        $teamOne->addPlayer($playerTwo);

        // Setup team two
        $teamTwo = new Team();
        $playerThree = new Player();
        $teamTwo->addPlayer($playerThree);
        $playerFour = new Player();
        $teamTwo->addPlayer($playerFour);
        
        $game = new Game();

        $entityManagerMock = $this->getMockBuilder(EntityManager::class, array('persist', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $entityManagerMock->expects($this->once())
                          ->method('persist')
                          ->with($this->identicalTo($game));

        $entityManagerMock->expects($this->once())
                          ->method('flush');

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $gameManager = new GameManager($entityManagerMock, $eventDispatcherMock);
        $actualGame = $gameManager->start($game, $teamOne, $teamTwo, $startTime);

        $this->assertInstanceOf(Game::class, $actualGame);
        $this->assertEquals($startTime, $actualGame->getStart());
        $this->assertEquals(0, $actualGame->getGoalsTeamOne());
        $this->assertEquals(0, $actualGame->getGoalsTeamTwo());
        $this->assertNull($actualGame->getEnd());
        $this->assertEquals($startTime, $actualGame->getStart());
        $this->assertEquals($playerOne, $actualGame->getPlayerOneTeamOne());
        $this->assertEquals($playerTwo, $actualGame->getPlayerTwoTeamOne());
        $this->assertEquals($playerThree, $actualGame->getPlayerOneTeamOne());
        $this->assertEquals($playerFour, $actualGame->getPlayerTwoTeamOne());
        $this->assertEquals($teamOne, $actualGame->getTeamOne());
        $this->assertEquals($teamTwo, $actualGame->getTeamTwo());
    }

    /**
     * Test if there is thrown an exception if no goal is found
     *
     * @expectedException        \KickFoo\Domain\Exception\NoGoalToRemoveException
     * @expectedExceptionMessage No goal to remove
     * @return void
     */
    public function testExceptionIfthereIsNoGoalToBeDeleted()
    {
        $entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('persist', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $game = new Game();

        $gameManager = new GameManager($entityManagerMock, $eventDispatcherMock);
        $gameManager->deleteLastGoal($game);
    }

    /**
     * Test to see if the goal get's removed
     *
     * @return void
     */
    public function testIftheGoalGetsRemoved()
    {
        $game = new Game();
        $goal = new Goal();
        $game->addGoal($goal);

        $entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('remove', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $entityManagerMock->expects($this->once())
                          ->method('remove')
                          ->with($this->identicalTo($goal));

        $entityManagerMock->expects($this->never())
                          ->method('flush');

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $gameManager = new GameManager($entityManagerMock, $eventDispatcherMock);
        $actualGame = $gameManager->deleteLastGoal($game);

        $goals = $actualGame->getGoals();
        $this->assertTrue($goals->isEmpty());
    }

    /**
     * Test to verify if the check auto end function works as planned
     *
     * @dataProvider dataProviderCheckAutoEnd
     */
    public function testCheckAutoEnd($goalsTeamOne, $goalsTeamTwo, $result)
    {
        $game = new Game();
        $game->setGoalsTeamOne($goalsTeamOne);
        $game->setGoalsTeamTwo($goalsTeamTwo);

        $entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('remove', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $gameManager = new GameManager($entityManagerMock, $eventDispatcherMock);
        $actualResult = $gameManager->checkAutoEnd($game);
        $this->assertEquals($result, $actualResult);
    }

    /**
     * Data provider for the testCheckAutoEnd function
     *
     * @return array
     */
    public function dataProviderCheckAutoEnd()
    {
        return array(
            array(0, 0, false),
            array(1, 0, false),
            array(2, 0, false),
            array(11, 0, true),
            array(0, 1, false),
            array(0, 2, false),
            array(0, 11, true),
            array(11, 11, false),
            array(11, 9, true),
            array(10, 11, false),
            array(9, 11, true),
            array(11, 10, false),
            array(12, 10, true),
            array(10, 12, true),
            array(11, 12, false),
            array(12, 11, false),
            array(13, 11, true),
            array(11, 13, true),
        );
    }

    /**
     * Test to see if the end game functionality works like it should
     * @return void
     */
    public function testEndGameWorksCorrect()
    {
        $game = new Game();
        $entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('remove', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $entityManagerMock->expects($this->once())
                          ->method('persist')
                          ->with($this->identicalTo($game));

        $entityManagerMock->expects($this->once())
                          ->method('flush');

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $gameManager = new GameManager($entityManagerMock, $eventDispatcherMock);
        $actualGame = $gameManager->end($game);
        $this->assertNotNull($actualGame->getEnd());

    }

    /**
     * Test the delete last goal from game with goals left
     * @return void
     */
    public function testDeleteLastGoalFromGameWithGoals()
    {
        $game = new Game();
        $goal = new Goal();
        $goal->setGoalsTeamOne(1);
        $goal->setGoalsTeamTwo(1);
        $game->addGoal($goal);

        $entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('remove', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $entityManagerMock->expects($this->once())
                          ->method('persist')
                          ->with($this->identicalTo($game));

        $entityManagerMock->expects($this->never())
                          ->method('flush');

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $gameManager = new GameManager($entityManagerMock, $eventDispatcherMock);
        $actualGame = $gameManager->deleteLastGoalFromGame($game);

        $this->assertEquals($goal->getGoalsTeamOne(), $actualGame->getGoalsTeamOne());
        $this->assertEquals($goal->getGoalsTeamTwo(), $actualGame->getGoalsTeamTwo());
    }

    /**
     * Test the delete last goal from game without goals left
     * @return void
     */
    public function testDeleteLastGoalFromGameWithoutGoals()
    {
        $game = new Game();

        $entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('remove', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $entityManagerMock->expects($this->once())
                          ->method('persist')
                          ->with($this->identicalTo($game));

        $entityManagerMock->expects($this->never())
                          ->method('flush');

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $gameManager = new GameManager($entityManagerMock, $eventDispatcherMock);
        $actualGame = $gameManager->deleteLastGoalFromGame($game);

        $this->assertEquals(0, $actualGame->getGoalsTeamOne());
        $this->assertEquals(0, $actualGame->getGoalsTeamTwo());
    }

    /**
     * Test the add goal function
     *
     * @return void
     */
    public function testAddGoal()
    {
        // Setup team one
        $team = new Team();
        $playerOne = new Player();
        $team->addPlayer($playerOne);
        $playerTwo = new Player();
        $team->addPlayer($playerTwo);

        $playerThree = new Player();
        $playerFour = new Player();

        $players = array(
            $playerOne,
            $playerTwo,
            $playerThree,
            $playerFour
        );
        $goal = new Goal();

        $entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('persist', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $entityManagerMock->expects($this->once())
                          ->method('persist')
                          ->with($this->identicalTo($goal));
        $entityManagerMock->expects($this->never())
                          ->method('flush');

        $game = new Game();
        $game->setGoalsTeamOne(2);
        $game->setGoalsTeamTwo(3);
        
        $position = Goal::POSITION_BACK;
        $type = Goal::TYPE_REGULAR;
        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $gameManager = new GameManager($entityManagerMock, $eventDispatcherMock);
        $actualGoal = $gameManager->addGoal($goal, $game, $playerOne, $position, $team, $type, $players);

        $this->assertInstanceOf(Goal::class, $actualGoal);
        $this->assertEquals($position, $actualGoal->getPosition());
        $this->assertEquals(2, $actualGoal->getGoalsTeamOne());
        $this->assertEquals(3, $actualGoal->getGoalsTeamTwo());
        $this->assertEquals($type, $actualGoal->getType());
        $this->assertInstanceOf('\DateTime', $actualGoal->getTime());
        $this->assertEquals($playerOne, $actualGoal->getPlayer());

        $this->assertEquals($playerOne, $actualGoal->getTeamOneBack());
        $this->assertEquals($playerTwo, $actualGoal->getTeamOneForward());
        $this->assertEquals($playerThree, $actualGoal->getTeamTwoBack());
        $this->assertEquals($playerFour, $actualGoal->getTeamTwoForward());

        $this->assertEquals($game, $actualGoal->getGame());
        $this->assertEquals($team, $actualGoal->getTeam());
    }
}
