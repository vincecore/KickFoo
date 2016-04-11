<?php

namespace KickFoo\Infrastructure\Subscriber;

use KickFoo\Domain\Event\GameEvents;
use KickFoo\Domain\Event\GoalDeletedEvent;
use KickFoo\Domain\Event\GoalScoredEvent;
use Pusher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PusherSubscriber implements EventSubscriberInterface
{
    /**
     * @var Pusher
     */
    protected $pusher;


    public function __construct(Pusher $pusher)
    {
        $this->pusher = $pusher;
    }

    public static function getSubscribedEvents()
    {
        return array(
            GameEvents::ON_ADD_TEAM_GOAL => array('onAddTeamGoal', 0),
            GameEvents::ON_DELETE_GOAL => array('onDeleteGoal', 0),
        );
    }

    /**
     * @param GoalScoredEvent $event
     */
    public function onAddTeamGoal(GoalScoredEvent $event)
    {
        $this->pusher->trigger('kickfoo', 'addGoal', array('game' => $event->getGame(), 'goal' => $event->getGoal(), 'player' => $event->getGoal()->getPlayer()));
    }


    /**
     * @param GoalDeletedEvent $event
     */
    public function onDeleteGoal(GoalDeletedEvent $event)
    {
        $this->pusher->trigger('kickfoo', 'deleteLastGoal', array('game' => $event->getGame()));
    }
}

