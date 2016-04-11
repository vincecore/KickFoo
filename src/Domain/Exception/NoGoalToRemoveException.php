<?php
namespace KickFoo\Domain\Exception;

class NoGoalToRemoveException extends \Exception
{
    protected $message = "No goal to remove";
}
