<?php

namespace KickFoo\Component\KickFoo\Tests\Model;

use DateTime;
use KickFoo\Domain\Entity\Game;
use KickFoo\Domain\Entity\Goal;
use KickFoo\Domain\Entity\Player;
use KickFoo\Domain\Entity\Team;
use KickFoo\Domain\Model\GameFacade;
use KickFoo\Domain\Model\GameManager;
use KickFoo\Domain\Model\GoalManager;
use KickFoo\Domain\Model\TeamManager;
use PHPUnit_Framework_TestCase;

class GameFacadeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test to see if the deleteLastGoal function calls the correct
     * underlying methods
     *
     * @return void
     */
    public function testIfDeleteLastGoalCallsTheCorrectFunctions()
    {
        $game = new Game();

        $gameManagerMock = $this->getMockBuilder(GameManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $goalManagerMock = $this->getMockBuilder(GoalManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $gameManagerMock->expects($this->once())
                        ->method('deleteLastGoal')
                        ->with($this->equalTo($game))
                        ->will($this->returnValue($game));

        $gameManagerMock->expects($this->once())
                        ->method('deleteLastGoalFromGame')
                        ->with($this->equalTo($game))
                        ->will($this->returnValue($game));

        $teamManagerMock = $this->getMockBuilder(TeamManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();


        $entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('remove', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $entityManagerMock->expects($this->once())
                          ->method('flush');

        $eventDispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $facade = new GameFacade($entityManagerMock, $gameManagerMock, $teamManagerMock, $goalManagerMock, $eventDispatcherMock);
        $facade->deleteLastGoal($game);
    }

    public function testAddGoalTeamOne()
    {
        $game = new Game();
        $game->setGoalsTeamOne(8);
        $game->setGoalsTeamTwo(6);
        $goal = new Goal();
        $playerOne = new Player();
        $playerTwo = new Player();
        $playerTwo->setFirstname('John');
        $playerTwo->setLastname('Doe');
        $playerThree = new Player();
        $playerFour = new Player();
        $players = array($playerOne, $playerTwo, $playerThree, $playerFour);
        $position = Goal::POSITION_FORWARD;
        $type = Goal::TYPE_REGULAR;
        $oldScore = array(8, 6);

        $teamOne = new Team();
        $teamOne->addPlayer($playerOne);
        $teamOne->addPlayer($playerTwo);
        $teamTwo = new Team();
        $teamTwo->addPlayer($playerThree);
        $teamTwo->addPlayer($playerFour);

        $expectedGoal = new Goal();
        $expectedGoal->setType($type);
        $expectedGoal->setGoalsTeamOne(9);
        $expectedGoal->setGoalsTeamTwo(6);

        $gameManagerMock = $this->getMockBuilder(GameManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $goalManagerMock = $this->getMockBuilder(GoalManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $expectedScore = array(9, 6);
        $goalManagerMock->expects($this->once())
                        ->method('isGoalAlreadyAdded')
                        ->with($this->equalTo($game), $this->equalTo($expectedScore))
                        ->will($this->returnValue(false));

        $gameManagerMock->expects($this->once())
                        ->method('addGoal')
                        ->with(
                            $this->identicalTo($goal),
                            $this->identicalTo($game),
                            $this->identicalTo($playerTwo),
                            $this->equalTo($position),
                            $this->equalTo($teamOne),
                            $this->equalTo($type),
                            $this->identicalTo($players)
                        )
                        ->will($this->returnValue($expectedGoal));

        $gameManagerMock->expects($this->once())
                        ->method('checkAutoEnd')
                        ->with($this->equalTo($game))
                        ->will($this->returnValue(false));

        $gameManagerMock->expects($this->never())
                        ->method('end');

        $teamManagerMock = $this->getMockBuilder(TeamManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $teamManagerMock->expects($this->any())
                        ->method('formTeam')
                        ->will($this->onConsecutiveCalls($teamOne, $teamTwo));

        $entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('remove', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $entityManagerMock->expects($this->once())
                          ->method('flush');
        $entityManagerMock->expects($this->once())
                          ->method('persist');
        
        $eventDispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')
                                  ->disableOriginalConstructor()
                                  ->getMock();
        
        $facade = new GameFacade($entityManagerMock, $gameManagerMock, $teamManagerMock, $goalManagerMock, $eventDispatcherMock);
        $actualResult = $facade->addGoal($goal, $game, $playerTwo, $position, $type, $oldScore, $players);

        $this->assertArrayHasKey('goal', $actualResult);
        $this->assertArrayHasKey('game', $actualResult);
        $this->assertArrayHasKey('player', $actualResult);

        $this->assertEquals(9, $actualResult['goal']->getGoalsTeamOne());
        $this->assertEquals(6, $actualResult['goal']->getGoalsTeamTwo());
        $this->assertEquals(9, $actualResult['game']->getGoalsTeamOne());
        $this->assertEquals(6, $actualResult['game']->getGoalsTeamTwo());
        $this->assertEquals('John', $actualResult['player']->getFirstname());
        $this->assertEquals('Doe', $actualResult['player']->getLastname());
        $this->assertNull($actualResult['game']->getEnd());
    }

    /**
     * Test to see if checkAutoEnd returns true, the game is being ended
     */
    public function testAddGoalAutoEnd()
    {
        $game = new Game();
        $game->setGoalsTeamOne(9);
        $game->setGoalsTeamTwo(10);
        $endedGame = new Game();
        $endedGame->setGoalsTeamOne(9);
        $endedGame->setGoalsTeamTwo(11);
        $endedGame->setEnd(new \DateTime());
        $goal = new Goal();
        $playerOne = new Player();
        $playerTwo = new Player();
        $playerThree = new Player();
        $playerThree->setFirstname('John');
        $playerThree->setLastname('Doe');
        $playerFour = new Player();
        $players = array($playerOne, $playerTwo, $playerThree, $playerFour);
        $position = Goal::POSITION_BACK;
        $type = Goal::TYPE_REGULAR;
        $oldScore = array(9, 10);

        $teamOne = new Team();
        $teamOne->addPlayer($playerOne);
        $teamOne->addPlayer($playerTwo);
        $teamTwo = new Team();
        $teamTwo->addPlayer($playerThree);
        $teamTwo->addPlayer($playerFour);

        $expectedGoal = new Goal();
        $expectedGoal->setType($type);
        $expectedGoal->setGoalsTeamOne(9);
        $expectedGoal->setGoalsTeamTwo(11);

        $gameManagerMock = $this->getMockBuilder(GameManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $goalManagerMock = $this->getMockBuilder(GoalManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $expectedScore = array(9, 11);
        $goalManagerMock->expects($this->once())
                        ->method('isGoalAlreadyAdded')
                        ->with($this->equalTo($game), $this->equalTo($expectedScore))
                        ->will($this->returnValue(false));

        $gameManagerMock->expects($this->once())
                        ->method('addGoal')
                        ->with(
                            $this->identicalTo($goal),
                            $this->identicalTo($game),
                            $this->identicalTo($playerThree),
                            $this->equalTo($position),
                            $this->equalTo($teamTwo),
                            $this->equalTo($type),
                            $this->identicalTo($players)
                        )
                        ->will($this->returnValue($expectedGoal));

        $gameManagerMock->expects($this->once())
                        ->method('checkAutoEnd')
                        ->with($this->equalTo($game))
                        ->will($this->returnValue(true));

        $gameManagerMock->expects($this->once())
                        ->method('end')
                        ->with($this->equalTo($game))
                        ->will($this->returnValue($endedGame));

        $teamManagerMock = $this->getMockBuilder(TeamManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $teamManagerMock->expects($this->any())
                        ->method('formTeam')
                        ->will($this->onConsecutiveCalls($teamOne, $teamTwo));

        $entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('remove', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $entityManagerMock->expects($this->once())
                          ->method('flush');
        $entityManagerMock->expects($this->once())
                          ->method('persist');

        $eventDispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')
                                  ->disableOriginalConstructor()
                                  ->getMock();
        
        $facade = new GameFacade($entityManagerMock, $gameManagerMock, $teamManagerMock, $goalManagerMock, $eventDispatcherMock);
        $actualResult = $facade->addGoal($goal, $game, $playerThree, $position, $type, $oldScore, $players);

        $this->assertArrayHasKey('goal', $actualResult);
        $this->assertArrayHasKey('game', $actualResult);
        $this->assertArrayHasKey('player', $actualResult);

        $this->assertEquals(9, $actualResult['goal']->getGoalsTeamOne());
        $this->assertEquals(11, $actualResult['goal']->getGoalsTeamTwo());
        $this->assertEquals(9, $actualResult['game']->getGoalsTeamOne());
        $this->assertEquals(11, $actualResult['game']->getGoalsTeamTwo());
        $this->assertEquals('John', $actualResult['player']->getFirstname());
        $this->assertEquals('Doe', $actualResult['player']->getLastname());
        $this->assertNotNull($actualResult['game']->getEnd());
    }

    public function testAddOwnGoalTeamOne()
    {
        $game = new Game();
        $game->setGoalsTeamOne(8);
        $game->setGoalsTeamTwo(6);
        $goal = new Goal();
        $playerOne = new Player();
        $playerTwo = new Player();
        $playerTwo->setFirstname('John');
        $playerTwo->setLastname('Doe');
        $playerThree = new Player();
        $playerFour = new Player();
        $players = array($playerOne, $playerTwo, $playerThree, $playerFour);
        $position = Goal::POSITION_FORWARD;
        $type = Goal::TYPE_OWNGOAL;

        $teamOne = new Team();
        $teamOne->addPlayer($playerOne);
        $teamOne->addPlayer($playerTwo);
        $teamTwo = new Team();
        $teamTwo->addPlayer($playerThree);
        $teamTwo->addPlayer($playerFour);

        $expectedGoal = new Goal();
        $expectedGoal->setType($type);
        $expectedGoal->setGoalsTeamOne(8);
        $expectedGoal->setGoalsTeamTwo(7);

        $gameManagerMock = $this->getMockBuilder(GameManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $goalManagerMock = $this->getMockBuilder(GoalManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $gameManagerMock->expects($this->never())
                        ->method('end');

        $expectedScore = array(8,7);
        $goalManagerMock->expects($this->once())
                        ->method('isGoalAlreadyAdded')
                        ->with($this->equalTo($game), $this->equalTo($expectedScore))
                        ->will($this->returnValue(false));

        $gameManagerMock->expects($this->once())
                        ->method('addGoal')
                        ->with(
                            $this->identicalTo($goal),
                            $this->identicalTo($game),
                            $this->identicalTo($playerTwo),
                            $this->equalTo($position),
                            $this->equalTo($teamOne),
                            $this->equalTo($type),
                            $this->identicalTo($players)
                        )
                        ->will($this->returnValue($expectedGoal));

        $gameManagerMock->expects($this->once())
                        ->method('checkAutoEnd')
                        ->with($this->equalTo($game))
                        ->will($this->returnValue(false));

        $teamManagerMock = $this->getMockBuilder(TeamManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $teamManagerMock->expects($this->any())
                        ->method('formTeam')
                        ->will($this->onConsecutiveCalls($teamOne, $teamTwo));

        $entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('remove', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $entityManagerMock->expects($this->once())
                          ->method('flush');
        $entityManagerMock->expects($this->once())
                          ->method('persist');

        $oldScore = array(8, 6);

        $eventDispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')
                                  ->disableOriginalConstructor()
                                  ->getMock();
        
        $facade = new GameFacade($entityManagerMock, $gameManagerMock, $teamManagerMock, $goalManagerMock, $eventDispatcherMock);
        $actualResult = $facade->addGoal($goal, $game, $playerTwo, $position, $type, $oldScore, $players);

        $this->assertArrayHasKey('goal', $actualResult);
        $this->assertArrayHasKey('game', $actualResult);
        $this->assertArrayHasKey('player', $actualResult);

        $this->assertEquals(8, $actualResult['goal']->getGoalsTeamOne());
        $this->assertEquals(7, $actualResult['goal']->getGoalsTeamTwo());
        $this->assertEquals(8, $actualResult['game']->getGoalsTeamOne());
        $this->assertEquals(7, $actualResult['game']->getGoalsTeamTwo());
        $this->assertEquals('John', $actualResult['player']->getFirstname());
        $this->assertEquals('Doe', $actualResult['player']->getLastname());
        $this->assertNull($actualResult['game']->getEnd());
    }

    /**
     * @expectedException \KickFoo\Domain\Exception\GoalAlreadyAddedException
     */
    public function testAddDuplicateGoalThrowsException()
    {
        $game = new Game();
        $game->setGoalsTeamOne(8);
        $game->setGoalsTeamTwo(6);
        $goal = new Goal();
        $goal->setType(Goal::TYPE_REGULAR);
        $playerOne = new Player();
        $playerTwo = new Player();
        $playerTwo->setFirstname('John');
        $playerTwo->setLastname('Doe');
        $playerThree = new Player();
        $playerFour = new Player();
        $players = array($playerOne, $playerTwo, $playerThree, $playerFour);
        $position = Goal::POSITION_FORWARD;
        $type = Goal::TYPE_REGULAR;

        $teamOne = new Team();
        $teamOne->addPlayer($playerOne);
        $teamOne->addPlayer($playerTwo);
        $teamTwo = new Team();
        $teamTwo->addPlayer($playerThree);
        $teamTwo->addPlayer($playerFour);

        $gameManagerMock = $this->getMockBuilder(GameManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $goalManagerMock = $this->getMockBuilder(GoalManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $expectedScore = array(9, 7);
        $goalManagerMock->expects($this->once())
                        ->method('isGoalAlreadyAdded')
                        ->with($this->equalTo($game), $this->equalTo($expectedScore))
                        ->will($this->returnValue(true));

        $teamManagerMock = $this->getMockBuilder(TeamManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $teamManagerMock->expects($this->any())
                        ->method('formTeam')
                        ->will($this->onConsecutiveCalls($teamOne, $teamTwo));

        $entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('remove', 'flush'))
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $oldScore = array(8, 7);

        $eventDispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')
                                  ->disableOriginalConstructor()
                                  ->getMock();
        
        $facade = new GameFacade($entityManagerMock, $gameManagerMock, $teamManagerMock, $goalManagerMock, $eventDispatcherMock);
        $facade->addGoal($goal, $game, $playerTwo, $position, $type, $oldScore, $players);
    }
}
