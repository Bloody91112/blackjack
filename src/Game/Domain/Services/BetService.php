<?php

namespace Src\Game\Domain\Services;

use DomainException;
use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Enum\BetStatus;

class BetService
{
    public function calculateResult(Game $game): void
    {
        foreach ($game->players() as $player){
            $bet = $player->bet();
            $playerWinnings = match($bet->status()){
                BetStatus::Won => $bet->money()->add($bet->money()),
                BetStatus::Lost => $bet->money()->setToZero(),
                BetStatus::Push => $bet->money(),
                BetStatus::Pending => throw new DomainException("Player {$player->id()->value()} bet still pending")
            };
        }
    }
}
