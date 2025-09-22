<?php

namespace Src\Game\Domain\Entities;

use LogicException;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\ValueObjects\Ids\GameId;

class Game
{
    private GameState $state;

    public function __construct(
        private GameId $id,
        private Shoe $shoe,
        private Hand $dealerHand,
        private array $players,
    ){
        if (empty($this->players)){
            throw new LogicException("Cant create a game. There are no players to play.");
        }

        $this->state = GameState::Created;
    }

    public function state(): GameState
    {
        return $this->state;
    }

    public function shoe(): Shoe
    {
        return $this->shoe;
    }

    public function players(): array
    {
        return $this->players;
    }

    public function id(): GameId
    {
        return $this->id;
    }

}
