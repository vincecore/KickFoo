<?php
namespace KickFoo\Domain\Exception;

class GoalAlreadyAddedException extends \Exception
{
    protected $message = "Goal already added";
}
