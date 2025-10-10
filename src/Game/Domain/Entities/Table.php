<?php

namespace Src\Game\Domain\Entities;

use DomainException;
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

    public function join(Player $newPlayer): void
    {
        foreach ($this->players as $player){
            if ($player->id()->equals($newPlayer->id())){
                throw new LogicException("Player " . $player->id()->value() . " already at the table");
            }
        }

        if (count($this->players) === 8){
            throw new DomainException("Maximum players at the table");
        }

        $newPlayer->joinTable();
        $this->players[] = $newPlayer;
    }

    public function startGame(): void
    {
        if ($this->game !== null){
            throw new LogicException("Previous game is not finished.");
        }

        $this->game = (new GameFactory)->create($this->players, $this->shoe);
        $this->game->betStart();
    }

    public function finishGame(): void
    {
        if ($this->game === null){
            throw new LogicException("There is no game to finish.");
        }

        $this->game->finish();
        $this->shoe = $this->game->shoe();
    }

    /** @return array<Player> */
    public function players(): array
    {
        return $this->players;
    }

    public function game(): ?Game
    {
        return $this->game;
    }
}
