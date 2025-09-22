<?php

namespace Src\Game\Domain\Entities;

use Src\Game\Domain\Enum\TableState;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\GameId;
use Src\Game\Domain\ValueObjects\Ids\HandId;
use Src\Game\Domain\ValueObjects\Ids\TableId;

class Table
{
    private ?Game $game = null;

    public function __construct(
        private TableId $id,
        private Shoe $shoe,
        private TableState $state = TableState::Waiting,
        /** @var array<Player> $players */
        private array $players = []
    ){}

    private function join(Player $player): void
    {
        if (isset($this->players[$player->id()->value()])){
            throw new \LogicException("Player " . $player->id()->value() . " already at the table");
        }
        $this->players[$player->id()->value()] = $player;
    }

    private function startGame(): void
    {
        if ($this->game !== null){
            throw new \LogicException("Game already started");
        }

        $this->game = new Game(
            GameId::generate(),
            $this->shoe,
            new Hand(HandId::generate(), new HandValue()),
            $this->players
        );
    }
}
