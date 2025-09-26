<?php

namespace Src\Game\Domain\Services;

use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Entities\Player;

class ScoringService
{
    public function calculateResult(Game $game): void
    {
        while ($game->dealerScore() < 17){
            $game->placeDealerCard();
        }

        if ($game->dealerScore() < 21){
            foreach ($game->standingPlayers() as $player){
                /** @var Player $player */
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
