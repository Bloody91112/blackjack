<?php

namespace Src\Game\Domain\Enum;

enum BetStatus: string
{
    case Pending = "pending";
    case Won = "won";
    case Lost = "lost";
    case Push = "push";
}
