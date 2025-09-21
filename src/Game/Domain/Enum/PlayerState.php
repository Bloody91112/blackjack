<?php

namespace Src\Game\Domain\Enum;

enum PlayerState: string
{
    case Watching = "watching";
    case Active = 'active';
    case Standing = 'standing';
    case Busted = 'busted';
    case Finished = 'finished';
}
