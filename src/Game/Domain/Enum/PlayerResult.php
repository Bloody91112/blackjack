<?php

namespace Src\Game\Domain\Enum;

enum PlayerResult: string
{
    case Won = 'won';
    case Lost = 'lost';
    case Push = 'push';
    case Blackjack = 'blackjack';
}
