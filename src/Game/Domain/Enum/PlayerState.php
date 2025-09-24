<?php

namespace Src\Game\Domain\Enum;

enum PlayerState: string
{
    case Watching = "watching";
    case JoinedTheGame = "joined_the_game";
    case ChoosingABet = "choosing_a_bet";
    case PlacedABet = 'placed_a_bet';
    case Active = 'active';
    case Standing = 'standing';
    case Busted = 'busted';
    case Finished = 'finished';
}
