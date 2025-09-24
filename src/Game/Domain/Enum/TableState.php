<?php

namespace Src\Game\Domain\Enum;

enum TableState: string
{
    case Waiting = "waiting";
    case GameStarted = "game_started";
    case GameFinished = "game_finished";

}
