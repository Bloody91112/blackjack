<?php

namespace Src\Game\Domain\Enum;

enum PlayerResult: string
{
    case Won = 'won'; // 21 > Player hand < Dealer hand
    case Lost = 'lost'; // Player hand < dealer hand
    case Bust = 'bust'; // Player hand > 21
    case Push = 'push'; // Player hand = dealer hand; Bet returned
    case Blackjack = 'blackjack'; // Player hand = 21
}
