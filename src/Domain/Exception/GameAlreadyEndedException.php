<?php
namespace KickFoo\Domain\Exception;

class GameAlreadyEndedException extends \Exception
{
    protected $message = "Game already ended";
}
