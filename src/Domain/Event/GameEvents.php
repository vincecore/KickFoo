<?php

namespace KickFoo\Domain\Event;

final class GameEvents
{
    const ON_ADD_TEAM_GOAL = 'kickfoo.event.add_team_goal';
    const ON_CLAIM_GOAL = 'kickfoo.event.claim_goal';
    const ON_SWITCH = 'kickfoo.event.switch';
    const ON_GAME_START = 'kickfoo.event.start_game';
    const ON_GAME_END = 'kickfoo.event.end_game';
    const ON_DELETE_GOAL = 'kickfoo.event.delete_goal';
}
