<?php

namespace Src\Game\Domain\Entities;

use LogicException;
use Src\Game\Domain\Factories\GameFactory;
use Src\Game\Domain\ValueObjects\Ids\TableId;

class Table
{
    private ?Game $game = null;

    public function __construct(
        private TableId $id,
        private Shoe $shoe,
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
            throw new LogicException("Previous game is not finished.");
        }

        $this->game = (new GameFactory)->create($this->players, $this->shoe);
        $this->game->betStart();
    }

    private function finishGame(): void
    {
        $this->game->finish();
        $this->shoe = $this->game->shoe();
    }
}
