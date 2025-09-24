<?php

namespace Src\Game\Domain\Enum;

enum PlayerState: string
{
    case Free = "free"; // Choosing a table
    case SittingAtTheTable = "sitting_at_the_table"; // At the table. Waiting a game.
    case ChoosingABet = "choosing_a_bet"; // Joined the game. Choosing a bet
    case PlacedABet = 'placed_a_bet';
    case Active = 'active'; // Make his turn;
    case Standing = 'standing'; // Did turn and stand
    case Busted = 'busted'; // Lose, more than 21
    case Finished = 'finished'; // Ended the game
}
