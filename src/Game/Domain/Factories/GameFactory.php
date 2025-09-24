<?php

namespace Src\Game\Domain\Factories;

use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\Entities\Shoe;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\GameId;
use Src\Game\Domain\ValueObjects\Ids\HandId;

class GameFactory
{
    public function __construct(){}

    public function create(array $players, Shoe $shoe): Game
    {
        $dealerHand = new Hand(HandId::generate(), new HandValue());
        return new Game(GameId::generate(), $shoe, $dealerHand, $players);
    }
}
