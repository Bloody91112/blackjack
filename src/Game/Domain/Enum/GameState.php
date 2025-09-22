<?php

namespace Src\Game\Domain\Enum;

enum GameState: string
{
    case Created = "created";
    case Betting = "betting";
    case Dealing = "dealing";
    case PlayersTurn = "players_turn";
    case DealerTurn = "dealer_turn";
    case Settling  = "settling";
    case Finished = "finished";
}
