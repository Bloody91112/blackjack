<?php

namespace Src\Game\Domain\Services;

use Src\Game\Domain\Entities\Game;

class ScoringService
{
    public function calculateResult(Game $game): void
    {
        while ($game->dealerScore() < 17){
            $game->placeDealerCard();
        }

        if ($game->dealerScore() < 21){
            foreach ($game->standingPlayers() as $player){
                if ($player->hand()->value()->score() < $game->dealerScore()){
                    $player->lost();
                } elseif ($player->hand()->value()->score() === $game->dealerScore()){
                    $player->push();
                } else {
                    $player->won();
                }
            }
        }

        if ($game->dealerScore() === 21){
            foreach ($game->standingPlayers() as $player){
                $player->lost();
            }
        }

        if ($game->dealerScore() > 21){
            foreach ($game->standingPlayers() as $player){
                $player->won();
            }
        }
    }
}
