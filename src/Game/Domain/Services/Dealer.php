<?php

namespace Src\Game\Domain\Services;


use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\HandId;

class Dealer
{

    public function dealInitialCards(Game $game): void
    {
        foreach ($game->players() as $player){
            $player->assignHand(new Hand(HandId::generate(), new HandValue()));
            $hand = $player->hand();
            $firstCard = $game->shoe()->draw();
            $hand->receiveCard($firstCard);

            $secondCard = $game->shoe()->draw();
            $hand->receiveCard($secondCard);
        }

        $game->dealerHand()->receiveCard($game->shoe()->draw());
        $game->playersTurnsStage();
    }

}
