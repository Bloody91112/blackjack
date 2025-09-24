<?php

namespace Src\Game\Domain\Entities;

use LogicException;
use Src\Game\Domain\Enum\TableState;
use Src\Game\Domain\Factories\GameFactory;
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

    private function join(Player $newPlayer): void
    {
        foreach ($this->players as $player){
            if ($player->id()->equals($newPlayer->id())){
                throw new LogicException("Player " . $player->id()->value() . " already at the table");
            }
        }
        $newPlayer->joinTable();
        $this->players[] = $newPlayer;
    }

    private function startGame(): void
    {
        if ($this->game !== null){
            throw new LogicException("Game already started");
        }

        $this->state = TableState::GameStarted;
        $this->game = (new GameFactory)->create($this->players, $this->shoe);
        $this->game->betStage();
    }
}
