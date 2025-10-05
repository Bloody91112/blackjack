<?php

namespace Src\Game\Domain\Services;

use Src\Game\Domain\Entities\Game;

class ScoringService
{
    public function calculateResult(Game $game): void
    {
        if ($game->dealerScore() < 21){
            foreach ($game->standingPlayers() as $player){
                if ($player->hand()->value()->score() < $game->dealerScore()){
                    $player->lose();
                } elseif ($player->hand()->value()->score() === $game->dealerScore()){
                    $player->push();
                } else {
                    $player->win();
                }
            }
        }

        if ($game->dealerScore() === 21){
            foreach ($game->standingPlayers() as $player){
                $player->lose();
            }
        }

        if ($game->dealerScore() > 21){
            foreach ($game->standingPlayers() as $player){
                $player->win();
            }
        }
    }
}
